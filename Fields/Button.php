<?php

namespace Formula\Fields;

class Button extends Abstracts\Input {

  public $title = 'Button';

  public $buttonType = 'button';

  protected function render() {
    
    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = $this->buttonType;
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;
    
    if ($this->defaultValue)
      $attrs['value'] = $this->defaultValue;
    if ($this->_data)
      $attrs['value'] = $this->_data;
    
    if ($this->label) {     
      $label_html = "<label for='{$this->id}'>{$this->label}</label>";
    }
    else {
      $label_html = '';
    }

    $item_html = "<button ". $this->renderAttrs($attrs) .">" . $this->title . "</button>";
    
    return "$label_html $item_html";
  }  


}

/* EOF: Button.php */