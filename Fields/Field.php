<?php

namespace Formula\Fields;

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
   * @var Validator
   */
  private $validator;  
  
	// -----------------------------------------------------------
    
  public function __construct(Validator $validator) {
    
    $this->type = strtolower(__CLASS__);
    $this->validator = $validator;    
  }
   
}

/* EOF: Field.php */
