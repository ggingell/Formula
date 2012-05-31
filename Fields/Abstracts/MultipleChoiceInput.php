<?php

namespace Formula\Fields\Abstracts;

abstract class MultipleChoiceInput extends Input {

	/**
	 * Associative array
	 *
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
	public $otherLabel = 'Other';

	/**
	 * If TRUE, array keys in the $options will not be used
	 * when submitting the form
	 *
	 * @var boolean
	 */
	public $useValues = FALSE;

}

/* EOF: MultipleChoiceInput.php */