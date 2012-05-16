<?php

namespace Formula\Fields;

class File extends Abstracts\Input {

 protected function render() {
    
    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = 'file';
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;
    
    if ($this->defaultValue)
      $attrs['value'] = $this->defaultValue;
    if ($this->_data)
      $attrs['value'] = $this->_data;
         
    $label_html = "<label for='{$this->id}'>{$this->label}</label>";
    $item_html = "<input ". $this->renderAttrs($attrs) ." />";
    
    return "$label_html $item_html";
  }  

}

/* EOF: File.php */