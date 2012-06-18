<?php

/**
 * @file Fieldset Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

namespace Formula;

class Fieldset extends Form {

  protected $legend = '';

  protected $val;

  public $renderValidationErrors = NULL;

  // -----------------------------------------------------------

  public function __construct($name, Validator $val) {

    $this->val = $val;
  	$this->name = $name;
  	$this->data = new \stdClass();
  }

  // -----------------------------------------------------------

  public function setLegend($legend) {
    $this->legend = $legend;
  }

  // -----------------------------------------------------------

  /**
   * Render the fieldset
   *
   * @param $attrs
   * @return string
   */
  public function asHtml($attrs = array()) {

    $html = '';

    //Add children
    foreach($this->data as $obj) {

      //Turn off individual validation errors in output?
      if ( ! $this->renderIndividualErrors && isset($obj->renderValidationErrors)) {
        $obj->renderValidationErrors = FALSE;
      }

      $html .= $obj->asHtml();

      if ($obj instanceof Fields\File) {
        $hasFiles = TRUE;
      }
    }

    $legend = ($this->legend) ? sprintf("<legend>%s</legend>", $this->legend) : '';
    $attrs  = ( ! empty($attrs)) ? ' ' . $this->prepAttrs($attrs) : '';

    return "<fieldset{$attrs}>" . $legend . $html . "</fieldset>";
  }

  // -----------------------------------------------------------

  /**
   * Render is a shortcut for asHtml, and it ignores the $action and $method
   * attributes
   */
  public function render($action = NULL, $method = 'POST', $attrs = array()) {
    return $this->asHtml($attrs);
  }

  // -----------------------------------------------------------

  public function prepAttrs($attrs) {

    $outArr = array();
    foreach($attrs as $k => $v) {
      $outArr[] = "$k='$v'";
    }
    return implode(' ', $outArr);
  }

}

/* EOF: Fieldset.php */