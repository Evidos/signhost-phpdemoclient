<?php
require_once("response.php");
require_once("DTO/consentverification.php");
require_once("DTO/digidverification.php");
require_once("DTO/eherkenningverification.php");
require_once("DTO/eidasloginverification.php");
require_once("DTO/filemetadata.php");
require_once("DTO/formsetfield.php");
require_once("DTO/formsets.php");
require_once("DTO/idealverification.php");
require_once("DTO/idinverification.php");
require_once("DTO/itsmeidentificationverification.php");
require_once("DTO/itsmesignverification.php");
require_once("DTO/location.php");
require_once("DTO/phonenumberverification.php");
require_once("DTO/receiver.php");
require_once("DTO/scribbleverification.php");
require_once("DTO/signer.php");
require_once("DTO/signingcertificateverification.php");
require_once("DTO/surfnetverification.php");
require_once("DTO/transaction.php");
require_once("DTO/verification.php");

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
	 * @return Response
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

		$response = new Response($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Gets an exisiting transaction by providing a transaction ID.
	 *
	 * When the response has a status code of 410, you can still retrieve
	 * partial historical data from the JSON in the error message property.
	 * @param string $transactionId
	 * @return Response
	 */
	public function GetTransaction($transactionId) {
		$ch = curl_init($this->ApiEndpoint."/transaction/".$transactionId);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Accept: application/vnd.signhost.".self::API_VERSION."+json",
			"Application: APPKey ".$this->AppKey,
			"Authorization: APIKey ".$this->ApiKey,
		));

		$response = new Response($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Deletes an exisiting transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @return Response
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

		$response = new Response($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Starts an exisiting transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @return Response
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

		$response = new Response($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Add a file to an existing transaction by providing a file path
	 * and a transaction ID.
	 * @param string $transactionId
	 * @param string $fileId
	 * @param string $filePath
	 * @return Response
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

		$response = new Response($ch);
		curl_close($ch);
		fclose($fh);

		return $response;
	}

	/**
	 * Adds file metadata for a file to an existing transaction by providing a transaction ID.
	 * @param string       $transactionId
	 * @param string       $fileId
	 * @param FileMetadata $metadata
	 * @return Response
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

		$response = new Response($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Gets the receipt of a finished transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @return Response
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

		$response = new Response($ch);
		curl_close($ch);

		return $response;
	}

	/**
	 * Gets the document of a transaction by providing a transaction ID.
	 * @param string $transactionId
	 * @param string $fileId
	 * @return Response
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

		$response = new Response($ch);
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
