<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Builder extends Jelly_Core_Builder {

	public function __call($method, $args)
	{
		if (preg_match('/^by_(.*)/', $method, $matches))
		{
			return (isset($args[0])) ? $this->where($matches[1], '=', $args[0]) : $this;
		}

		return parent::__call($method, $args);
	}

	public function select_one($throw = TRUE)
	{
		$record = $this->limit(1)->select();

		if ( ! $record->loaded() && $throw)
		{
			throw new HTTP_Exception_404(':model looked in query ":query" was not found in the database.', array(
				':model' => ucfirst(Inflector::humanize($this->_model)),
				':query' => Database::instance()->last_query,
			));
		}

		return $record;
	}

	public function paginate(Pagination $pagination)
	{
		return $this
			->limit($pagination->items_per_page)
			->offset($pagination->offset);
	}

}
