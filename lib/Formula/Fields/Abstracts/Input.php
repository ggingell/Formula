<?php

namespace Formula\Fields\Abstracts;

abstract class Input extends Field {

  public static $errorMsgHtml           = "<span class='input_error_msg'>%s</span>";
  public static $labelHtml              = "<label for='%s'>%s</label>";
  public static $inputErrorClassName    = 'input_error';

  // -----------------------------------------------------------

  /**
   * @var string  An optional alternative label to use for validation
   */
  public $validationLabel = NULL;

  /**
   * @var string
   */
  public $defaultValue;

  /**
   * @var array
   */
  public $validationErrors = array();

  /**
   * @var boolean
   */
  public $refill = TRUE;

  /**
   * @var array|string  Validation Rules
   */
  public $validation = '';

  /**
   * @var boolean
   */
  public $renderValidationErrors = TRUE;

  // -----------------------------------------------------------

  /**
   * Data for rendering the field
   * @var array
   */
  protected $_data = NULL;

  // -----------------------------------------------------------

  /**
   * Get the values in $_REQUEST or $_POST or $_GET that this field expects
   *
   * @return array
   */
  public function getDataKeys() {
    return array($this->name);
  }

  // -----------------------------------------------------------

  /**
   * Set the data for this field input
   *
   * @param mixed $data
   */
  public function setData($key, $data) {

    if ( ! is_array($this->_data)) {
      $this->_data = array();
    }

    $this->_data[$key] = $data;
  }

  // -----------------------------------------------------------

  /**
   * Get the data for this field input
   */
  public function getData() {
    return (is_array($this->_data) && isset($this->_data[$this->name]))
      ? $this->_data[$this->name] : NULL;
  }

  // -----------------------------------------------------------

  /**
   * Override the HTML renderer to include error messages for inputs
   *
   * @return string
   */
  public function asHtml($classes = NULL) {

    if (count($this->validationErrors) > 0 && $this->renderValidationErrors) {
      $class = self::$inputErrorClassName;

      $valErrorHtml = "";
      foreach($this->validationErrors as $error) {
        $valErrorHtml .= sprintf(self::$errorMsgHtml, $error);
      }

      $this->after = $valErrorHtml . $this->after;
    }
    else {
      $class = NULL;
    }

    return parent::asHtml($class);
  }
}

/* EOF: Input.php */
