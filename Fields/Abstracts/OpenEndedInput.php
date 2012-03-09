<?php

namespace Formula\Fields\Abstracts;

abstract class OpenEndedInput extends Input {
  
  /**
   * @var string
   */
  public $validation_rules;
  
  /**
   * @var string
   */
  public $default_value;
  
  /**
   * @var string 
   */
  public $placeholder;

}

/* EOF: Input.php */
