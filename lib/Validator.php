<?php

/**
 * @file Validator Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

namespace Formula;

/**
 * Validator Class
 *
 * For validationg forms and arbitrary data
 *
 * @package Formula
 * @author Casey McLaughlin
 */
class Validator {

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	private $validationRules = array();

	/**
	 * Array containing any error messages.  Will be empty if no errors
	 * @var array
	 */
	private $errorMessages = array();

	/*
	 * Temporary error message containers for processing...
	 */

	/**
	 * @var string
	 */
	private $ruleContext = NULL;

	/**
	 * @var string
	 */
	private $fieldContext = NULL;

	// -----------------------------------------------------------

	/**
	 * Set rules for form field values
	 *
	 * @param string $field  Form field name attribute
	 * @param string $label  A human name for the field
	 * @param string|array $rules  Rules, separated by pipes ("|")
	 */
	public function setInputRules($field, $label, $rules) {

		$dataVal = (isset($_POST[$field])) ? $_POST[$field] : NULL;
		$this->setRules($field, $dataVal, $label, $rules);

	}

	// -----------------------------------------------------------

	/**
	 * Set rules for arbitrary data
	 *
	 * @param string $dataName  A machine name (slug) for the data
	 * @param mixed $data  The data to be validated
	 * @param string $label  A human name for the field
	 * @param string|array $rules  Rules, separated by pipes ("|"), or an array of them
	 */
	public function setRules($dataName, $data, $label, $rules) {

		$rule = (object) array(
			'name'  => $dataName,
			'data'  => $data,
			'label' => $label ?: $dataName,
			'rules'	=> (is_array($rules) ? $rules : explode("|", $rules))
		);

		$this->validationRules[$dataName] = $rule;
	}

	// -----------------------------------------------------------

  //Do we need this?
  /*public function add_rules($dataName, $rules = NULL) {

    if ( ! is_array($rules))
      $rules = explode("|", $rules);


    if ( ! isset($this->validationRules[$dataName]))
      throw new RuntimeException("Data $dataName not defined in Validator.  Use set_rules method before running add_rules");
    else {
      foreach($rules as $rule)
        $this->validationRules[$dataName]->rules[] = $rule;
    }
  }*/

	// -----------------------------------------------------------

	/**
	 * Run validation on preset rules
	 *
	 * @return boolean  TRUE if all validation checks passed; FALSE if any failed
	 */
	public function run() {

		$overall_result = TRUE;

		foreach($this->validationRules as &$ritem) {

			$result = $this->validate($ritem->name, $ritem->data, $ritem->label, $ritem->rules);

			if ( ! is_bool($result))
				$ritem->data = $result;
			elseif ($result === FALSE) {
				$overall_result = FALSE;
			}
		}
		unset($ritem);

		return $overall_result;
	}

	// -----------------------------------------------------------

	/**
	 * Validate a single item
	 *
	 * @param string $dataName  A machine name (slug) for the data
	 * @param mixed $data  The data to be validated
	 * @param string $label  A human name for the field
	 * @param array $rules  Rules array
	 * @return boolean  Whether validation succeeded or not
	 */
	public function validate($dataName, $data, $label, $rules) {

		//Set field context
		$this->fieldContext = (object) array(
      'dataName' => $dataName,
      'data' => $data,
      'label' => $label
    );

    //Set succeed/fail flag
    $success = TRUE;

		//Run rules...
		foreach($rules as $rule) {

      if ( ! $this->runRule($rule, $data))
        $success = FALSE;
		}

		//Clear field context
		$this->fieldContext = NULL;

		return $success;
	}

	// -----------------------------------------------------------

	/**
	 * Get error messages from failed validation
	 *
	 * If $linear is TRUE, the error messages will be a flat array of
	 * messages.  If FALSE (default), it will be a hierarchal array, with
	 * array keys as data field names.
	 *
	 * @param boolean $linear
	 * @param boolean $filter_isRequired
	 * @return array  An array of error messages (empty if no errors were encountered)
	 */
	public function getErrorMessages($linear = FALSE, $filterIsRequired = TRUE) {

		if ($filterIsRequired) {
			foreach($this->errorMessages as &$field) {
				if (isset($field['isRequired'])) {
					$field = array('isRequired' => $field['isRequired']);
				}
			}
		}

		if ($linear) {

			$msgs = array();

			foreach($this->errorMessages as $field) {
				$msgs = array_merge($msgs, $field);
			}

			return $msgs;
		}
		else {
			return $this->errorMessages;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Get validation errors for a single field
	 *
	 * @param string $field
	 * @return array  Empty if there were no validation errors for the field
	 */
	public function getErrorMessagesForField($field) {
		return (isset($this->errorMessages[$field])) ? $this->errorMessages[$field] : array();
	}

	// -----------------------------------------------------------

	/**
	 * Run Rule
	 *
	 * @param string $rule
	 * A single rule
	 *
	 * @return boolean|mixed
	 * If TRUE, validation succeeded
	 * If FALSE, validation failed
	 * If a other value, prep function finished
	 */
	private function runRule($rule, $value) {

		$regex = "^([a-zA-Z0-9\-_]+)(\[([a-zA-Z0-9\-_\s:;,]+?)\])?$";
		preg_match("/$regex/", $rule, $matches);

		//Extract the function and the arguments
		$function = (isset($matches[1])) ? $matches[1] : FALSE;

		$args = (isset($matches[3])) ? $matches[3] : '';
		$args = array_filter(array_map('trim', explode(',', $args)));
		array_unshift($args, $value);

		//Set Context
		$this->ruleContext = $function;

		//Run it
		if (method_exists($this, $function))
			$result = call_user_func_array(array($this, $function), $args);
		elseif (function_exists($function))
			$result = call_user_func_array($function, $args);
		else //no validation rule; ignore...
			$result = TRUE;

		//Clear Context
		$this->ruleContext = NULL;

		return $result;
	}

	// -----------------------------------------------------------

	/**
	 * Set current message for an error
	 *
	 * @param string $ruleName  The rule name to apply the message to
	 * @param string $message  The message
	 */
	private function setCurrMsg($ruleName, $message) {

		//Skip setting message if no field context
		if ( ! $this->fieldContext)
			return;

		//Skip setting the error message if the rule context is set and doesn't match
		if ($this->ruleContext && $ruleName !== $this->ruleContext)
			return;

		$label = $this->fieldContext->label;
		$message = sprintf($message, $label);
    $dataName = $this->fieldContext->dataName;

		$this->errorMessages[$dataName][$ruleName] = $message;
	}

	// -----------------------------------------------------------

	/*
	 * Data Validation Checks (always start with 'is_')
	 */

	// -----------------------------------------------------------

	/**
	 * Check if field contains any values
	 *
	 * @param string|array|object $str
	 * @return boolean
	 */
	public function isRequired($str) {

		if (is_array($str) OR is_object($str) && count($str) > 0) {
			return TRUE;
		}
		if (trim((string) $str) !== '') {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isRequired', '%s is required!');
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if a value is numeric
	 *
	 * @param string|int|float $str
	 * @return boolean
	 */
	public function isNumeric($str) {

		if (is_numeric($str)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isNumeric', "%s must be a number!");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if value is an integer (string integer or literal)
	 *
	 * @param strin|int|float $str
	 * @return boolean
	 */
	public function isInteger($str) {

		if ((string) (int) $str === (string) $str) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isInteger', "%s must be an integer!");
      return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Validate email address
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isEmailAddress($str) {


		if ((bool) filter_var($str, FILTER_VALIDATE_EMAIL)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isEmailAddress', "%s must be a valid email address (e.g. you@example.com)");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if is one of several values
	 *
	 * @param string $str
	 * @param string|array $vals  If string, separate with ';' character
	 * @return boolean
	 */
	public function isOneOf($str, $vals) {

		if ( ! is_array($vals)) {
			$vals = array_filter(array_map('trim', explode(';', $vals)));
		}

		if (in_array($str, $vals)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isOneOf', '%s is an unacceptable value!');
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if the string is a valid URL
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isUrl($str) {

		if ((bool) filter_var($str, FILTER_VALIDATE_URL)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isUrl', "%s must be a valid URL!");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if the string is a valid IPv4 or IPv6 address
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isValidIP($str) {

		if ($this->isValidIPv4($str) OR $this->isValidIPv6($str)) {
			return TRUE;
		} else {
			$this->setCurrMsg('isValidIP', "%s must be a valid IPv4 or IPv6 address!");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if string is a valid hostname (IP address or DNS name)
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isValidHostname($str) {

		$hostname_regex = "/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/";

		if ($this->isValidIP($str) OR preg_match($hostname_regex, $str)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isValidHostname', "%s must be a valid hostname!");
			return FALSE;
		}


	}

	// -----------------------------------------------------------

	/**
	 * Check if string contains valid IPv4 address
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isValidIPv4($str) {

		if ((bool) filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isValidIPv4', "%s must be a valid IPv4 address!");
			return FALSE;
		}
	}

	// -----------------------------------------------------------

	/**
	 * Check if string contains valid public IPv4 address
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isPublicIP($str) {


		if ((bool) filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE|FILTER_FLAG_NO_PRIV_RANGE)) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isPublicIP', "%s must be a public Address!");
			return FALSE;
		}
	}

	// -----------------------------------------------------------

	/**
	 * Check if string contains valid IPv6 address
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function isValidIPv6($str) {


		if ((bool) filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return TRUE;
		} else {
			$this->setCurrMsg('isValidIPv6', "%s must be a valid IPv6 address!");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if value is less than another value
	 *
	 * @param int|float|string $str
	 * @param int|float $val
	 * @return boolean
	 */
	public function isLessThan($str, $val) {

		if ((float) $str < (float) $val) {
			return TRUE;
		} else {
			$decs = ($val % 1 == 0) ? 0 : strlen(substr((string) $val, strpos('.')));
			$this->setCurrMsg('isLessThan', "%s must be less than " . number_format((float) $str, $decs));
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if value is greater than another value
	 *
	 * @param int|float|string $str
	 * @param int|float $val
	 * @return boolean
	 */
	public function isGreaterThan($str, $val) {

		if ((float) $str > (float) $val) {
			return TRUE;
		} else {
			$decs = ($val % 1 == 0) ? 0 : strlen(substr((string) $val, strpos('.')));
			$this->setCurrMsg('isGreaterThan', "%s must be greater than " . number_format((float) $str, $decs));
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Check if string is shorter than a value
	 *
	 * @param string $str
	 * @param int $val
	 * @return boolean
	 */
	public function isShorterThan($str, $val) {

		if (strlen($str) < $val) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isShorterThan', "%s must be shorter than $val characters!");
			return FALSE;
		}
	}

	// -----------------------------------------------------------

	/**
	 * Check if string is longer than a value
	 *
	 * @param string $str
	 * @param int $val
	 * @return boolean
	 */
	public function isLongerThan($str, $val) {

		if (strlen($str) > $val) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isLongerThan', "%s must be longer than $val characters!");
			return FALSE;
		}
	}

	// -----------------------------------------------------------

	/**
	 * Checks if string matches another string
	 *
	 * @param string $str
	 * @param string $val
	 * @return boolean
	 */
	public function isExactly($str, $val) {

    if (is_array($str) OR is_object($str))
      $str = serialize($str);
    if (is_array($val) OR is_object($val))
      $val = serialize($val);

    if (is_numeric($str) && is_numeric($val) && $val == $str) {
      return TRUE;
    }
		elseif (( ! is_numeric($str) OR ! is_numeric($val)) && strcmp($str, $val) == 0) {
			return TRUE;
		}
		else {
			$this->setCurrMsg('isExactly', "The %s field must match '$val'");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Checks if string matches another string; ignores case
	 *
	 * @param string $str
	 * @param string $val
	 * @return boolean
	 */
	public function isExactlyCaseInsensitive($str, $val) {

    //Check if can be cast to string and if they match
    //@link http://stackoverflow.com/questions/5496656/check-if-item-can-be-converted-to-string
    if((
        ( ! is_array( $str ) ) &&
        ( ( !is_object( $str ) && settype( $str, 'string' ) !== false ) ||
        ( is_object( $str ) && method_exists( $str, '__toString' ) ) )
    ) && (
        ( ! is_array( $val ) ) &&
        ( ( !is_object( $val ) && settype( $val, 'string' ) !== false ) ||
        ( is_object( $val ) && method_exists( $val, '__toString' ) ) )
    )
    && strcasecmp($str, $val) == 0) {
			return TRUE;
		}
    elseif ($this->isExactly($str, $val))
      return TRUE;
		else {
			$this->setCurrMsg('isExactlyCaseInsensitive', "The %s field must match '$val");
			return FALSE;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Checks to see if a string contains a specific value
	 *
	 * If the string contains the value, return true, otherwise
	 * return false
	 *
	 * @param string|array $str
	 * @param string $val
	 * @return boolean
	 */
	public function isContaining($str, $val)
	{
    if ((is_array($str) OR is_object($str)) && in_array($val, $str)) {
      return TRUE;
    }
		elseif ( ! is_array($str) && ! is_object($str) && strpos((string) $str, (string) $val) !== FALSE)
			return TRUE;
		else
		{
			$this->setCurrMsg('isContaining', "The %s field must contain '$val'");
			return FALSE;
		}
	}

	// -----------------------------------------------------------

  /**
   * Checks to see if a string is alphanumeric
   *
   * @param string $str
   * @return boolean
   */
  public function isAlphaNumeric($str) {

    if (preg_match("/^([a-zA-Z0-9]+)$/", $str) > 0)
      return TRUE;
    else
    {
      $this->setCurrMsg('isAlphaNumeric', "The %s field contains invalid characters");
      return FALSE;
    }

  }

	// -----------------------------------------------------------

  /**
   * Checks to see if a string is alphanumeric
   *
   * dashes and underscores allowed
   *
   * @param string $str
   * @return boolean
   */
  public function isAlphaNumDash($str) {

    if (preg_match("/^([a-zA-Z0-9_-]+)$/", $str) > 0)
      return TRUE;
    else
    {
      $this->setCurrMsg('isAlphaNumDash', "The %s field contains invalid characters");
      return FALSE;
    }

  }

	// -----------------------------------------------------------

  /**
   * Checks to see if a string is alphanumeric
   *
   * dashes, underscores, and spaces allowed
   *
   * @param string $str
   * @return boolean
   */
  public function isAlphaNumDashSpace($str) {

    if (preg_match("/^([a-zA-Z0-9 _\-]+)$/", $str) > 0)
      return TRUE;
    else
    {
      $this->setCurrMsg('isAlphaNumDashSpace', "The %s field contains invalid characters");
      return FALSE;
    }
  }

}

/* EOF: validator.php */