<?php

namespace Formula\Fields\Abstracts;

class MultipleChoiceInput extends Input {

	/**
	 * @var array
	 */
	public $options;

	/**
	 * @var boolean
	 */
	public $allowOther;

	/**
	 * @var string
	 */
	public $otherLabel;
}

/* EOF: MultipleChoiceInput.php */