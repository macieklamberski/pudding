<?php defined('SYSPATH') or die('No direct script access.');

abstract class Jelly_Core_Field_Datetime extends Jelly_Field {

	/**
	 * @var  string  a date formula representing the time in the database
	 */
	public $format = 'Y-m-d H:i:s';

} // End Jelly_Core_Field_Datetime