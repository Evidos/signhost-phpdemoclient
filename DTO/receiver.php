<?php
class Receiver implements JsonSerializable {
	/** @var string */
	public $Name;

	/** @var string */
	public $Email;

	/** @var string */
	public $Language;

	/** @var string */
	public $Message;

	/** @var string */
	public $Reference;

	/** @var object */
	public $Context;

	/**
	 * @param string $name
	 * @param string $email
	 * @param string $message
	 * @param string $language
	 * @param string $reference
	 * @param object $context
	 */
	function __construct(
		$name,
		$email,
		$message,
		$language  = "nl-NL",
		$reference = null,
		$context   = null
	) {
		$this->Name      = $name;
		$this->Email     = $email;
		$this->Language  = $language;
		$this->Message   = $message;
		$this->Reference = $reference;
		$this->Context   = $context;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Name"      => $this->Name,
			"Email"     => $this->Email,
			"Language"  => $this->Language,
			"Message"   => $this->Message,
			"Reference" => $this->Reference,
			"Context"   => $this->Context,
		));
	}
}
