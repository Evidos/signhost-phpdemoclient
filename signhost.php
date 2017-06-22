<?php

class SignHost {

    const API_URL = "https://api.signhost.com/api";

    public $AppKey;
    public $ApiKey;
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
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

    public $Id; // String
    public $Files; // Map of <String,FileEntry>
    public $Seal; // Boolean
    public $Signers; // Array of Signer
    public $Receivers; // Array of Receiver
    public $Reference; // String
    public $PostbackUrl; // String
    public $SignRequestMode; // Integer
    public $DaysToExpire; // Integer
    public $SendEmailNotifications; // Boolean
    public $Status; // Integer (enum)
    public $Context; // Any object

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

    public $Id; // String
    public $Email; // String
    public $Mobile; // String
    public $BSN; // String
    public $RequireScribble; // Boolean
    public $RequireSmsVerification; // Boolean
    public $RequireDigidVerification; // Boolean
    public $RequireKennisnetVerification; // Boolean
    public $RequireSurfnetVerification; // Boolean
    public $Verifications; // Array of Verification
    public $SendSignRequest; // Boolean
    public $SignRequestMessage; // String
    public $SendSignConfirmation; // Boolean
    public $Language; // String (enum)
    public $ScribbleName; // String
    public $ScribbleNameFixed; // Boolean
    public $DaysToRemind; // Integer
    public $Expires; // String
    public $Reference; // String
    public $ReturnUrl; // String
    public $Activities; // Array of Activity
    public $Context; // Any object

    function __construct(
	    $email,
	    $id = null,
	    $mobile = null,
	    $bsn = null,
	    $requireScribble = false,
	    $requireSmsVerification = false,
	    $requireDigidVerification = false,
	    $requireKennisnetVerification = false,
	    $requireSurfnetVerification = false,
	    $verifications = array(),
	    $sendSignRequest = false,
	    $signRequestMessage = null,
	    $sendSignConfirmation = null,
	    $language = "nl-NL",
	    $scribbleName = null,
	    $scribbleNameFixed = false,
	    $daysToRemind = 7,
	    $expires = null,
	    $reference = null,
	    $returnUrl = "https://signhost.com",
	    $context = null) {
	$this->Id = $id;
	$this->Email = $email;
	$this->Mobile = $mobile;
	$this->BSN = $bsn;
	$this->RequireScribble = $requireScribble;
	$this->RequireSmsVerification = $requireSmsVerification;
	$this->RequireDigidVerification = $requireDigidVerification;
	$this->RequireKennisnetVerification = $requireKennisnetVerification;
	$this->RequireSurfnetVerification = $requireSurfnetVerification;
	$this->Verifications = $verifications;
	$this->SendSignRequest = $sendSignRequest;
	$this->SendSignRequestMessage = $signRequestMessage;
	$this->SendSignConfirmation = $sendSignConfirmation;
	$this->Language = $language;
	$this->ScribbleName = $scribbleName;
	$this->ScribbleNameFixed = $scribbleNameFixed;
	$this->DaysToRemind = $daysToRemind;
	$this->Expires = $expires;
	$this->Reference = $reference;
	$this->ReturnUrl = $returnUrl;
	$this->Context = $context;
    }

}

class Receiver {

    public $Name; // String
    public $Email; // String
    public $Language; // String (enum)
    public $Message; // String
    public $Reference; // String
    public $Context; // Any object

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

    public $Type; // String (enum)

    function __construct($type) {
	$this->Type = $type;
    }

}

class IdealVerification extends Verification implements JsonSerializable {

    public $Iban; // String
    public $AccountHolderName; // String
    public $AccountHolderCity; // String

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

    public $AccountHolderName; // String
    public $AccountHolderAddress1; // String
    public $AccountHolderAddress2; // String
    public $AccountHolderDateOfBirth; // String

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

    public $Bsn; // String

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

    public $RequireHandsignature; // Bool
	public $ScribbleNameFixed; // Bool
	public $ScribbleName; // String

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

    public $Number; // String

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

    public $Id; // String
    public $Code; // Integer (enum)
    public $Info; // String
    public $CreatedDateTime; // String

}

class FileEntry {

    public $Links; // Array of Link
    public $DisplayName; // String

}

class Link {

    public $Rel; // String (enum)
    public $Type; // String
    public $Link; // String

}

class FileMetadata {

    public $DisplayName; // String
    public $DisplayOrder; // Integer
    public $Description; // String
    public $Signers; // Map of <String,FormSets>
    public $FormSets; // Map of <String,Map of <String,FormSetField>>

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

    public $FormSets; // Array of String

    function __construct($formSets) {
	$this->FormSets = $formSets;
    }

}

class FormSetField {

    public $Type; // String (enum)
    public $Value; // String
    public $Location; // Location

    function __construct($type, $location, $value = null) {
	$this->Type = $type;
	$this->Location = $location;
	$this->Value = $value;
    }

}

class Location {

    public $Search; // String
    public $Occurence; // Integer
    public $Top; // Integer
    public $Right; // Integer
    public $Bottom; // Integer
    public $Left; // Integer
    public $Width; // Integer
    public $Height; // Integer
    public $PageNumber; // Integer

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
