<?php

namespace Formula\Fields;

class Textbox extends Abstracts\OpenEndedInput {
  
  protected function render() {
    
    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = 'text';
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;
    
    if ($this->defaultValue)
      $tfValue = $this->defaultValue;
    if ($this->_data)
      $tfValue = $this->_data;
    else
      $tfValue = '';
    
    if ($this->placeholder)
      $attrs['placeholder'] = $this->placeholder;
         
    $label_html = "<label for='{$this->id}'>{$this->label}</label>";
    $item_html = "<textarea ". $this->renderAttrs($attrs) .">" . $tfValue . "</textarea>";
    
    return "$label_html $item_html";
  }
  
}

/* EOF: Text.php */