<?php
class Signer implements JsonSerializable {
	/** @var string */
	public $Id;

	/** @var string */
	public $Email;

	/** @var Verification[] */
	public $Authentications;

	/** @var Verification[] */
	public $Verifications;

	/** @var bool */
	public $SendSignRequest;

	/** @var string */
	public $SignRequestMessage;

	/** @var bool */
	public $SendSignConfirmation;

	/** @var string */
	public $Language;

	/** @var string */
	public $ScribbleName;

	/** @var int */
	public $DaysToRemind;

	/** @var string */
	public $Expires;

	/** @var string */
	public $Reference;

	/** @var string */
	public $ReturnUrl;

	/** @var object */
	public $Context;

	/**
	 * @param string         $email
	 * @param string         $id
	 * @param Verification[] $authentications
	 * @param Verification[] $verifications
	 * @param bool           $sendSignRequest
	 * @param string         $signRequestMessage
	 * @param bool           $sendSignConfirmation
	 * @param string         $language
	 * @param string         $scribbleName
	 * @param int            $daysToRemind
	 * @param string         $expires
	 * @param string         $reference
	 * @param string         $returnUrl
	 * @param object         $context
	 */
	function __construct(
		$email,
		$id                   = null,
		$authentications      = array(),
		$verifications        = array(),
		$sendSignRequest      = true,
		$signRequestMessage   = null,
		$sendSignConfirmation = null,
		$language             = "nl-NL",
		$scribbleName         = null,
		$daysToRemind         = 7,
		$expires              = null,
		$reference            = null,
		$returnUrl            = "https://signhost.com",
		$context              = null
	) {
		$this->Id                   = $id;
		$this->Email                = $email;
		$this->Authentications      = $authentications;
		$this->Verifications        = $verifications;
		$this->SendSignRequest      = $sendSignRequest;
		$this->SignRequestMessage   = $signRequestMessage;
		$this->SendSignConfirmation = $sendSignConfirmation;
		$this->Language             = $language;
		$this->ScribbleName         = $scribbleName;
		$this->DaysToRemind         = $daysToRemind;
		$this->Expires              = $expires;
		$this->Reference            = $reference;
		$this->ReturnUrl            = $returnUrl;
		$this->Context              = $context;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Id"                   => $this->Id,
			"Email"                => $this->Email,
			"Authentications"      => $this->Authentications,
			"Verifications"        => $this->Verifications,
			"SendSignRequest"      => $this->SendSignRequest,
			"SignRequestMessage"   => $this->SignRequestMessage,
			"SendSignConfirmation" => $this->SendSignConfirmation,
			"Language"             => $this->Language,
			"ScribbleName"         => $this->ScribbleName,
			"DaysToRemind"         => $this->DaysToRemind,
			"Expires"              => $this->Expires,
			"Reference"            => $this->Reference,
			"ReturnUrl"            => $this->ReturnUrl,
			"Context"              => $this->Context,
		));
	}
}
