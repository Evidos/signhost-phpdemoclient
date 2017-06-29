<?php

class SignHost {

    const API_URL = "https://api.signhost.com/api";

	/**
	 * @var string
	 */
	public $AppKey;

	/**
	 * @var string
	 */
	public $ApiKey;

	/**
	 * @var string
	 */
	public $SharedSecret;

    function __construct($appKey, $apiKey, $sharedSecret = null) {
	$this->AppKey = $appKey;
	$this->ApiKey = $apiKey;
	$this->SharedSecret = $sharedSecret;
    }

    public function CreateTransaction($transaction) {
	$ch = curl_init(self::API_URL."/transaction");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$responseJson = curl_exec($ch);
	return json_decode($responseJson);
    }

    public function GetTransaction($transactionId) {
	$ch = curl_init(self::API_URL."/transaction/".$transactionId);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$responseJson = curl_exec($ch);
	return json_decode($responseJson);
    }

    public function DeleteTransaction($transactionId) {
	$ch = curl_init(self::API_URL."/transaction/".$transactionId);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$response = curl_exec($ch);
	return $response;
    }

    public function StartTransaction($transactionId) {
	$ch = curl_init(self::API_URL."/transaction/".$transactionId."/start");
	curl_setopt($ch, CURLOPT_PUT, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$responseJson = curl_exec($ch);
	return json_decode($responseJson);
    }

    public function AddOrReplaceFile($transactionId, $fileId, $filePath) {
	$checksum_file = base64_encode(pack('H*', hash_file('sha256', $filePath)));
	$fh = fopen($filePath, 'r');
	$ch = curl_init(self::API_URL."/transaction/".$transactionId."/file/".$fileId);
	curl_setopt($ch, CURLOPT_PUT, 1);
	curl_setopt($ch, CURLOPT_INFILE, $fh);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/pdf",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey,
	    "Digest: SHA256=".$checksum_file
	));

	$response = curl_exec($ch);
	fclose($fh);
	return $response;
    }

    public function AddOrReplaceMetadata($transactionId, $fileId, $metadata) {
	$ch = curl_init(self::API_URL."/transaction/".$transactionId."/file/".$fileId);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($metadata));
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$response = curl_exec($ch);
	return $response;
    }

    public function GetReceipt($transactionId) {
	$ch = curl_init(self::API_URL."/file/receipt/".$transactionId);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$response = curl_exec($ch);
	return $response;	
	// Returns binary stream
    }

    public function GetDocument($transactionId, $fileId) {
	$ch = curl_init(self::API_URL."/transaction/".$transactionId."/file/".$fileId);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey ".$this->AppKey,
	    "Authorization: APIKey ".$this->ApiKey
	));

	$response = curl_exec($ch);
	return $response;
	// Returns binary stream
    }

    public function ValidateChecksum($masterTransactionId, $fileId, $status, $checksum) {
	return sha1($masterTransactionId."|".$fileId."|".$status."|".$this->SharedSecret) == $checksum;
    }

}

class Transaction {

	/**
	 * @var string
	 */
	public $Id;

	/**
	 * @var <string,FileEntry>[]
	 */
	public $Files;

	/**
	 * @var bool
	 */
	public $Seal;

	/**
	 * @var Signer[]
	 */
	public $Signers;

	/**
	 * @var Receiver[]
	 */
	public $Receivers;

	/**
	 * @var string
	 */
	public $Reference;

	/**
	 * @var string
	 */
	public $PostbackUrl;

	/**
	 * @var integer
	 */
	public $SignRequestMode;

	/**
	 * @var integer
	 */
	public $DaysToExpire;

	/**
	 * @var bool
	 */
	public $SendEmailNotifications;

	/**
	 * @var integer
	 */
	public $Status;

	/**
	 * @var object
	 */
	public $Context;

    function __construct(
	    $seal = false,
	    $signers = array(),
	    $receivers = array(),
	    $reference = null,
	    $postbackUrl = null,
	    $signRequestMode = 2,
	    $daysToExpire = 60,
	    $sendEmailNotifications = false,
	    $context = null) {
	$this->Seal = $seal;
	$this->Signers = $signers;
	$this->Receivers = $receivers;
	$this->Reference = $reference;
	$this->PostbackUrl = $postbackUrl;
	$this->SignRequestMode = $signRequestMode;
	$this->DaysToExpire = $daysToExpire;
	$this->SendEmailNotifications = $sendEmailNotifications;
	$this->Context = $context;
    }

}

class Signer {

	/**
	 * @var string
	 */
	public $Id;

	/**
	 * @var string
	 */
	public $Email;

	/**
	 * @var string
	 */
	public $Mobile;

	/**
	 * @var string
	 */
	public $BSN;

	/**
	 * @var Verification[]
	 */
	public $Verifications;

	/**
	 * @var bool
	 */
	public $SendSignRequest;

	/**
	 * @var string
	 */
	public $SignRequestMessage;

	/**
	 * @var bool
	 */
	public $SendSignConfirmation;

	/**
	 * @var string
	 */
	public $Language;

	/**
	 * @var integer
	 */
	public $DaysToRemind;

	/**
	 * @var string
	 */
	public $Expires;

	/**
	 * @var string
	 */
	public $Reference;

	/**
	 * @var string
	 */
	public $ReturnUrl;

	/**
	 * @var Activity[]
	 */
	public $Activities;

	/**
	 * @var object
	 */
	public $Context;

    function __construct(
	    $email,
	    $id = null,
	    $mobile = null,
	    $bsn = null,
	    $verifications = array(),
	    $sendSignRequest = false,
	    $signRequestMessage = null,
	    $sendSignConfirmation = null,
	    $language = "nl-NL",
	    $daysToRemind = 7,
	    $expires = null,
	    $reference = null,
	    $returnUrl = "https://signhost.com",
	    $context = null) {
	$this->Id = $id;
	$this->Email = $email;
	$this->Mobile = $mobile;
	$this->BSN = $bsn;
	$this->Verifications = $verifications;
	$this->SendSignRequest = $sendSignRequest;
	$this->SendSignRequestMessage = $signRequestMessage;
	$this->SendSignConfirmation = $sendSignConfirmation;
	$this->Language = $language;
	$this->DaysToRemind = $daysToRemind;
	$this->Expires = $expires;
	$this->Reference = $reference;
	$this->ReturnUrl = $returnUrl;
	$this->Context = $context;
    }

}

class Receiver {

	/**
	 * @var string
	 */
	public $Name;

	/**
	 * @var string
	 */
	public $Email;

	/**
	 * @var string
	 */
	public $Language;

	/**
	 * @var string
	 */
	public $Message;

	/**
	 * @var string
	 */
	public $Reference;

	/**
	 * @var object
	 */
	public $Context;

    function __construct(
	    $name,
	    $email,
	    $message,
	    $language = "nl-NL",
	    $reference = null,
	    $context = null) {
	$this->Name = $name;
	$this->Email = $email;
	$this->Language = $language;
	$this->Message = $message;
	$this->Reference = $reference;
	$this->Context = $context;
    }

}

class Verification {

	/**
	 * @var string
	 */
	public $Type;

    function __construct($type) {
	$this->Type = $type;
    }

}

class IdealVerification extends Verification implements JsonSerializable {

	/**
	 * @var string
	 */
	public $Iban;

	/**
	 * @var string
	 */
	public $AccountHolderName;

	/**
	 * @var string
	 */
	public $AccountHolderCity;

    function __construct($iban = null) {
	parent::__construct("iDeal");
	$this->Iban = $iban;
    }

	function jsonSerialize() {
		return array(
			"Type" => $this->Type,
			"Iban" => $this->Iban,
			"AccountHolderName" => $this->AccountHolderName,
			"AccountHolderCity" => $this->AccountHolderCity);
	}

}

class IdinVerification extends Verification implements JsonSerializable {

	/**
	 * @var string
	 */
	public $AccountHolderName;

	/**
	 * @var string
	 */
	public $AccountHolderAddress1;

	/**
	 * @var string
	 */
	public $AccountHolderAddress2;

	/**
	 * @var string
	 */
	public $AccountHolderDateOfBirth;

    function __construct() {
	parent::__construct("iDIN");
    }

	function jsonSerialize() {
		return array(
			"Type" => $this.Type,
			"AccountHolderName" => $this.AccountHolderName,
			"AccountHolderAddress1" => $this.AccountHolderAddress1,
			"AccountHolderAddress2" => $this.AccountHolderAddress2,
			"AccountHolderDataOfBirth" => $this.AccountHolderDateOfBirth);
	}

}

class DigidVerification extends Verification implements JsonSerializable {

	/**
	 * @var string
	 */
	public $Bsn;

	function __construct($bsn = null) {
		parent::__construct("DigiD");
		$this->Bsn = $bsn;
	}

	function jsonSerialize() {
		return array(
			"Type" => $this->Type,
			"Bsn" => $this->Bsn);
	}

}

class KennisnetVerification extends Verification implements JsonSerializable {

	function __construct() {
		parent::__construct("Kennisnet");
	}

	function jsonSerialize() {
		return array("Type" => $this->Type);
	}

}

class SurfnetVerification extends Verification implements JsonSerializable {

	function __construct() {
		parent::__construct("SURFnet");
	}

	function jsonSerialize() {
		return array("Type" => $this->Type);
	}

}

class ScribbleVerification extends Verification implements JsonSerializable {

	/**
	 * @var bool
	 */
	public $RequireHandsignature;

	/**
	 * @var bool
	 */
	public $ScribbleNameFixed;

	/**
	 * @var string
	 */
	public $ScribbleName;

	function __construct($requireHandsignature, $scribbleNameFixed = null, $scribbleName = null) {
		parent::__construct("Scribble");
		$this->RequireHandsignature = $requireHandsignature;
		$this->ScribbleNameFixed = $scribbleNameFixed;
		$this->ScribbleName = $scribbleName;
	}

	function jsonSerialize() {
		return array(
			"Type" => $this->Type,
			"RequireHandsignature" => $this->RequireHandsignature,
			"ScribbleNameFixed" => $this->ScribbleNameFixed,
			"ScribbleName", $this->ScribbleName);
	}

}

class PhoneNumberVerification extends Verification implements JsonSerializable {

	/**
	 * @var string
	 */
	public $Number;

	function __construct($number) {
		parent::__construct("PhoneNumber");
		$this->Number = $number;
	}

	function jsonSerialize() {
		return array(
			"Type" => $this->Type,
			"Number" => $this->Number);
	}

}

class ConsentVerification extends Verification implements JsonSerializable {

	function __construct() {
		parent::__construct("Consent");
	}

	function jsonSerialize() {
		return array("Type" => $this->Type);
	}

}

class EherkenningVerification extends Verification implements JsonSerializable {

	/**
	 * @var string
	 */
	public $EntityConcernIdKvkNr;

	function __construct($entityConcernIdKvkNr = null) {
		parent::__construct("eHerkenning");
		$this->$EntityConcernIdKvkNr = $entityConcernIdKvkNr;
	}

	function jsonSerialize() {
		return array(
			"Type" => $this->Type,
			"EntityConcernIdKvkNr" => $entityConcernIdKvkNr);
	}

}

class Activity {

	/**
	 * @var string
	 */
	public $Id;

	/**
	 * @var integer
	 */
	public $Code;

	/**
	 * @var string
	 */
	public $Info;

	/**
	 * @var string
	 */
	public $CreatedDateTime;

}

class FileEntry {

	/**
	 * @var Link[]
	 */
	public $Links;

	/**
	 * @var string
	 */
	public $DisplayName;

}

class Link {

	/**
	 * @var string
	 */
	public $Rel;

	/**
	 * @var string
	 */
	public $Type;

	/**
	 * @var string
	 */
	public $Link;

}

class FileMetadata {

	/**
	 * @var string
	 */
	public $DisplayName;

	/**
	 * @var integer
	 */
	public $DisplayOrder;

	/**
	 * @var string
	 */
	public $Description;

	/**
	 * @var <string,FormSets>[]
	 */
	public $Signers;

	/**
	 * @var <string,<string,FormSetField>>[]
	 */
	public $FormSets;

    function __construct(
	    $displayName = null,
	    $displayOrder = null,
	    $description = null,
	    $signers = null,
	    $formSets = null) {
	$this->DisplayName = $displayName;
	$this->DisplayOrder = $displayOrder;
	$this->Description = $description;
	$this->Signers = $signers;
	$this->FormSets = $formSets;
    }

}

class FormSets {

	/**
	 * @var string[]
	 */
	public $FormSets;

    function __construct($formSets) {
	$this->FormSets = $formSets;
    }

}

class FormSetField {

    /**
	 * @var string
	 */
	public $Type;

	/**
	 * @var string
	 */
	public $Value;

	/**
	 * @var Location
	 */
	public $Location;

    function __construct($type, $location, $value = null) {
	$this->Type = $type;
	$this->Location = $location;
	$this->Value = $value;
    }

}

class Location {

	/**
	 * @var string
	 */
	public $Search;

	/**
	 * @var integer
	 */
	public $Occurence;

	/**
	 * @var integer
	 */
	public $Top;

	/**
	 * @var integer
	 */
	public $Right;

	/**
	 * @var integer
	 */
	public $Bottom;

	/**
	 * @var integer;
	 */
	public $Left;

	/**
	 * @var integer
	 */
	public $Width;

	/**
	 * @var integer
	 */
	public $Height;

	/**
	 * @var integer
	 */
	public $PageNumber;

    function __construct(
	    $search = null,
	    $occurence = null,
	    $top = null,
	    $right = null,
	    $bottom = null,
	    $left = null,
	    $width = null,
	    $height = null,
	    $pageNumber = null) {
	$this->Search = $search;
	$this->Occurence = $occurence;
	$this->Top = $top;
	$this->Right = $right;
	$this->Bottom = $bottom;
	$this->Left = $left;
	$this->Width = $width;
	$this->Height = $height;
	$this->PageNumber = $pageNumber;
    }

}
