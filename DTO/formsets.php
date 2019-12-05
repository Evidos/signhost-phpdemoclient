<?php
class FormSets implements JsonSerializable {
	/** @var string[] */
	public $FormSets;

	/**
	 * @param string[] $formSets
	 */
	function __construct($formSets) {
		$this->FormSets = $formSets;
	}

	function jsonSerialize() {
		return array_filter(array(
			"FormSets" => $this->FormSets,
		));
	}
}
