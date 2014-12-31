Silverstripe Form extras module
==================

Utilities to get better forms with Silverstripe

New fields
==================

MiniColorsField
------------------

A field that uses the "minicolors" jquery plugin to pick a color. Its best
to store the color using the DBColor field instead of a regular Varchar(7).
You can set the theme using the MiniColorsField::setTheme method

BaseUploadField
------------------

An UploadField with a factory method to get an upload field with a folder name
already set depending on the context. This context takes into account the
current Subsite by default if the module is enabled.

ImageUploadField
------------------

An UploadField already set up for image upload

Sample usage:

	$fields->replaceField('Logo', ImageUploadField::createForClass($this, 'Logo'));

Recommended modules
==================
Works best with
- Zenvalidator : https://github.com/sheadawson/silverstripe-zenvalidator.git
- Display Logic : https://github.com/unclecheese/silverstripe-display-logic

Compatibility
==================
Tested with 3.1

Maintainer
==================
LeKoala - thomas@lekoala.be