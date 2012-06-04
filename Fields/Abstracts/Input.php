<?php

namespace Formula\Fields\Abstracts;

abstract class Input extends Field {

  /**
   * @var array  Validation Rules
   */
  public $validation = array();

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
   * @var boolean
   */
  public $renderValidationErrors = TRUE;

  // -----------------------------------------------------------

  /**
   * Data for rendering the field
   * @var array|string
   */
  protected $_data = NULL;

  // -----------------------------------------------------------

  /**
   * Set the data for this field input
   *
   * @param mixed $data
   */
  public function setData($data) {
    $this->_data = $data;
  }

  // -----------------------------------------------------------

  /**
   * Override the HTML renderer to include error messages for inputs
   *
   * @return string
   */
  public function asHtml() {

    if (count($this->validationErrors) > 0 && $this->renderValidationErrors) {
      $class = 'input_error';

      $valErrorHtml = "";
      foreach($this->validationErrors as $error) {
        $valErrorHtml .= "<span class='input_error_msg'>{$error}</span>";
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
