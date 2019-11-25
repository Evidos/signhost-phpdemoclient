<?php
class SignHost {
	const API_VERSION = "v1";

	/** @var string */
	public $AppKey;

	/** @var string */
	public $ApiKey;

	/** @var string */
	public $SharedSecret;

	/** @var string */
	public $ApiEndpoint;

	/**
	 * @param string $appKey
	 * @param string $apiKey
	 * @param string $sharedSecret
	 * @param string $apiEndpoint
	 */
	function __construct(
		$appKey,
		$apiKey,
		$sharedSecret = null,
		$apiEndpoint  = "https://api.signhost.com/api"
	) {
		$this->AppKey       = $appKey;
		$this->ApiKey       = $apiKey;
		$this->SharedSecret = $sharedSecret;
		$this->ApiEndpoint  = $apiEndpoint;
	}

	/**
	 * Creates a new transaction.
	 * @param Transaction $transaction
	 * @return object
	 */
	public function CreateTransaction($transaction) {
		$ch = curl_init($this->ApiEndpoint."/transaction");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Content-Type: application/json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$responseJson = curl_exec($ch);
		curl_close($ch);

		return json_decode($responseJson);
	}

	/**
	 * Gets an exisiting transaction by providing a transaction ID.
	 *
	 * When the response has a status code of 410, you can still retrieve
	 * partial historical data from the JSON in the error message property.
	 * @param string $transactionId
	 * @return object
	 */
	public function GetTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$responseJson = curl_exec($ch);
		curl_close($ch);

		return json_decode($responseJson);
	}

	/**
	 * Deletes an exisiting transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @return object
	 */
	public function DeleteTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * Starts an exisiting transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @return object
	 */
	public function StartTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/start");
		curl_setopt($ch, CURLOPT_PUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$responseJson = curl_exec($ch);
		curl_close($ch);

		return json_decode($responseJson);
	}

	/**
	 * Add a file to an existing transaction by providing a file path
	 * and a transaction ID.
	 * @param string $transactionId
	 * @param string $fileId
	 * @param string $filePath
	 * @return object
	 */
	public function AddOrReplaceFile($transactionId, $fileId, $filePath) {
		$checksum_file = base64_encode(pack('H*', hash_file('sha256', $filePath)));
		$fh = fopen($filePath, 'r');
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
		curl_setopt($ch, CURLOPT_PUT, 1);
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Content-Type: application/pdf",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
			"Digest: SHA256=".$checksum_file,
		));

		$response = curl_exec($ch);
		curl_close($ch);
		fclose($fh);

		return $response;
	}

	/**
	 * Adds file metadata for a file to an existing transaction by providing a transaction ID.
	 * @param string       $transactionId
	 * @param string       $fileId
	 * @param FileMetadata $metadata
	 * @return object
	 */
	public function AddOrReplaceMetadata($transactionId, $fileId, $metadata) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($metadata));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Content-Type: application/json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Gets the receipt of a finished transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @return object
	 */
	public function GetReceipt($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/file/receipt/".$transactionId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: ".
				"application/pdf, ".
				"application/vnd.signhost.".self::API_VERSION."+json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Gets the document of a transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @param string $fileId
	 * @return object
	 */
	public function GetDocument($transactionId, $fileId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: ".
				"application/pdf, ".
				"application/vnd.signhost.".self::API_VERSION."+json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Generates a checksum and validates it with the remote checksum.
	 * @param string $masterTransactionId
	 * @param string $fileId
	 * @param int    $status
	 * @param string $remoteChecksum
	 * @return bool
	 */
	public function ValidateChecksum($masterTransactionId, $fileId, $status, $remoteChecksum) {
		$localChecksum = sha1($masterTransactionId."|".$fileId."|".$status."|".$this->SharedSecret);

		if (strlen($localChecksum) !== strlen($remoteChecksum)) {
			return false;
		}

		return hash_equals($localChecksum, $remoteChecksum);
	}
}

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
		$sendEmailNotifications = false,
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
		return array_filter(array(
			"Seal"                   => $this->Seal,
			"Signers"                => $this->Signers,
			"Receivers"              => $this->Receivers,
			"Reference"              => $this->Reference,
			"PostbackUrl"            => $this->PostbackUrl,
			"SignRequestMode"        => $this->SignRequestMode,
			"DaysToExpire"           => $this->DaysToExpire,
			"SendEmailNotifications" => $this->SendEmailNotifications,
			"Context"                => $this->Context,
		));
	}
}

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
		$sendSignRequest      = false,
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

abstract class Verification {
	/** @var string */
	public $Type;

	/**
	 * @param string $type
	 */
	function __construct($type) {
		$this->Type = $type;
	}
}

class IDealVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $Iban;

	/**
	 * @param string $iban
	 */
	function __construct($iban = null) {
		parent::__construct("iDeal");
		$this->Iban = $iban;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
			"Iban" => $this->Iban,
		));
	}
}

class IDinVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("iDIN");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}

class DigiDVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $Bsn;

	/**
	 * @param string $bsn
	 */
	function __construct($bsn = null) {
		parent::__construct("DigiD");
		$this->Bsn = $bsn;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
			"Bsn"  => $this->Bsn,
		));
	}
}

class SurfnetVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("SURFnet");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}

class ScribbleVerification extends Verification implements JsonSerializable {
	/** @var bool */
	public $RequireHandsignature;

	/** @var bool */
	public $ScribbleNameFixed;

	/** @var string */
	public $ScribbleName;

	/**
	 * @param bool   $requireHandsignature
	 * @param bool   $scribbleNameFixed
	 * @param string $scribbleName
	 */
	function __construct(
		$requireHandsignature = false,
		$scribbleNameFixed    = false,
		$scribbleName         = null
	) {
		parent::__construct("Scribble");
		$this->RequireHandsignature = $requireHandsignature;
		$this->ScribbleNameFixed    = $scribbleNameFixed;
		$this->ScribbleName         = $scribbleName;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"                 => $this->Type,
			"RequireHandsignature" => $this->RequireHandsignature,
			"ScribbleNameFixed"    => $this->ScribbleNameFixed,
			"ScribbleName"         => $this->ScribbleName,
		));
	}
}

class PhoneNumberVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $Number;

	/**
	 * @param string $number
	 */
	function __construct($number) {
		parent::__construct("PhoneNumber");
		$this->Number = $number;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"   => $this->Type,
			"Number" => $this->Number,
		));
	}
}

class ConsentVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("Consent");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}

class EherkenningVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $EntityConcernIdKvkNr;

	/**
	 * @param string $entityConcernIdKvkNr
	 */
	function __construct($entityConcernIdKvkNr = null) {
		parent::__construct("eHerkenning");
		$this->EntityConcernIdKvkNr = $entityConcernIdKvkNr;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"                 => $this->Type,
			"EntityConcernIdKvkNr" => $this->EntityConcernIdKvkNr,
		));
	}
}

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

class SigningCertificateVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("SigningCertificate");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}

class ItsmeIdentificationVerification extends Verification implements JsonSerializable {
	/** @var string */
	public $PhoneNumber;

	/**
	 * @param string $phoneNumber
	 */
	function __construct($phoneNumber) {
		parent::__construct("itsme Identification");
		$this->PhoneNumber = $phoneNumber;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"        => $this->Type,
			"PhoneNumber" => $this->PhoneNumber,
		));
	}
}

class ItsmeSignVerification extends Verification implements JsonSerializable {
	function __construct() {
		parent::__construct("itsme sign");
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type" => $this->Type,
		));
	}
}

class FileMetadata implements JsonSerializable {
	/** @var string */
	public $DisplayName;

	/** @var int */
	public $DisplayOrder;

	/** @var string */
	public $Description;

	/** @var FormSets[string] */
	public $Signers;

	/** @var FormSetField[string][string] */
	public $FormSets;

	/**
	 * @param string                       $displayName
	 * @param int                          $displayOrder
	 * @param string                       $description
	 * @param FormSets[string]             $signers
	 * @param FormSetField[string][string] $formSets
	 */
	function __construct(
		$displayName  = null,
		$displayOrder = null,
		$description  = null,
		$signers      = null,
		$formSets     = null
	) {
		$this->DisplayName  = $displayName;
		$this->DisplayOrder = $displayOrder;
		$this->Description  = $description;
		$this->Signers      = $signers;
		$this->FormSets     = $formSets;
	}

	function jsonSerialize() {
		return array_filter(array(
			"DisplayName"  => $this->DisplayName,
			"DisplayOrder" => $this->DisplayOrder,
			"Description"  => $this->Description,
			"Signers"      => $this->Signers,
			"FormSets"     => $this->FormSets,
		));
	}
}

class FormSets implements JsonSerializable {
	/** @var string[] */
	public $FormSets;

	/**
	 * @param string[] $formSets
	 */
	function __construct($formSets) {
		$this->FormSets = $formSets;
	}

	function jsonSerialize() {
		return array_filter(array(
			"FormSets" => $this->FormSets,
		));
	}
}

class FormSetField implements JsonSerializable {
	/** @var string */
	public $Type;

	/** @var string */
	public $Value;

	/** @var Location */
	public $Location;

	/**
	 * @param string   $type
	 * @param string   $value
	 * @param Location $location
	 */
	function __construct($type, $location, $value = null) {
		$this->Type     = $type;
		$this->Location = $location;
		$this->Value    = $value;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"     => $this->Type,
			"Value"    => $this->Value,
			"Location" => $this->Location,
		));
	}
}

class Location implements JsonSerializable {
	/** @var string */
	public $Search;

	/** @var int */
	public $Occurence;

	/** @var int */
	public $Top;

	/** @var int */
	public $Right;

	/** @var int */
	public $Bottom;

	/** @var int */
	public $Left;

	/** @var int */
	public $Width;

	/** @var int */
	public $Height;

	/** @var int */
	public $PageNumber;

	/**
	 * @param string $search
	 * @param int    $occurence
	 * @param int    $top
	 * @param int    $right
	 * @param int    $bottom
	 * @param int    $left
	 * @param int    $width
	 * @param int    $height
	 * @param int    $pageNumber
	 */
	function __construct(
		$search     = null,
		$occurence  = null,
		$top        = null,
		$right      = null,
		$bottom     = null,
		$left       = null,
		$width      = null,
		$height     = null,
		$pageNumber = null
	) {
		$this->Search     = $search;
		$this->Occurence  = $occurence;
		$this->Top        = $top;
		$this->Right      = $right;
		$this->Bottom     = $bottom;
		$this->Left       = $left;
		$this->Width      = $width;
		$this->Height     = $height;
		$this->PageNumber = $pageNumber;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Search"     => $this->Search,
			"Occurence"  => $this->Occurence,
			"Top"        => $this->Top,
			"Right"      => $this->Right,
			"Bottom"     => $this->Bottom,
			"Left"       => $this->Left,
			"Width"      => $this->Width,
			"Height"     => $this->Height,
			"PageNumber" => $this->PageNumber,
		));
	}
}
