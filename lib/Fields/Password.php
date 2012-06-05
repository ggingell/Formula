<?php

namespace Formula\Fields;

class Password extends Abstracts\OpenEndedInput {

  /**
   * @var boolean
   */
  public $refill = FALSE;

  // -----------------------------------------------------------

  protected function render() {

    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = 'password';
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;

    if ($this->defaultValue)
      $attrs['value'] = $this->defaultValue;
    if ($this->_data && $this->refill)
      $attrs['value'] = $this->_data[$this->name];

    $label_html = ($this->label) ? sprintf(self::$labelHtml, $this->id, $this->label) : NULL;
    $item_html = "<input ". $this->renderAttrs($attrs) ." />";

    return "$label_html $item_html";
  }

}

/* EOF: Password.php */