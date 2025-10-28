<?php
// Required due to inheritance.
require_once("verification.php");

class CSCQualifiedVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("CSC Qualified");
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}