<?php

class SignHost {

    const API_URL = "https://api.signhost.com/api/";

    public $AppKey;
    public $ApiKey;
    public $SharedSecret;

    function __construct($appKey, $apiKey, $sharedSecret = null) {
	$this->AppKey = $appKey;
	$this->ApiKey = $apiKey;
	$this->SharedSecret = $sharedSecret;
    }

    public function CreateTransaction($transaction) {
	$ch = curl_init(self::API_URL . "transaction");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey " . $this->AppKey,
	    "Authorization: APIKey " . $this->ApiKey));
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	$responseJson = curl_exec($ch);
	return json_decode($responseJson);
    }

    public function GetTransaction($transactionId) {
	$ch = curl_init(self::API_URL . "transaction/" . $transactionId);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey " . $this->AppKey,
	    "Authorization: APIKey " . $this->ApiKey));
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	$responseJson = curl_exec($ch);
	return json_decode($responseJson);
    }

    public function DeleteTransaction($transactionId) {
	$ch = curl_init(self::API_URL . "transaction/" . $transactionId);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/json",
	    "Application: APPKey " . $this->AppKey,
	    "Authorization: APIKey " . $this->ApiKey));
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	$responseJson = curl_exec($ch);
	return json_decode($responseJson);
    }

    public function StartTransaction() {
	// To be implemented
    }

    public function AddOrReplaceFile($transactionId, $fileId, $filePath) {
	$fh = fopen($filePath, 'r');
	$ch = curl_init(self::API_URL . "transaction" . $transactionId . "file" . $fileId);
	curl_setopt($ch, CURLOPT_PUT, 1);
	curl_setopt($ch, CURLOPT_INFILE, $fh);
	curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	    "Content-Type: application/pdf",
	    "Application: APPKey " . $this->AppKey,
	    "Authorization: APIKey " . $this->ApiKey));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

	$response = curl_exec($ch);

	fclose($fh);
	return $response;
    }

    public function AddOrReplaceMetadata() {
	// To be implemented
    }

    public function GetReceipt() {
	// To be implemented
    }

    public function GetDocument() {
	// To be implemented
    }

    public function ValidateChecksum($masterTransactionId, $fileId, $status, $checksum) {
	return sha1($masterTransactionId . "|" . $fileId . "|" . $status . "|" . $this->SharedSecret) == $checksum;
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
    public $SendSignRequestMessage; // String
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

}

class Receiver {

    public $Name; // String
    public $Email; // String
    public $Language; // String (enum)
    public $Message; // String
    public $Reference; // String
    public $Activities; // Array of Activity
    public $Context; // Any object

}

class Verification {

    public $Type; // String (enum)

    function __construct($type) {
	$this->Type = $type;
    }

}

class iDEAL extends Verification {

    public $Iban; // String
    public $AccountHolderName; // String
    public $AccountHolderCity; // String

    function __construct($type, $iban, $accountHolderName, $accountHolderCity) {
	parent::__construct($type);

	$this->Iban = $iban;
	$this->AccountHolderName = $accountHolderName;
	$this->AccountHolderCity = $accountHolderCity;
    }

}

class iDIN extends Verification {

    public $AccountHolderName; // String
    public $AccountHolderAddress1; // String
    public $AccountHolderAddress2; // String
    public $AccountHolderDateOfBirth; // String

    function __construct(
	    $type,
	    $accountHolderName,
	    $accountHolderAddress1,
	    $accountHolderAddress2,
	    $accountHolderDateOfBirth) {
	parent::__construct($type);

	$this->AccountHolderName = $accountHolderName;
	$this->AccountHolderAddress1 = $accountHolderAddress1;
	$this->AccountHolderAddress2 = $accountHolderAddress2;
	$this->AccountHolderDateOfBirth = $accountHolderDateOfBirth;
    }

}

class FileEntry {

    public $Link; // Array of Link
    public $DisplayName; // String

}

class Link {

    public $Rel; // String (enum)
    public $Type; // String
    public $Link; // String

}

class FileMetaData {

    public $DisplayName; // String
    public $Signers; // Map of <String,FormSets>
    public $FormSets; // Map of <String,Map of <String,FormSetField>>

}

class FormSets {

    public $FormSets; // Array of String

}

class FormSetField {

    public $Type; // String (enum)
    public $Location; // Location

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

}
