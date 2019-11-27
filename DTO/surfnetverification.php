<?php
// Required due to inheritance.
require_once("verification.php");

class SurfnetVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("SURFnet");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
