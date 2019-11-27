<?php
// Required due to inheritance.
require_once("verification.php");

class IDealVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $Iban;

	/**
	 * @param string $iban
	 */
	function __construct($iban = null) {
		parent::__construct("iDeal");
		$this->Iban = $iban;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
			"Iban" => $this->Iban,
		));
	}
}
