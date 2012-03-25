# Pudding

Pudding is an extension to popular ORM for Kohana - [Jelly](http://github.com/creatoro/jelly). It extends Jelly in the form of custom behaviors with many useful features:
* More *Rails-ish* deletion of relationship-dependent records and files,
* Few handy methods for query builder,
* Ability to generate text for `Jelly_Field_Slug` field from content of other field,
* Datetime field type.

## Features

### Ruby on Rails-like deletion of dependent records and files

You can already configure fields of Jelly model to delete all related records and files (eg. images associated with `Jelly_Field_Image`) when it is deleted. There's one drawback of this mechanism, though. It works only in case of deleting single model instance. So while removing collection (`Jelly_Collection`) of records that match a given condition, related data will not be deleted along with it. In order to do this, you need write your own loop and delete each record there. **Pudding** comes with more handy solution and detects whether there are set any dependencies and if so, deletes records in loop automaticaly.

You need to remember though, that in case of the loop solution, each of records will be deleted separately, which means separate SQL query. Keep that in mind when removing a larger number of records at a time.

To enable dependents deletion for relations and files use flag `dependent` set to `TRUE`:

	'image' => Jelly::field('image', array(
		'path'      => 'path/to/images',
		'dependent' => TRUE,
		...
	)),

And in case of relations:

	'comments' => Jelly::field('hasmany', array(
		'dependent' => TRUE,
		...
	)),

### Generating slug from other field in model

Pudding makes it possible to automatically generate text for `Jelly_Field_Slug` field from content of other field, when the model is saved in database. To set from which field slug is to be generated, use `source` property setting source field name as value:

	'title' => Jelly::field('string'),
	'slug'  => Jelly::field('slug', array(
		'source' => 'title',
	)),

### Datetime field type

Maybe it's not a big deal, but I found it frustrating to have to set `'format' => 'Y-m-d H:i:s'` for each declared timestamp field, which is to be store in database as DATETIME. Now, such fields can be defined much simpler:

	'created_at' => Jelly::field('datetime'),

Datetime field type inherits from `Jelly_Field_Timestamp`, so every other properties works here as well.

### Query builder methods

Pudding extends default Jelly query builder with few handy methods:

* `->by_field_name('value')` - which is the shortcut form of `->where('field_name', '=', 'value')`;
* `->find()` - similary to Ruby on Rails, this method returns single record matching given conditions. If there's no record found, `HTTP_Exception_404` exception is thrown;
* `->paginate($pagination_object)` - to shorten amount of code written to paginate records, you can use this method passing previously defined Pagination object. Method requires Pagination object as its paramter.

## More yummy?

There are other features which could be added to Pudding. One of them is **polymorphic relationships**. But that requires significant amount of time to develop, and for now remains a dream.