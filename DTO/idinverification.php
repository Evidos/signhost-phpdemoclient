<?php
// Required due to inheritance.
require_once("verification.php");

class IDinVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("iDIN");
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
