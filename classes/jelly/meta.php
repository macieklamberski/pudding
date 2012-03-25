<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Meta extends Jelly_Core_Meta {

	public function finalize($model)
	{
		$behaviors = Kohana::$config->load('pudding')->behaviors;

		// Include all Pudding behaviors
		foreach ($behaviors as $behavior)
		{
			$this->behaviors(array($behavior => Jelly::behavior($behavior)));
		}

		parent::finalize($model);
	}

}