<?php
// Required due to inheritance.
require_once("verification.php");

class OpenIDProvidersVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $ProviderName;

	/**
	 * @param string $ProviderName
	 */
	function __construct($ProviderName = null) {
		parent::__construct("OpenID Providers");
		$this->ProviderName = $ProviderName;
	}

	function jsonSerialize(): mixed {
		return array_filter(array(
			"Type" => $this->Type,
			"ProviderName" => $this->ProviderName,
		));
	}
}