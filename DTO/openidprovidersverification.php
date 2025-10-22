<?php
// Required due to inheritance.
require_once("verification.php");

class OpenIDProvidersVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("OpenID Providers");
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}
