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
   * @var Validator  Static Instance
   */
  public static $validator;

  /**
   * Form Name/Identifer
   */
  protected $name;

  /**
   * Form data
   * @var array
   */
  protected $data;

  // -----------------------------------------------------------

  /**
   * Constructor
   *
   * @param string $name
   * @param Validator $validator
   */
  public function __construct($name = 'form', Validator $validator = NULL) {

    //Inject static validator dependency
    if ($validator) {
      self::$validator = $validator;
    }

    //Set name
    $this->name = $name;

    //Initialize data
    $this->data = new \stdClass();
  }

  // -----------------------------------------------------------

  public function __set($item, $val) {

    if ($val instanceOf Fields\Abstracts\Field) {
      $this->data->$item = $val;
    }
    else {
      throw new \InvalidArgumentException("$item is not a valid Fieldtype object!");
    }

  }

  // -----------------------------------------------------------

  public function __get($item) {

    if ($item == 'name') {
      return $this->name;
    }
    else {
      return $this->data->$item;
    }
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
   * Return the form as a JSON object
   *
   * @return string
   */
  public function toJson() {

  }

  // -----------------------------------------------------------

  /**
   * Render the form
   *
   * @param string|boolean|null $action
   * If FALSE, will not print the form header.  If NULL, the action will be blank.  Or, put a string in.
   *
   * @param string $method
   * No validation here, but it should be GET or POST (or lowercase get/post)
   *
   * @param array $attrs
   * Key/value attributes in addition to 'action' and 'method' (optional)
   *
   * @return string
   */
  public function render($action = NULL, $method = 'POST', $attrs = array()) {

    $hasFiles = FALSE;

    //Add children
    $html = '';
    foreach($this->data as $obj) {

      $html .= $obj->asHtml();

      if ($obj instanceof Fields\File) {
        $hasFiles = TRUE;
      }

    }

    //Add <form>...</form> tags
    if ($action !== FALSE) {

      $attributes = array();
      $attributes['action'] = $action ?: '';
      $attributes['method'] = strtolower($method ?: 'POST');
      $attributes['enctype'] = ($hasFiles) ? 'multipart/form-data' : 'application/x-www-form-urlencoded';

      $attrs = array_merge($attributes, $attrs);
      foreach($attrs as $key => &$val) {
        $val = "$key='$val'";
      }
      $attrs = implode(' ', $attrs);

      $html = "<form {$attrs}>" . $html . "</form>";
    }

    return $html;
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

    if ( ! isset($this->validator)) {
      throw new RuntimeException("No validator set!");
    }

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

    if ( ! isset($this->validator)) {
      throw new RuntimeException("No validator set!");
    }

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