<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Behavior_Translatable extends Jelly_Behavior {

	protected static $_langs = array();
	public $_fields = array();

	public function meta_before_finalize($meta)
	{
		// Find and gather all translatable fields in model
		foreach ($meta->fields() as $name => $field)
		{
			if (isset($field->translate) && $field->translate)
			{
				// Remember translatable fields
				$this->_fields[] = $name;
			}
		}

		// Load and remember all languages if model is translatable
		if ( ! empty($this->_fields) && empty(self::$_langs))
		{
			$langs = Kohana::$config->load('pudding')->languages;

			foreach ($langs as $lang_code => $lang_name)
			{
				self::$_langs[$lang_code] = $lang_name;
			}
		}

		// Cerate temporary translatable field
		foreach ($meta->fields() as $name => $field)
		{
			if (in_array($name, $this->_fields))
			{
				// Setting main translation field as not in database
				$field->in_db = FALSE;

				// Creating mock fields for each lang
				foreach (self::$_langs as $lang_code => $lang_name)
				{
					$new_field = clone $field;
					$new_field->label .= ' ('.$lang_name.')';

					$meta->fields(array(
						$name.'_'.$lang_code => $new_field,
					));
				}

				// Clearing rules for main field
				$field->rules = array();
			}
		}
	}

	public function model_call_clear_translated_fields($model)
	{
		foreach ($model->meta()->fields() as $name => $field)
		{
			if (in_array($name, $this->_fields))
			{
				// Clearing rules for main field added after before_finalize (File/Image field)
				$field->rules = array();
			}
		}
	}

	public function model_call_load_translations($model)
	{
		// Stop if in model there's no translatable fields
		if (empty($this->_fields))
		{
			return FALSE;
		}

		$translations = DB::select()
			->from($model->meta()->table().'_i18n')
			->where('record_id', '=', $model->id())
			->as_object()
			->execute();

		foreach ($translations as $translation)
		{
			foreach ($this->_fields as $field)
			{
				$model->{$field.'_'.$translation->lang_code} = $translation->{$field};

				if ($translation->lang_code == I18n::lang())
				{
					$model->{$field} = $translation->{$field};
				}
			}
		}
	}

	public function model_after_save($model)
	{
		$updated_i18n = array();

		foreach ($this->_fields as $field)
		{
			foreach (self::$_langs as $lang_code => $lang_name)
			{
				if ($model->meta()->field($field) instanceof Jelly_Field_File)
				{
					$updated_i18n[$lang_code][$field] = $model->meta()->field($field.'_'.$lang_code)->save($model, $model->{$field.'_'.$lang_code}, $model->id());
				}
				else
				{
					$updated_i18n[$lang_code][$field] = $model->{$field.'_'.$lang_code};
				}
			}
		}

		foreach ($updated_i18n as $lang => $pairs)
		{
			$exists = (bool) DB::select()
				->from($model->meta()->table().'_i18n')
				->where('lang_code', '=', $lang)
				->where('record_id', '=', $model->id())
				->execute()
				->count();

			if ($exists)
			{
				DB::update($model->meta()->table().'_i18n')
					->where('lang_code', '=', $lang)
					->where('record_id', '=', $model->id())
					->set($pairs)
					->execute();
			}
			else
			{
				$values = array_merge(
					array('lang_code' => $lang, 'record_id' => $model->id()),
					$updated_i18n[$lang]
				);

				DB::insert($model->meta()->table().'_i18n', array_keys($values))
					->values($values)
					->execute();
			}
		}
	}

	public function model_before_delete($model)
	{
		parent::model_before_delete($model);

		DB::delete($model->meta()->table().'_i18n')
			->where('record_id', '=', $model->id())
			->execute();
	}

}