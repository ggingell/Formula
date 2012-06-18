<?php

/**
 * @file Fieldset Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

namespace Formula;

class Fieldset extends Form {

  /**
   * @var string
   */
  public $legend = '';

  /**
   * @var string|array
   */
  public $classes = '';

  /**
   * @var Validation
   */
  protected $val;

  /**
   * @var array
   */
  protected $attributes = array();

  /**
   * @var boolean
   */
  protected $renderValidationErrors = TRUE;

  // -----------------------------------------------------------

  /**
   * Constructor
   *
   * @param string $name
   * @param Validator $val
   * @param string $parentName
   */
  public function __construct($name, Validator $val, $parentName = '') {

    $this->val = $val;
  	$this->name = $name;
    $this->attributes['id'] = $parentName . '_' . $name; //Unique name
  	$this->data = new \stdClass();
  }

  // -----------------------------------------------------------

  /**
   * Set the legend
   *
   * @param string $legend
   */
  public function setLegend($legend) {
    $this->legend = $legend;
  }

  // -----------------------------------------------------------

  /**
   * Magic method - return the renderValidationErrors property
   */
  public function __get($item) {

    if ('renderValidationErrors' == $item) {
      return $this->renderValidationErrors;
    }

    return parent::__get($item);
  }

  // -----------------------------------------------------------

  /**
   * Render the fieldset
   *
   * @param array $attrs
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
    }

    $legend = ($this->legend) ? sprintf("<legend>%s</legend>", $this->legend) : '';
    return sprintf("<fieldset%s>%s%s</fieldset>", ' ' . $this->prepAttrs($attrs), $legend, $html);
  }

  // -----------------------------------------------------------

  /**
   * Render is a shortcut for asHtml, and it ignores the $action and $method
   * attributes
   *
   * @param NULL $action  Ignored
   * @param NULL $method  Ignored
   * @param array $attrs  Array
   * @return html
   */
  public function render($action = NULL, $method = NULL, $attrs = array()) {
    return $this->asHtml($attrs);
  }

  // -----------------------------------------------------------

  /**
   * Prep the attributes for inclusion in the output string
   *
   * Compiles the classes, attributes, and any custom attributes
   * into a string
   *
   * @param array $attrs
   * @return string
   */
  private function prepAttrs($attrs = array()) {

    //Prep the classes
    $classes = $this->classes;
    if ( ! is_array($classes)) {
      $classes = (empty($classes)) ? array() : explode(' ', $classes);
    }

    $attrs = array_merge($this->attributes, $attrs);

    if ( ! empty($classes)) {
      $attrs['class'] = implode(' ', $classes);
    }

    $outArr = array();
    foreach($attrs as $k => $v) {
      $outArr[] = "$k='$v'";
    }
    return implode(' ', $outArr);
  }

}

/* EOF: Fieldset.php */