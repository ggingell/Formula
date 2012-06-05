<?php

namespace Formula;

/**
 * Form Class for Rendering and Validating
 *
 * @package Formula
 * @author Casey McLaughlin
 */
class Form implements \IteratorAggregate {

  const AS_ARRAY = 1;
  const AS_ARRAY_BY_FIELD = 2;
  const AS_STRING = 3;

  /**
   * @var Validator  Static Instance
   */
  public static $validator;

  /**
   * @var Validator
   */
  protected $val;

  /**
   * Form Name/Identifer
   */
  protected $name;

  /**
   * Individual Errors
   */
  protected $renderIndividualErrors = TRUE;

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
    if ( ! $validator) {
      $this->val = $validator ?: clone self::$validator;
    }

    //Set name
    $this->name = $name;

    //Initialize data
    $this->data = new \stdClass();
  }

  // -----------------------------------------------------------

  /**
   * Add a field to the form
   *
   * @param string $fieldname
   * @param string $type
   */
  public function addField($fieldname, $type) {

    $className = "\\Formula\\Fields\\" . ucfirst($type);
    $this->data->$fieldname = new $className($fieldname, $this->name);

    //Set the POST data
    if ($this->data->$fieldname instanceOf Fields\Abstracts\Input) {

      foreach($this->data->$fieldname->getDataKeys() as $datakey) {

        if (isset($_POST[$datakey])) {
          $this->data->$fieldname->setData($datakey, $_POST[$datakey]);
        }
      }
    }
  }

  // -----------------------------------------------------------

  /**
   * Get Magic Method
   *
   * @param string $item
   * @return $mixed
   */
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
   * Iterator Interface for the Form
   */
  public function getIterator() {
    return new ArrayObject($this->data);
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

    $out = new \stdClass();
    $out->name = $this->name;
    $out->fields = $this->data;
    return json_encode($out);
  }

  // -----------------------------------------------------------

  /**
   * Turn individual error output on or off in the HTML of the form
   *
   * @param boolean $val
   */
  public function renderIndividualErrors($val) {
    $this->renderIndividualErrors = (boolean) $val;
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

    //Add hidden properties -- @TODO: Add optional CSRF as value
    $html = "<input type='hidden' name='{$this->name}_token' id='{$this->name}_token' value='submitted' />";

    //Add children
    foreach($this->data as $obj) {

      //Turn off individual validation errors in output?
      if ( ! $this->renderIndividualErrors && isset($obj->renderValidationErrors)) {
        $obj->renderValidationErrors = FALSE;
      }

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
   * @param string $fieldName
   * If NULL, the entire form will be validated
   *
   * @return boolean
   */
  public function validate($fieldName = NULL) {

    if ( ! isset($this->val)) {
      throw new \RuntimeException("No validator set!");
    }

    if ($fieldName && ! isset($this->data[$fieldName]))
      throw new \Exception("The Field $fieldName is not defined and cannot be validated.");

    $toValidate = ($fieldName) ? array($this->data['fieldName']) : $this->data;

    //Flag - TRUE until we run across a bad field
    $result = TRUE;

    //Add all fields to validation object
    foreach($toValidate as $fkey => $field) {

      //Ignore form submitted CSRF hidden
      if (strcmp($this->name . '_token', $fkey) == 0) {
        continue;
      }

      if ($field instanceof Fields\Abstracts\Input) {
        $valLabel = $field->validationLabel ?: $field->label;
        $this->val->setRules($field->name, $field->getData(), $valLabel, $field->validation);
      }
    }

    //Run it!
    $result = $this->val->run();
    $valErrors = $this->val->getErrorMessages();

    //Update the field validation errors
    foreach($toValidate as $fkey => $field) {
      if (isset($valErrors[$field->name])) {

        $this->data->$fkey->validationErrors = $valErrors[$field->name];

      }
    }

    return $result;
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

    if ( ! isset($this->val)) {
      throw new RuntimeException("No validator set!");
    }

    switch($format) {

      case self::AS_ARRAY:
        return $this->val->getErrorMessages(TRUE);
      break;


      case self::AS_ARRAY_BY_FIELD:
        return $this->val->getErrorMessages(FALSE);
      break;


      case self::AS_STRING:
      default:

        $msgs = array();
        foreach($this->val->getErrorMessages(TRUE) as $msg) {
          $msgs[] = sprintf("<li class='validation_error'>%s</li>", $msg);
        }
        $msgs = sprintf("<ul class='validation_errors'>%s</ul>", implode("\n", $msgs));

      break;
    }

    return $msgs;
  }

  // -----------------------------------------------------------

  /**
   * Check if this form was submitted
   *
   * @return boolean
   * Returns TRUE if this form was submitted, FALSE otherwise
   */
  public function wasSubmitted() {

    //TODO: Add CSRF check (optional)
    $key = $this->name . '_token';
    return (isset($_POST[$key]));

  }

}

/* EOF: Form.php */