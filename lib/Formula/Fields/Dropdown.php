<?php

/**
 * @file Dropdown Field Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

namespace Formula\Fields;

class Dropdown extends Abstracts\MultipleChoiceInput {

  // -----------------------------------------------------------

  /**
   * Overrides parent::getDataKeys().
   *
   * @return array
   */
  public function getDataKeys() {
    return array_merge(parent::getDataKeys(), array($this->name . '_other_input'));
  }

  // -----------------------------------------------------------

  /**
   * Overrides parent::getDataKeys().
   *
   * @return string
   */
  public function getData() {
    if ($this->allowOther && ! in_array($this->_data[$this->name], $this->options)) {
      return $this->_data[$this->name . '_other_input'];
    }
    else {
      return parent::getData();
    }
  }

  // -----------------------------------------------------------

  protected function render() {

    // Add special class if needed
    $this->classes .= ($this->allowOther) ? ' has_other' : '';

    $attrs = $this->getAttrs();
    $attrs['class'] = trim($this->classes);
    $attrs['name'] = $this->name;
    $attrs['id'] = $this->id;


    //Determine the checked value, if possible
    if ($this->_data) {
      $value = $this->_data[$this->name];
    }
    elseif ($this->defaultValue) {
      $value = $this->defaultValue;
    }
    else {
      $value = NULL;
    }

    //Flag
    $itemSelected = FALSE;

    //Options values discovered
    $optionsValuesDiscovered = array();

    //Add the options
    $optionsArray = array();
    if (is_array($this->options)) {

      foreach($this->options as $key => $val) {

        $currval  = ($this->useValues) ? $val : $key;
        $selected = (strcmp($value, $currval) == 0) ? "selected='selected'" : NULL;
        $itemSelected = (boolean) $selected;

        if (is_array($val)) {

          foreach($val as $k => $v) {
            $currval  = ($this->useValues) ? $v : $k;
            $selected = (strcmp($val, $currval) == 0) ? "selected='selected'" : NULL;
            $subOptArray[] = "<option id='{$this->name}_{$k}' value='{$currval}' {$selected}>{$val}</option>";
            $optionsValuesDiscovered[] = $currval;
          }

          $optionsArray[] = "<optgroup id='{$this->name}_{$key}'>" . implode("\n", $subOptArray) . "</optgroup>";
        }
        else {
          $optionsValuesDiscovered[] = $currval;
          $optionsArray[] = "<option id='{$this->name}_{$key}' value='{$currval}' {$selected}>{$val}</option>";
        }
      }
    }

    $optionsHtml = implode("\n", $optionsArray);

    //Add 'other' logic if enabled
    $otherHtml = '';
    if ($this->allowOther) {

      //If no data, see if the default value is not in the array of discovered options
      if (empty($this->_data) && $this->defaultValue) {
        $checked = ( ! in_array($this->defaultValue, $optionsValuesDiscovered));
        $value = $this->defaultValue;
      }
      else {
        $checked = ($value == '_other') ? "checked='checked'" : NULL;
        $value = $this->_data[$this->name . '_other_input'];
      }

      if ($this->otherLabel) {
        $otherHtml .= sprintf("<label class='dropdown_other_label' for='%s_other_input'>%s</label>", $this->id, $this->otherLabel);
      }

      $otherHtml .= "<input type='text' class='dropdown_other' id='{$this->id}__other_input' name='{$this->name}_other_input' value='{$value}' placeholder='{$this->otherPlaceholder}' />";
    }

    $labelHtml = ($this->label) ? sprintf(self::$labelHtml, $this->id, $this->label) : NULL;
    $optionsHtml = $labelHtml . "<select " . $this->renderAttrs($attrs) . ">{$optionsHtml}</select>" . $otherHtml;

    return $optionsHtml;
  }

}

/* EOF: Dropdown.php */