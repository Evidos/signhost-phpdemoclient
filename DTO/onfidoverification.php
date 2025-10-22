<?php
// Required due to inheritance.
require_once("verification.php");

class OnfidoVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("Onfido");
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
