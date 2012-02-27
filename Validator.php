<?php

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
	private $validation_rules = array();

	/**
	 * Array containing any error messages.  Will be empty if no errors
	 * @var array
	 */
	private $error_messages = array();

	/*
	 * Temporary error message containers for processing...
	 */

	/**
	 * @var string
	 */
	private $rule_context = NULL;

	/**
	 * @var string
	 */
	private $field_context = NULL;

	// -----------------------------------------------------------

	/**
	 * Set rules for form field values
	 *
	 * @param string $field  Form field name attribute
	 * @param string $human_name  A human name for the field
	 * @param string|array $rules  Rules, separated by pipes ("|")
	 */
	public function set_post_rules($field, $human_name, $rules) {

		$data_val = (isset($_POST[$field])) ? $_POST[$field] : NULL;
		$this->set_rules($field, $_POST[$field], $human_name, $rules);

	}

	// -----------------------------------------------------------

	/**
	 * Set rules for arbitrary data
	 * 
	 * @param string $data_name  A machine name (slug) for the data
	 * @param mixed $data  The data to be validated
	 * @param string $human_name  A human name for the field
	 * @param string|array $rules  Rules, separated by pipes ("|"), or an array of them
	 */
	public function set_rules($data_name, $data, $human_name = NULL, $rules = NULL) {

		$rule = (object) array(
			'name'       => $data_name,
			'data'       => $data,
			'human_name' => $human_name ?: $data_name,
			'rules'			 => (is_array($rules) ? $rules : explode("|", $rules))
		);

		$this->validation_rules[$data_name] = $rule;
	}
	
	// -----------------------------------------------------------
  
  public function add_rules($data_name, $rules = NULL) {
    
    if ( ! is_array($rules))
      $rules = explode("|", $rules);
      
    
    if ( ! isset($this->validation_rules[$data_name]))
      throw new RuntimeException("Data $data_name not defined in Validator.  Use set_rules method before running add_rules");
    else {
      foreach($rules as $rule)
        $this->validation_rules[$data_name]->rules[] = $rule;
    }
  }
  
	// -----------------------------------------------------------

	/**
	 * Run validation on preset rules
	 *
	 * @return boolean  TRUE if all validation checks passed; FALSE if any failed
	 */
	public function run() {

		$overall_result = TRUE;

		foreach($this->validation_rules as &$ritem) {

			$result = $this->validate($ritem->name, $ritem->data, $ritem->human_name, $ritem->rules);

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
	 * @param string $data_name  A machine name (slug) for the data
	 * @param mixed $data  The data to be validated
	 * @param string $human_name  A human name for the field
	 * @param array $rules  Rules array
	 * @return boolean  Whether validation succeeded or not
	 */
	public function validate($data_name, $data, $human_name = NULL, $rules = array()) {

		//Set field context
		$this->field_context = $data_name;

		//Run rules...
		foreach($rules as $rule) {
			$result = $this->run_rule($rule, $data);
		}

		//Clear field context
		$this->field_context = NULL;

		return $result;
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
	 * @return array  An array of error messages (empty if no errors were encountered)
	 */
	public function get_error_messages($linear = FALSE) {

		if ($linear) {

			$out_msgs = array();

			foreach($this->error_messages as $field) {
				$out_msgs = array_merge($out_msgs, $field);
			}

			return $out_msgs;
		}
		else {
			return $this->error_messages;
		}

	}

	// -----------------------------------------------------------

	/**
	 * Get validation errors for a single field
	 * 
	 * @param string $field
	 * @return array  Empty if there were no validation errors for the field
	 */
	public function get_error_messages_for_field($field) {
		return (isset($this->error_messages[$field])) ? $this->error_messages[$field] : array();
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
	private function run_rule($rule, $value) {

		$regex = "^([a-zA-Z0-9\-_]+)(\[([a-zA-Z0-9\-_\s:;,]+?)\])?$";
		preg_match("/$regex/", $rule, $matches);

		//Extract the function and the arguments
		$function = (isset($matches[1])) ? $matches[1] : FALSE;
		
		$args = (isset($matches[3])) ? $matches[3] : '';
		$args = array_filter(array_map('trim', explode(',', $args)));
		array_unshift($args, $value);

		//Set Context
		$this->rule_context = $function;

		//Run it
		if (method_exists($this, $function))
			$result = call_user_func_array(array($this, $function), $args);
		elseif (function_exists($function))
			$result = call_user_func_array($function, $args);
		else //no validation rule; ignore...
			$result = TRUE;

		//Clear Context
		$this->rule_context = NULL;

		return $result;
	}

	// -----------------------------------------------------------

	/**
	 * Set current message for an error
	 * 
	 * @param string $rule_name  The rule name to apply the message to
	 * @param string $message  The message
	 */
	private function set_curr_message($rule_name, $message) {

		//Skip setting message if no field context
		if ( ! $this->field_context)
			return;

		//Skip setting the error message if the rule context is set and doesn't match
		if ($this->rule_context && $rule_name !== $this->rule_context)
			return;

		$human_name = $this->validation_rules[$this->field_context]->human_name;
		$message = sprintf($message, $human_name);

		$this->error_messages[$this->field_context][] = $message;
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
	public function is_required($str) {

		if (is_array($str) OR is_object($str) && count($str) > 0) {
			return TRUE;
		}
		if (trim((string) $str) !== '') {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_required', '%s is required!');
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
	public function is_numeric($str) {		

		if (is_numeric($str)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_numeric', "%s must be a number!");
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
	public function is_integer($str) {

		if ((string) (int) $str === (string) $str) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_integer', "%s must be an integer!");
		}

	}
	
	// -----------------------------------------------------------

	/**
	 * Validate email address
	 *
	 * @param string $str
	 * @return boolean
	 */
	public function is_email_address($str) {


		if ((bool) filter_var($str, FILTER_VALIDATE_EMAIL)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_email_address', "%s must be a valid email address (e.g. you@example.com)");
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
	public function is_one_of($str, $vals) {

		if ( ! is_array($vals)) {
			$vals = array_filter(array_map('trim', explode(';', $vals)));
		}

		if (in_array($str, $vals)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_one_of', '%s is an unacceptable value!');
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
	public function is_url($str) {

		if ((bool) filter_var($str, FILTER_VALIDATE_URL)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_url', "%s must be a valid URL!");
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
	public function is_valid_ip($str) {

		if ($this->is_valid_ipv4($str) && $this->is_valid_ipv6($str)) {
			return TRUE;
		} else {
			$this->set_curr_message('is_valid_ip', "%s must be a valid IPv4 or IPv6 address!");
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
	public function is_valid_hostname($str) {

		$hostname_regex = "/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z]|[A-Za-z][A-Za-z0-9\-]*[A-Za-z0-9])$/";

		if ($this->is_valid_ip($str) && preg_match($hostname_regex, $str)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_valid_hostname', "%s must be a valid hostname!");
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
	public function is_valid_ipv4($str) {

		if ((bool) filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_valid_ipv4', "%s must be a valid IPv4 address!");
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
	public function is_public_ip($str) {
		

		if ((bool) filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE, FILTER_FLAG_NO_PRIV_RANGE)) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_public_ip', "%s must be a public IPv4 Address!");
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
	public function is_valid_ipv6($str) {


		if ((bool) filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			return TRUE;
		} else {
			$this->set_curr_message('is_valid_ipv6', "%s must be a valid IPv6 address!");
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
	public function is_less_than($str, $val) {

		if ((float) $str < (float) $val) {
			return TRUE;
		} else {
			$decs = ($val % 1 == 0) ? 0 : strlen(substr((string) $val, strpos('.')));
			$this->set_curr_message('is_less_than', "%s must be less than " . number_format((float) $str, $decs));
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
	public function is_greater_than($str, $val) {

		if ((float) $str > (float) $val) {
			return TRUE;
		} else {
			$decs = ($val % 1 == 0) ? 0 : strlen(substr((string) $val, strpos('.')));
			$this->set_curr_message('is_greater_than', "%s must be greater than " . number_format((float) $str, $decs));
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
	public function is_shorter_than($str, $val) {

		if (strlen($str) < $val) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_shorter_than', "%s must be shorter than $val characters!");
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
	public function is_longer_than($str, $val) {

		if (strlen($str) > $val) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_longer_than', "%s must be longer than $val characters!");
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
	public function is_exactly($str, $val) {
		
		if (strcmp($str, $val) == 0) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_exactly', "The %s field must match '$val'");
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
	public function is_exactly_case_insensitive($str, $val) {
		
		if (strcasecmp($str, $val) == 0) {
			return TRUE;
		}
		else {
			$this->set_curr_message('is_exactly_case_insensitive', "The %s field must match '$val");
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
	 * @param string $str
	 * @param string $val
	 * @return boolean
	 */
	public function is_containing($str, $val)
	{
		if (strpos($str, $val) !== FALSE)
			return TRUE;
		else
		{
			$this->set_curr_message('is_containing', "The %s field must contain '$val'");
			return FALSE;
		}
	}

}

/* EOF: validator.php */