<?php

namespace Formula\Fields;

class Button extends Abstracts\Input {

  /**
   * @var string
   */
  public $title = 'Button';

  /**
   * @var string
   */
  public $buttonType = 'button';

  // -----------------------------------------------------------

  protected function render() {

    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = $this->buttonType;
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;

    if ($this->defaultValue)
      $attrs['value'] = $this->defaultValue;
    if ($this->_data)
      $attrs['value'] = $this->_data[$this->name];

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