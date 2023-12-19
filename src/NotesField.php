<?php

namespace Outl1ne\NovaNotesField;

use Laravel\Nova\Fields\Field;

class NotesField extends Field
{
    public $component = 'nova-notes-field';

    public $showOnCreation = false;

    public $showOnIndex = false;

    /**
     * NovaNotesField constructor.
     *
     * @param  null  $attribute
     */
    public function __construct($name, $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'placeholder' => null,
            'addingNotesEnabled' => true,
            'displayNotableInfo' => false,
            'fullWidth' => config('nova-notes-field.full_width_inputs', false),
        ]);
    }

    /**
     * Sets the placeholder value displayed on the field.
     *
     * @param  string  $placeholder
     * @return NotesField
     **/
    public function placeholder($placeholder)
    {
        return $this->withMeta(['placeholder' => $placeholder]);
    }

    /**
     * Show or hide the AddNote input.
     *
     * @param  bool  $addingNotesEnabled
     * @return NotesField
     */
    public function addingNotesEnabled($addingNotesEnabled = true)
    {
        return $this->withMeta(['addingNotesEnabled' => $addingNotesEnabled]);
    }

    public function displayNotableInfo($displayNotableInfo = false)
    {
        return $this->withMeta(['displayNotableInfo' => $displayNotableInfo]);
    }

    /**
     * Show or hide the AddNote input.
     *
     * @param  bool  $fullWidth
     * @return NotesField
     **/
    public function fullWidth($fullWidth = true)
    {
        return $this->withMeta(['fullWidth' => $fullWidth]);
    }
}
