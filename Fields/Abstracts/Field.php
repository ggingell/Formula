<?php

namespace Formula\Fields\Abstracts;

abstract class Field {

  /**
   * @var string
   */
  public $name;

  /**
   * @var string
   */
  public $label;

  /**
   * @var string
   */
  public $attrs;

  /**
   * @var string
   */
  public $classes;

  /**
   * @var string
   */
  public $before;

  /**
   * @var string
   */
  public $after;

  /**
   * @var string
   */
  public $description;

  /**
   * Automatically identified
   * @var type
   */
  public $type;

  /**
   * Automatically identified
   * @var string
   */
  public $id;

  /**
   * Form ID
   * @var string
   */
  public $formId = 'form';

	// -----------------------------------------------------------

  /**
   * Data for rendering the field
   * @var array|string
   */
  protected $_data = NULL;

	// -----------------------------------------------------------

  /**
   * Constructor
   *
   * @param array|string $data  Typically, existing
   */
  public function __construct($name, $data = NULL, $formId = 'form') {

    $this->type    = strtolower(get_called_class());
    $this->name    = $name;
    $this->_data   = $data;
    $this->formId = $formId;

    $this->id = $formId . '_' . $name;
  }

  // ------------------------------------------------------------

  //Render Method Returns a String of HTML
  abstract protected function render();

  // ------------------------------------------------------------

  /**
   * Render the field as HTML
   *
   * @param array|string $classes
   * Optional classes to apply to the container object
   *
   * @return string
   */
  public function asHtml($classes = NULL) {

    $html = $this->render();

    //Before and after HTML
    $before = $this->before ? "<span class='before'>$this->before</span>" : NULL;
    $after = $this->after ? "<span class='after'>$this->after</span>" : NULL;

    if ( ! is_array($classes)) {
      $classes = (is_null($classes)) ? array() : explode(' ', $classes);
    }

    //Add the default class
    $classes[] = "{$this->formId}_{$this->name}";

    //Render!
    $html = $before . $html . $after;
    $html = "<div class='" . implode(' ', $classes) . "'>" . $html . "</div>";
    return $html;
  }

  // ------------------------------------------------------------

  /**
   * Render the field as JSON
   *
   * @return string
   */
  public function asJson() {

    $obj = clone $this;
    unset($obj->_data);
    return $obj;

  }

  // ------------------------------------------------------------

  /**
   * Alias for 'as_json'
   *
   * @return string
   */
  public function __toString() {

    return $this->as_json();

  }

  // ------------------------------------------------------------

  /**
   * Get the attributes as an array if they are not already
   *
   * @return array
   */
  protected function getAttrs() {

    return ( ! is_array($this->attrs)) ? explode(" ", $this->attrs) : $this->attrs;

  }

  // ------------------------------------------------------------

  protected function renderAttrs($attrs) {

    if (is_array($attrs)) {
      $attrs = array_filter($attrs);
      foreach($attrs as $k => &$v) {
        $v = "$k='$v'";
      }
      unset($v);

      $attrs = implode(' ', $attrs);
    }

    return $attrs;
  }

}

/* EOF: Field.php */
