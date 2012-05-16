<?php

namespace Formula\Fields\Abstracts;

abstract class Input extends Field {
  
  /**
   * @var string
   */
  public $validationRules;
  
  /**
   * @var string
   */
  public $defaultValue;

  // -----------------------------------------------------------

  /**
   * Get Validation Rules
   * 
   * @throws RuntimeException 
   * @return array;
   */
  public function getValidationRules() {
 
    return $this->validationRules();
  }
}

/* EOF: Input.php */
