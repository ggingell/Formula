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

  public $classes = '';

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
   * @return string
   */
  public function render() {

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
    return "<fieldset>" . $legend . $html . "</fieldset>";
  }

  // -----------------------------------------------------------

  /**
   * Alias for render() to make the interface more consistent
   *
   * @return string
   */
  public function asHtml() {
    return $this->render();
  }

}

/* EOF: Fieldset.php */