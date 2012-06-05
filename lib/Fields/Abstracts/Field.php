<?php

namespace Formula\Fields\Abstracts;

abstract class Field {

  public static $beforeWrapperHtml      = "<span class='before'>%s</span>";      //after code
  public static $descriptionWrapperHtml = "<span class='description'>%s</span>"; //description code
  public static $afterWrapperHtml       = "<span class='after'>%s</span>";       //before code
  public static $fieldWrapperHtml       = "<div class='%s'>%s</div>";            //classes, and then inner HTML

  // -----------------------------------------------------------

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
   * Constructor
   *
   * @param string $name
   * @param string $formId
   */
  public function __construct($name, $formId = 'form') {

    $this->type    = strtolower(get_called_class());
    $this->name    = $name;
    $this->formId = $formId;

    //Set the id
    $this->id = $formId . '_' . $this->name;
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

    if ( ! $this->name) {
      throw new \RuntimeException("Cannot render a field that does not have a name!");
    }

    $html = $this->render();

    //Before and after HTML
    $before      = $this->before ? sprintf(self::$beforeWrapperHtml, $this->before) : NULL;
    $after       = $this->after ? sprintf(self::$afterWrapperHtml, $this->after) : NULL;
    $description = $this->description ? sprintf(self::$descriptionWrapperHtml, $this->description) : NULL;

    if ( ! is_array($classes)) {
      $classes = (is_null($classes)) ? array() : explode(' ', $classes);
    }

    //Add the default class
    $classes[] = "{$this->formId}_{$this->name}";

    //Render!
    $html = $before . $html . $after . $description;
    $html = sprintf(self::$fieldWrapperHtml, implode(' ', $classes), $html);
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
