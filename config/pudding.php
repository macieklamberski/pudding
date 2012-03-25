<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	/**
	 * Languages for which content will be stored in database. Each item in array
	 * should have specified language code (as key) and full name (as value).
	 */
	'languages' => array(
		'en' => 'English',
		'pl' => 'Polski',
	),
	'behaviors' => array('dependable', 'sluggable', 'translatable'),
);