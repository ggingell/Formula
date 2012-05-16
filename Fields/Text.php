<?php

namespace Formula\Fields;

class Text extends Abstracts\OpenEndedInput {
  
  protected function render() {
    
    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = 'text';
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;
    
    if ($this->defaultValue)
      $attrs['value'] = $this->defaultValue;
    if ($this->_data)
      $attrs['value'] = $this->_data;
    
    if ($this->placeholder)
      $attrs['placeholder'] = $this->placeholder;
         
    $label_html = "<label for='{$this->id}'>{$this->label}</label>";
    $item_html = "<input ". $this->renderAttrs($attrs) ." />";
    
    return "$label_html $item_html";
  }
  
}

/* EOF: Text.php */