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
  
  public function __construct(Validator $validator) {
    
    $this->validator = $validator;
    
  }
  
	// -----------------------------------------------------------
  
  /**
   * Return the form as a JSON object
   * 
   * @return string 
   */
  public function to_json() {
    
  }
  
	// -----------------------------------------------------------
  
  /**
   * Magic Method to get the JSON for the form
   * 
   * @return string 
   */
  public function __toString() {
    
    return $this->to_json();
    
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
   * @param string $field_name   If NULL, the entire form will be validated
   * @param int $return_validation_messages  Set to return validation messages
   * @return boolean
   */
  public function validate($field_name = NULL, $return_validation_msgs = FALSE) {
    
    if ($field_name && ! isset($this->data[$field_name]))
      throw new Exception("The Field $field_name is not defined and cannot be valdiated.");
    
    $to_validate = ($field_name) ? array($this->data['field_name']) : $this->data;
    
    //Flag - TRUE until we run across a bad field
    $result = TRUE;
    
    foreach($to_validate as $fname => $fdata) {

      //Get the custom validation settings for the field type
      //and add those to the custom field validation rules

      //Run all validations     
      
    }
    
    return ($return_validation_msgs) ? $this->get_validation_messages($return_validation_msgs) : $result;
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
  public function get_validation_messages($format = self::AS_ARRAY) {
    
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