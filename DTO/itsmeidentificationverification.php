<?php
// Required due to inheritance.
require_once("verification.php");

class ItsmeIdentificationVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $PhoneNumber;

	/**
	 * @param string $phoneNumber
	 */
	function __construct($phoneNumber) {
		parent::__construct("itsme Identification");
		$this->PhoneNumber = $phoneNumber;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"        => $this->Type,
			"PhoneNumber" => $this->PhoneNumber,
		));
	}
}
