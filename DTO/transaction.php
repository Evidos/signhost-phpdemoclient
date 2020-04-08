<?php
class Transaction implements JsonSerializable {
	/** @var bool */
	public $Seal;

	/** @var Signer[] */
	public $Signers;

	/** @var Receiver[] */
	public $Receivers;

	/** @var string */
	public $Reference;

	/** @var string */
	public $PostbackUrl;

	/** @var int */
	public $SignRequestMode;

	/** @var int */
	public $DaysToExpire;

	/** @var bool */
	public $SendEmailNotifications;

	/** @var object */
	public $Context;

	/**
	 * @param bool       $seal
	 * @param Signer[]   $signers
	 * @param Receiver[] $receivers
	 * @param string     $reference
	 * @param string     $postbackUrl
	 * @param int        $signRequestMode
	 * @param bool       $daysToExpire
	 * @param bool       $sendEmailNotifications
	 * @param object     $context
	 */
	function __construct(
		$seal                   = false,
		$signers                = array(),
		$receivers              = array(),
		$reference              = null,
		$postbackUrl            = null,
		$signRequestMode        = 2,
		$daysToExpire           = 60,
		$sendEmailNotifications = true,
		$context                = null
	) {
		$this->Seal                   = $seal;
		$this->Signers                = $signers;
		$this->Receivers              = $receivers;
		$this->Reference              = $reference;
		$this->PostbackUrl            = $postbackUrl;
		$this->SignRequestMode        = $signRequestMode;
		$this->DaysToExpire           = $daysToExpire;
		$this->SendEmailNotifications = $sendEmailNotifications;
		$this->Context                = $context;
	}

	function jsonSerialize() {
		$filtered = array_filter(array(
			"Seal"                   => $this->Seal,
			"Signers"                => $this->Signers,
			"Receivers"              => $this->Receivers,
			"Reference"              => $this->Reference,
			"PostbackUrl"            => $this->PostbackUrl,
			"SignRequestMode"        => $this->SignRequestMode,
			"DaysToExpire"           => $this->DaysToExpire,
			"Context"                => $this->Context,
		));

		if ($this->SendEmailNotifications === false) {
			$filtered["SendEmailNotifications"] = $this->SendEmailNotifications;
		}

		return $filtered;
	}
}
