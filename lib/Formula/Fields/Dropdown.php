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

    $attrs = $this->getAttrs();
    $attrs['class'] = trim($this->classes);
    $attrs['name'] = $this->name;
    $attrs['id'] = $this->id;

    //Determine the checked value, if possible
    if ($this->_data) {
      $val = $this->_data[$this->name];
    }
    elseif ($this->defaultValue) {
      $val = $this->defaultValue;
    }
    else {
      $val = NULL;
    }

    //Flag
    $itemSelected = FALSE;

    //Add the options
    $optionsArray = array();
    if (is_array($this->options)) {

      foreach($this->options as $key => $val) {

        $currval  = ($this->useValues) ? $val : $key;
        $selected = (strcmp($val, $currval) == 0) ? "selected='selected'" : NULL;
        $itemSelected = (boolean) $selected;

        if (is_array($val)) {

          $currval  = ($this->useValues) ? $v : $k;
          $selected = (strcmp($val, $currval) == 0) ? "selected='selected'" : NULL;

          foreach($val as $k => $v) {
            $subOptArray[] = "<option id='{$this->name}_{$k}' value='{$currval}' {$selected}>{$val}</option>";
          }

          $optionsArray[] = "<optgroup id='{$this->name}_{$key}'>" . implode("\n", $subOptArray) . "</optgroup>";
        }
        else {
          $optionsArray[] = "<option id='{$this->name}_{$key}' value='{$currval}' {$selected}>{$val}</option>";
        }
      }
    }

    $optionsHtml = implode("\n", $optionsArray);

    //Add 'other' logic if enabled
    $otherHtml = '';
    if ($this->allowOther) {

      // Add special class
      $this->classes .= ' has_other';

      $checked = ($val == '_other') ? "checked='checked'" : NULL;
      $val = $this->_data[$this->name . '_other_input'];

      if ($this->otherLabel) {
        $otherHtml .= sprintf("<label class='dropdown_other_label' for='%s_other_input'>%s</label>", $this->id, $this->otherLabel);
      }
      $otherHtml .= "<input type='text' class='dropdown_other' id='{$this->id}__other_input' name='{$this->name}_other_input' value='{$val}' placeholder='{$this->otherPlaceholder}' />";
    }

    $labelHtml = ($this->label) ? sprintf(self::$labelHtml, $this->id, $this->label) : NULL;
    $optionsHtml = $labelHtml . "<select " . $this->renderAttrs($attrs) . ">{$optionsHtml}</select>" . $otherHtml;

    return $optionsHtml;
  }

}

/* EOF: Dropdown.php */