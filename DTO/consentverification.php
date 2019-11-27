<?php
// Required due to inheritance.
require_once("verification.php");

class ConsentVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("Consent");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
