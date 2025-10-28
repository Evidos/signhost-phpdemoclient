<?php
// Required due to inheritance.
require_once("verification.php");

class OnfidoVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $WorkflowId;

	/**
	 * @param string $WorkflowId
	 */
	function __construct($WorkflowId = null) {
		parent::__construct("Onfido");
		$this->WorkflowId = $WorkflowId;
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
			"WorkflowId" => $this->WorkflowId,
		));
	}
}