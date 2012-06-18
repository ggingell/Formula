<?php

namespace Formula\Fields\Abstracts;

abstract class MultipleChoiceInput extends Input {

	public static $multipleChoiceLabelHtml = "<label class='multchoice_label'>%s</label>"; //Label Text

	/**
	 * Associative array
	 *
	 * @var array
	 */
	public $options;

	/**
	 * @var boolean
	 */
	public $allowOther = FALSE;

	/**
	 * @var string
	 */
	public $otherLabel;

  /**
   * @var string
   */
	public $otherPlaceholder = 'Other';

	/**
	 * If TRUE, array keys in the $options will not be used
	 * when submitting the form
	 *
	 * @var boolean
	 */
	public $useValues = FALSE;

  // -----------------------------------------------------------
 
  /**
   * Magic method to auto-add validation rules for multiple choice
   */
  public function __get($item) {

  	if ('validation' == $item && ! $this->allowOther) {

  	  //Auto-add the available options as isOneOf Validation
  	  $opts = ($this->useValues) ? array_values($this->options) : array_keys($this->options);
  	  $rule = "isOneOf[" . implode(';', $opts) . "]";

  	  if (is_array($this->validation)) {
  	    $this->validation[] = $rule;
  	  }
  	  else {
  	    $this->validation .= (strlen($this->validation) > 0) ? '|' . $rule : $rule;
  	  }
  	}

  	return parent::__get($item);
  }
}

/* EOF: MultipleChoiceInput.php */