<?php

/**
 * @file Hidden Field Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

namespace Formula\Fields;

class Hidden extends Abstracts\Input {

 protected function render() {

    $attrs = $this->getAttrs();
    $attrs['name'] = $this->name;
    $attrs['type'] = 'hidden';
    $attrs['class'] = $this->classes;
    $attrs['id'] = $this->id;

    if ($this->defaultValue)
      $attrs['value'] = $this->defaultValue;
    if ($this->_data)
      $attrs['value'] = $this->_data[$this->name];

    return "<input ". $this->renderAttrs($attrs) ." />";
  }

}

/* EOF: Hidden.php */