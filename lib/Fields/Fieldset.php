<?php

namespace Formula\Fields;

class Fieldset extends Abstracts\Field {

  /**
   * @var array
   */
  protected $children = array();

    protected function render() {
    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;

    $html = "<fieldset " . $this->renderAttrs($attrs) . ">";

    if ($this->label) {
      $html .= "<legend>" . $this->label . "</legend>";
    }

    foreach($this->children as $child) {
      $html .= $child->asHtml();
    }
    $html .= "</fieldset>";

    return $html;
   }

  /**
   * Magic Method
   */
  public function __set($name, $val) {

    if (is_object($val) && $val instanceOf Abstracts\Field) {

      if (isset($_POST[$name])) {
        $val->setData($name, $_POST[$name]);
      }

      if ( ! isset($val->name)) {
        $val->name = $name;
      }
    }

    if (isset($this->$name)) {
      $this->$name == $val;
    }
    elseif ($name == 'legend') {
       $this->label = $val;
    }
    elseif ($val instanceOf Abstracts\Field) {
      $this->children[$name] = $val;
    }
    else {
      throw new \InvalidArgumentException("Invalid field set for fieldset");
    }
  }
}

/* EOF: Fieldset.php */