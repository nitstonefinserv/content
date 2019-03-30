<?php namespace Reflexions\Content\Admin;

use Carbon\Carbon;
use Content;
use View;
use Illuminate\Support\Facades\Input;

use Reflexions\Content\Admin\Form\Field;
use Reflexions\Content\Admin\Form\FieldSet;
use Reflexions\Content\Admin\Form\FieldDataProvider;
use Reflexions\Content\Admin\Form\Widget;

/**
 * Fluent API to declare form fields supported by the Reflexions\Content admin layout
 */
class Form
{
    const OPTIONS_VALIDATION = 'validation';
    const OPTIONS_NULLABLE = 'nullable';
    const OPTIONS_DATE_MUTATE_TO_CARBON = 'date_mutate_to_carbon';
    const OPTIONS_DATE_MUTATE_FORMAT = 'date_mutate_format';

    protected $model;
    protected $fields = [];

    /**
     * @param \Eloquent $model Eloquent model to be edited
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @return array Return array of field views for display on edit page
     */
    public function getFieldViews()
    {
        $views = [];
        foreach ($this->fields as $f) {
            $views = array_merge($views, $f->getViews());
        }
        return array_filter($views);
    }

    /**
     * @return array Return array of field names
     */
    public function getFieldAttributes()
    {
        $attributes = [];
        foreach ($this->fields as $f) {
            $attributes = array_merge($attributes, $f->getAttributes());
        }
        return array_filter($attributes);
    }

    /**
     * @return array Return array of field Rules
     */
    public function getFieldRules()
    {
        $rules = [];
        foreach ($this->fields as $f) {
            $rules = array_merge($rules, $f->getRules());
        }
        return array_filter($rules);
    }

    /**
     * @return array Return array of field Mutators
     */
    public function getFieldMutators()
    {
        $mutators = [];
        foreach ($this->fields as $f) {
            $mutators = array_merge($mutators, $f->getMutators());
        }
        return array_filter($mutators);
    }

    /**
     * @return array Return array of field Mutators
     */
    public function getSavedHandlers()
    {
        $saved_handlers = [];
        foreach ($this->fields as $f) {
            $saved_handlers = array_merge($saved_handlers, $f->getSavedHandlers());
        }
        return array_filter($saved_handlers);
    }

    /**
     * Add FieldDataProvider to the form
     * @param \Reflexions\Content\Admin\Form\FieldDataProvider $provider
     * @return static
     */
    public function addField(FieldDataProvider $provider)
    {
        $this->fields[] = $provider;
        return $this;
    }

    /**
     * Add a field to the form.
     * @param string $attribute Name of the field attribute
     * @param string $rule Laravel validation rule for the field
     * @param \Illuminate\Contracts\View\View $field View to output field widget
     * @param callable $mutator Mutator for the field
     * @return static
     */
    public function addSingleField($attribute, $rule, \Illuminate\Contracts\View\View $view, callable $mutator = null)
    {
        return $this->addField(new Field($attribute, $rule, $view, $mutator));
    }

    /**
     * Add a fieldset to the form.
     * @param array $attributes Array of the field attribute names
     * @param array $rules Array of the Laravel validation rules
     * @param array $views Array of the widget views
     * @param array $mutators Array of the field attribute mutators
     */
    public function addFieldSet($attributes, $rules, $views, $mutators = null)
    {
        return $this->addField(new FieldSet($attributes, $rules, $views, $mutators));
    }

    /**
     * Merge default options into options array
     * @param array $options
     * @return array
     */
    public static function defaults($options = [], $defaults = [])
    {
        $defaults = array_merge(
            [
                'sizing' => 'col-sm-10',
                self::OPTIONS_VALIDATION => null,
            ], $defaults
        );
        return array_merge($defaults, $options);
    }

    /**
     * Add a text field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return static
     */
    public function text($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getTextWidget($attribute, $label, $options)
        );
    }

    /**
     * Add a number field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return static
     */
    public function number($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getNumberWidget($attribute, $label, $options)
        );
    }

    /**
     * Add a integer field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return static
     */
    public function integer($attribute, $label, $options = [])
    {
        $options['attributes']['step'] = 1;
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getNumberWidget($attribute, $label, $options),
            function ($value)
            {
                // cast to int datatype, or null if blank
                return is_null($value) || $value === '' ? null : intval($value);
            }
        );
    }

    /**
     * Add a password field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return static
     */
    public function password($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        $PASSWORD_PLACEHOLDER = Http\Controllers\AdminController::PASSWORD_PLACEHOLDER;
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getPasswordWidget($attribute, $label, $options),
            function ($value) use ($PASSWORD_PLACEHOLDER) {
                return $value != $PASSWORD_PLACEHOLDER
                    ? bcrypt($value)
                    : $value;
            }
        );
    }

    /**
     * Add a datetime field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return static
     */
    public function datetime($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getDateTimeWidget($attribute, $label, $options),
            function ($value) use ($options) {
                if ($value === ' ')
                {
                    // both date and time were blank.
                    // The space character appears from the concat of date + ' ' + time, when date & time are blank
                    return !empty($options[self::OPTIONS_NULLABLE])
                        ? null
                        : '';
                }

                // If this field isDateAttribute(), then you can let the model handle the formatting
                if( !empty($options[self::OPTIONS_DATE_MUTATE_TO_CARBON]) )
                {
                    return new Carbon($value);
                }

                $format = isset($options[self::OPTIONS_DATE_MUTATE_FORMAT])
                    ? $options[self::OPTIONS_DATE_MUTATE_FORMAT]
                    : 'Y-m-d H:i:s';
                return Carbon::createFromFormat($format, $value);
            }
        );
    }

    /**
     * Add a date field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return static
     */
    public function date($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getDateWidget($attribute, $label, $options),
            function ($value) use ($options) {
                if (!$value)
                {
                    return !empty($options[self::OPTIONS_NULLABLE])
                        ? null
                        : '';
                }

                // If this field isDateAttribute(), then you can let the model handle the formatting
                if( !empty($options[self::OPTIONS_DATE_MUTATE_TO_CARBON]) )
                {
                    return new Carbon($value);
                }

                $format = isset($options[self::OPTIONS_DATE_MUTATE_FORMAT])
                    ? $options[self::OPTIONS_DATE_MUTATE_FORMAT]
                    : 'Y-m-d';
                return Carbon::createFromFormat($format, $value);
            }
        );
    }

    /**
     * Add a select field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $values Values for dropdown e.g. ['key' => 'value']
     * @param array $options Additional options
     * @return static
     */
    public function select($attribute, $label, $values = [], $options = [])
    {
        $options = static::defaults($options, ['sizing' => 'col-sm-2']);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getSelectWidget($attribute, $label, $values, $options)
        );
    }

    /**
     * Add value keys to value array
     */
    public function addValueKeys($values)
    {
        $new = [];
        foreach ($values as $v) {
            $new[$v] = $v;
        }
        return $new;
    }

    /**
     * Add a textarea to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return static
     */
    public function textarea($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getTextareaWidget($attribute, $label, $options)
        );
    }

    /**
     * Adds an hr to the form to allow grouping of content
     *
     * @return static
     */
    public function hr()
    {
        return $this->addSingleField(
            '',
            '',
            Widget::getHr()
        );
    }

    /**
     * Add a multiple select field to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $values Values for dropdown e.g. ['key' => 'value']
     * @param array $selected Values
     * @param array $options Additional options
     * @return static
     */
    public function multi_select($attribute, $label, $values = [], $selected = [], $options = [])
    {
        if (!Input::has($attribute)) Input::merge([$attribute => []]);
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getMultiSelectWidget($attribute, $label, $values, $selected, $options),
            function ($value) {
                return json_encode($value);
            }
        );
    }


    /**
     * Add a checkbox to the form
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param boolean $value if the box should be checked or not
     * @param array $options Additional options
     * @return static
     */
    public function checkbox($attribute, $label, $value, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getCheckboxWidget($attribute, $label, $value, $options),
            function ($value)
            {
                return (bool) $value;
            }
        );
    }

    /**
     * Add a hidden input to the form
     * @param string $attribute Field name
     * @param boolean $value if the box should be checked or not
     * @param array $options Additional options
     * @return static
     */
    public function hidden($attribute, $value=null, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getHiddenWidget($attribute, $value, $options)
        );
    }

    /**
     * Add a static content field, pulled from the model by attribute name
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return static
     */
    public function staticContent($attribute, $label, $options = [])
    {
        $options = static::defaults($options);
        return $this->addSingleField(
            $attribute,
            $options[self::OPTIONS_VALIDATION],
            Widget::getStaticContentWidget($attribute, $label, $options)
        );
    }

    /**
     * Adds an p to the form to allow layout inline
     *
     * @return static
     */
    public function p($innerHTML, $options = [])
    {
        return $this->addSingleField(
            '',
            '',
            Widget::getPWidget($innerHTML, $options)
        );
    }

    /**
     * Adds an div to the form to allow layout inline
     *
     * @return static
     */
    public function div($innerHTML, $options = [])
    {
        return $this->addSingleField(
            '',
            '',
            Widget::getDivWidget($innerHTML, $options)
        );
    }
}
