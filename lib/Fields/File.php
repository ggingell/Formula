<?php

/**
 * @file File Field Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

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
      $attrs['value'] = $this->_data[$this->name];

    $label_html = ($this->label) ? sprintf(self::$labelHtml, $this->id, $this->label) : NULL;
    $item_html = "<input ". $this->renderAttrs($attrs) ." />";

    return "$label_html $item_html";
  }

}

/* EOF: File.php */