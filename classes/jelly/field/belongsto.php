<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Field_BelongsTo extends Jelly_Core_Field_BelongsTo {

	/**
	 * @var  int  default to 0 for no relationship
	 */
	public $default = NULL;

	/**
	 * @var  boolean  null values are not allowed, 0 represents no record
	 */
	public $allow_null = TRUE;

	/**
	 * @var  boolean  empty values are converted to the default
	 */
	public $convert_empty = TRUE;

	/**
	 * @var  int  empty values are converted to 0, not NULL
	 */
	public $empty_value = NULL;

	/**
	 * @var  string  a string pointing to the foreign model and (optionally, a
	 *               field, column, or meta-alias)
	 */
	public $foreign = '';

}