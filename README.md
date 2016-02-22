Silverstripe Form extras module
==================

Utilities to get better forms with Silverstripe

Handling jquery, mousewheel and gestures
==================

We try to favor jquery.mousewheel and hammer as librairies to handle mousewheel
and gestures. The idea is to try to avoid include other librairies that would
do the job twice, leading to duplicated code and extra load time.

You can use the following helper to load these libraries in your own project:

	FormExtraJquery::include_jquery();
	FormExtraJquery::include_jquery_ui();
	FormExtraJquery::include_hammer();
	FormExtraJquery::include_mousewheel();

Base form class
==================

New functionnalities
------------------

The FormExtra class comes with some simple but nice functionnalities.

- It will save and restore the data if the validation fails
- It provides some response shortcuts (like return $this->err('Failed!') )

There are also two new actions:
- FormActionArrow: a form action with an arrow (simple :-) )
- FormActionConfirm: a form action with a confirm text (really, just on onclick="confirm...")

Multi steps forms
------------------

The FormExtraMulti class implements multi steps functionnality.

Each step is represented by a single "Form" object. Classes must be named
in a sequential manner (Like MyFormStep1, MyFormStep2 ...) because the class
name is used to determine which is the current step, the next step etc...

Actions are defined by calling

	$actions = $this->definePrevNextActions()

This will create two actions: doPrev and doNext. doPrev will not be set on the first
step while doNext will be translated differently on the last step.

By default, clicking on these buttons will simply save the data to the session and
navigate into the form. It's up to you to override these methods to achieve what you
want to do.

A template is also provided to add steps at the top of your forms. This template
consumes the AllSteps method on the form that returns all steps with their properties.

NOTE: don't forget that validation will happen if you go back (doPrev) in the form.
This might not be a desirable behaviour. If used in conjonction with ZenValidator,
the FormActionNoValidation will be used to make sure that the validation does not
happen when going back (while still saving the current state, allowing the user
to freely navigate without losing data).

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
Otherwise the tendancy is to put everything in the "Uploads" folder which become
quite unmanageable after some time.

ImageUploadField
------------------

An UploadField already set up for image upload

Sample usage:

	$fields->replaceField('Logo', ImageUploadField::createForClass($this, 'Logo'));

FrontendUploadField
------------------

An UploadField ready to be used on the frontend. To allow editing on the frontend,
make sure that you have a canEditFrontend method that returns true (according to whatever
your security settings are).

This also adds a "Gallery" functionnality.
Supports for "cropbox", "focuspoint" as image resize modules.

BetterCheckboxField
------------------

The default checkbox field in Silverstripe doesn't send any value if not checked.
This one does!

BirthDateField
------------------

If you are like me and that your customers ask you to split the birthdate field in three...

ChosenField
------------------

A dropdown field integrated with Chosen
http://harvesthq.github.io/chosen/

CmsInlineFormAction
------------------

As you may have noticed, the InlineFormAction class doesn't work quite well.
This alternative is bare bones but does the job.

ComboField
------------------

A dropdown where values can be appended by the user.

MaskedInputField
------------------

A field that use Inputmask
https://github.com/RobinHerbots/jquery.inputmask

PostcodeField
------------------

A field that validates postcodes according to a country.

RegexTextField
------------------

A field that validates against a specific regex

Select2Field
------------------

A field that uses Select2. Version 3 and 4 are supported.
https://select2.github.io/

SexyPasswordField
------------------

A field that validates password according to your Member::password_validator()

SliderField
------------------

A field that uses jQuery ui slider

TableField
------------------

A field that supports multiple columns and unlimited rows. Data is stored as json.

SimpleTinyMceField
------------------

A simple TinyMceField plugin for frontend use

CropboxField
------------------

A field that uses CropboxField

GridField
==================

GridFieldConfig_RecordDefault
------------------

A default config that uses GridFieldSortableRows, GridFieldOrderableRows and GridFieldBulkManager

GridFieldConfig_RelationDefault
------------------

A default config that uses GridFieldSortableRows, GridFieldOrderableRows and GridFieldBulkManager

GridFieldDownloadButton
------------------

WIP

GridFieldExportAllButton
------------------

The default export to csv button export only the current list, not all items.
While this is nice in a way, sometimes your clients want to have a full export.
Simple use this button to achieve this.

NOTE : to make your csv files open nicely on excel, simple use:

	$btn->setCsvSeparator(';');


HasOne helper
==================

Sometimes, you want to add a button to edit a HasOne relationship or even embed all the fields
right inside a tab.

HasOneButtonField
------------------

Simple add the button as any other field:

	new HasOneButtonField($name,$title,$this);

Embed all fields
------------------

Thanks to the HasOneEditDataObjectExtension, all fields named like Relation:Name or
Relation/Name will load and save data from and to the Relation.

	new TextField('MyRelation:MyField','My field name')

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