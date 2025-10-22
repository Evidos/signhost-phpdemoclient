<?php
// Required due to inheritance.
require_once("verification.php");

class SigningCertificateVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("SigningCertificate");
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
