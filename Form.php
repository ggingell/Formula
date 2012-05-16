<?php

namespace Formula;

/**
 * Form Class for Rendering and Validating
 *
 * @package Formula
 * @author Casey McLaughlin  
 */
class Form {
  
  const AS_ARRAY = 1;
  const AS_ARRAY_BY_FIELD = 2;
  const AS_STRING = 3;
  
  /**
   * Form Name/Identifer
   */
  public $name;
  
  /**
   * Form data
   * @var array 
   */
  public $data;
  
  /**
   * Validator
   * @var Validator
   */
  private $validator;
  
	// -----------------------------------------------------------
  
  public function __construct(Validator $validator, $name = 'form') {

    //Inject validator dependency
    $this->validator = $validator;

    //Set name
    $this->name = $name;
  }
  
	// -----------------------------------------------------------
  
  /**
   * Return the form as a JSON object
   * 
   * @return string 
   */
  public function toJson() {
    
  }
  
	// -----------------------------------------------------------
  
  /**
   * Magic Method to get the JSON for the form
   * 
   * @return string 
   */
  public function __toString() {
    
    return $this->toJson();
    
  }
  
	// -----------------------------------------------------------
  
  /**
   * Render the form
   * 
   * @return string 
   */
  public function render() {
    
  }
  
	// -----------------------------------------------------------
  
  /**
   * Validate a single field or the entire form
   * 
   * @param string $fieldName   If NULL, the entire form will be validated
   * @param int $returnValidationMsgs  Set to return validation messages
   * @return boolean
   */
  public function validate($fieldName = NULL, $returnValidationMsgs = FALSE) {
    
    if ($fieldName && ! isset($this->data[$fieldName]))
      throw new Exception("The Field $fieldName is not defined and cannot be valdiated.");
    
    $toValidate = ($fieldName) ? array($this->data['fieldName']) : $this->data;
    
    //Flag - TRUE until we run across a bad field
    $result = TRUE;
    
    foreach($toValidate as $fname => $fdata) {

      //Get the custom validation settings for the field type
      //and add those to the custom field validation rules

      //Run all validations     
      
    }
    
    return ($returnValidationMsgs) ? $this->getValidationMessages($returnValidationMsgs) : $result;
  }
  
  // -----------------------------------------------------------
  
  /**
   * Get validation messages
   * 
   * Returns NULL or EMPTY array if no validation has yet occured.
   * 
   * @param int $format
   * @return string|array
   */
  public function getValidationMessages($format = self::AS_ARRAY) {
    
    switch($format) {
      
      case self::AS_ARRAY:
        //do stuff
      break;
    
    
      case self::AS_ARRAY_BY_FIELD:
        //do stuff
      break;
    
    
      case self::AS_STRING:
      default:
        //do stuff
      break;      
    }
    
    return $msgs;    
  }
  
}

/* EOF: Form.php */