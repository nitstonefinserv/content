<?php
namespace Reflexions\Content\Admin\Form;

use View;
use Content;
use Reflexions\Content\Admin\Http\Controllers\AdminController;

class Widget {
    /**
     * Get view for text widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getTextWidget($attribute, $label, $options)
    {
        return View::make(
            Content::package().'::admin.components.text',
            compact('attribute', 'label', 'options')
        );
    }
    /**
     * Get view for number widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getNumberWidget($attribute, $label, $options)
    {
        return View::make(
            Content::package().'::admin.components.number',
            compact('attribute', 'label', 'options')
        );
    }

    /**
     * Get view for password widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getPasswordWidget($attribute, $label, $options)
    {
        $PASSWORD_PLACEHOLDER = AdminController::PASSWORD_PLACEHOLDER;
        return View::make(
            Content::package().'::admin.components.password',
            compact('attribute', 'label', 'options', 'PASSWORD_PLACEHOLDER')
        );
    }

    /**
     * Get DateTime Widget 
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getDateTimeWidget($attribute, $label, $options)
    {
        return View::make(
            Content::package().'::admin.components.datetime',
            compact('attribute', 'label', 'options')
        );
    }

    /**
     * Get Date Widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getDateWidget($attribute, $label, $options)
    {
        return View::make(
            Content::package().'::admin.components.date',
            compact('attribute', 'label', 'options')
        );
    }

    /**
     * Get Select Widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $values Values for dropdown e.g. ['key' => 'value']
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getSelectWidget($attribute, $label, $values, $options)
    {
        return View::make(
            Content::package().'::admin.components.select',
            compact('attribute', 'label', 'values', 'options')
        );
    }

    /**
     * Get Textarea Widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getTextareaWidget($attribute, $label, $options)
    {
        return View::make(
            Content::package().'::admin.components.textarea',
            compact('attribute', 'label', 'options')
        );
    }


    /**
     * Get Hr
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public static function getHr()
    {
        return View::make(
            Content::package().'::admin.components.hr'
        );
    }

    /**
     * Get Multi Select Widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $values Values for dropdown e.g. ['key' => 'value']
     * @param array $selected Values
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getMultiSelectWidget($attribute, $label, $values, $selected, $options)
    {
        return View::make(
            Content::package().'::admin.components.multi-select',
            compact('attribute', 'label', 'values', 'selected', 'options')
        );
    }


    /**
     * Get Checkbox Widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getCheckboxWidget($attribute, $label, $value, $options)
    {
        return View::make(
            Content::package().'::admin.components.checkbox',
            compact('attribute', 'label', 'value', 'options')
        );
    }

    /**
     * Get Hidden Widget
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getHiddenWidget($attribute, $value, $options)
    {
        return View::make(
            Content::package().'::admin.components.hidden',
            compact('attribute', 'value', 'options')
        );
    }

    /**
     * Get Static Contetn Widget. Display static content loaded from the model
     * @param string $attribute Field name
     * @param string $label Label displayed to the user
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getStaticContentWidget($attribute, $label, $options)
    {
        return View::make(
            Content::package().'::admin.components.static-content',
            compact('attribute', 'label', 'options')
        );
    }

    /**
     * Get P Widget. Display a p tag inline with a form
     * @param string $innerHTML HTMLContent
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getPWidget($innerHTML, $options)
    {
        return View::make(
            Content::package().'::admin.components.p',
            compact('innerHTML', 'options')
        );
    }

    /**
     * Get P Widget. Display a div tag inline with a form
     * @param string $innerHTML HTMLContent
     * @param array $options Additional options
     * @return \Illuminate\Contracts\View\View
     */
    public static function getDivWidget($innerHTML, $options)
    {
        return View::make(
            Content::package().'::admin.components.div',
            compact('innerHTML', 'options')
        );
    }
}
