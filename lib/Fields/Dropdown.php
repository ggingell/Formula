<?php

namespace Formula\Fields;

class Dropdown extends Abstracts\MultipleChoiceInput {

  protected function render() {

    $attrs = $this->getAttrs();
    $attrs['class'] = $this->classes;
    $attrs['name'] = $this->name;
    $attrs['id'] = $this->id;

    //Determine the checked value, if possible
    if ($this->_data) {
      $val = $this->_data;
    }
    elseif ($this->defaultValue) {
      $val = $this->defaultValue;
    }
    else {
      $val = NULL;
    }

    //Flag
    $optchecked = FALSE;

    //Output the HTML

    $optionsHtml = ($this->label) ? "<label class='multchoice_label'>{$this->label}</label>" : '';
    if (is_array($this->options)) {
      foreach($this->options as $optkey => $optvalue) {

        $currval = ($this->useValues) ? $optvalue : $optkey;
        $checked = (strcmp($val, $currval) == 0) ? "checked='checked'" : NULL;
        $optchecked = TRUE;

        $optHtml = '';
        $optHtml .= "<input type='radio' name='{$this->name}' id='{$this->id}_{$optkey}' value='{$currval}' {$checked} />";
        $optHtml .= "<label class='radio_label' for='{$this->id}_{$optkey}'>{$optvalue}</label>";
        $optionsHtml .= "<span class='radio_opt'>{$optHtml}</span>";
      }
    }

    //Add 'other' logic if enabled
    if ($this->allowOther) {

      $checked = ($val && ! $optchecked) ? "checked='checked'" : NULL;
      $currval = ($val && ! $optchecked) ? $val : NULL;

      $optHtml = '';
      $optHtml .= "<input type='radio' name='{$this->name}' id='{$this->id}__other' value='_other' {$checked} />";
      $optHtml .= "<label class='radio_label' for='{$this->id}__other'>{$this->otherLabel}</label>";
      $optHtml .= "<input type='text' class='radio_other' id='{$this->id}__other_input' name='{$this->name}_other_input' value='{$currval}' />";
      $optionsHtml .= "<span class='radio_opt'>{$optHtml}</span>";
    }

    $optionsHtml = "<div " . $this->renderAttrs($attrs) . ">{$optionsHtml}</div>";

    return $optionsHtml;
  }

}

/* EOF: Dropdown.php */