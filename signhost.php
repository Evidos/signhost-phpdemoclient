<?php
class SignHost {
	public $AppKey;
	public $ApiKey;
	public $SharedSecret;
	public $ApiEndpoint;

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

	public function CreateTransaction($transaction) {
		$ch = curl_init($this->ApiEndpoint."/transaction");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$responseJson = curl_exec($ch);
		curl_close($ch);

		return json_decode($responseJson);
	}

	public function GetTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$responseJson = curl_exec($ch);
		curl_close($ch);

		return json_decode($responseJson);
	}

	public function DeleteTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public function StartTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/start");
		curl_setopt($ch, CURLOPT_PUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$responseJson = curl_exec($ch);
		curl_close($ch);

		return json_decode($responseJson);
	}

	public function AddOrReplaceFile($transactionId, $fileId, $filePath) {
		$checksum_file = base64_encode(pack('H*', hash_file('sha256', $filePath)));
		$fh = fopen($filePath, 'r');
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
		curl_setopt($ch, CURLOPT_PUT, 1);
		curl_setopt($ch, CURLOPT_INFILE, $fh);
		curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
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

	public function AddOrReplaceMetadata($transactionId, $fileId, $metadata) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($metadata));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	public function GetReceipt($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/file/receipt/".$transactionId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);

		// Returns binary stream
		return $response;
	}

	public function GetDocument($transactionId, $fileId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId."/file/".rawurlencode($fileId));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = curl_exec($ch);
		curl_close($ch);

		// Returns binary stream
		return $response;
	}

	public function ValidateChecksum($masterTransactionId, $fileId, $status, $remoteChecksum) {
		$localChecksum = sha1($masterTransactionId."|".$fileId."|".$status."|".$this->SharedSecret);

		if (strlen($localChecksum) !== strlen($remoteChecksum)) {
			return false;
		}

		return hash_equals($localChecksum, $remoteChecksum);
	}
}

class Transaction implements JsonSerializable {
	public $Seal; // Boolean
	public $Signers; // Array of Signer
	public $Receivers; // Array of Receiver
	public $Reference; // String
	public $PostbackUrl; // String
	public $SignRequestMode; // Integer
	public $DaysToExpire; // Integer
	public $SendEmailNotifications; // Boolean
	public $Context; // Any object

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
	public $Id; // String
	public $Email; // String
	public $Verifications; // Array of Verification
	public $SendSignRequest; // Boolean
	public $SignRequestMessage; // String
	public $SendSignConfirmation; // Boolean
	public $Language; // String (enum)
	public $ScribbleName; // String
	public $DaysToRemind; // Integer
	public $Expires; // String
	public $Reference; // String
	public $ReturnUrl; // String
	public $Context; // Any object

	function __construct(
		$email,
		$id                   = null,
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
	public $Type; // String (enum)

	function __construct($type) {
		$this->Type = $type;
	}
}

class IDealVerification extends Verification implements JsonSerializable {
	public $Iban; // String

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
	public $Bsn; // String

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
	public $RequireHandsignature; // Bool
	public $ScribbleNameFixed; // Bool
	public $ScribbleName; // String

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
	public $Number; // String

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
	public $EntityConcernIdKvkNr;

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
	public $PhoneNumber;

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
	public $DisplayName; // String
	public $DisplayOrder; // Integer
	public $Description; // String
	public $Signers; // Map of <String,FormSets>
	public $FormSets; // Map of <String,Map of <String,FormSetField>>

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
	public $FormSets; // Array of String

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
	public $Type; // String (enum)
	public $Value; // String
	public $Location; // Location

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
