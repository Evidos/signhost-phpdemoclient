<?php
// Required due to inheritance.
require_once("verification.php");

class EidasLoginVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("eIDAS Login");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
