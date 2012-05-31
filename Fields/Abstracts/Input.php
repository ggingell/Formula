<?php

namespace Formula\Fields\Abstracts;

abstract class Input extends Field {

  /**
   * @var array
   */
  public $validationRules = array();

  /**
   * @var string
   */
  public $defaultValue;

  /**
   * @var array
   */
  public $validationErrors = array();

  // -----------------------------------------------------------

  /**
   * Override the HTML renderer to include error messages for inputs
   *
   * @return string
   */
  public function asHtml() {

    if (count($this->validationErrors) > 0) {
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

  // -----------------------------------------------------------

  /**
   * Get Validation Rules
   *
   * @throws RuntimeException
   * @return array;
   */
  public function getValidationRules() {

    return $this->validationRules;
  }
}

/* EOF: Input.php */
