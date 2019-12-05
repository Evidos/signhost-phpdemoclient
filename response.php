<?php
class Response
{
	/** @var bool */
	public $IsSuccess;

	/** @var int */
	public $StatusCode;

	/** @var object */
	public $Content;

	/** @var string */
	public $ErrorMessage;

	/**
	 * @param object $ch An unclosed cURL resource.
	 */
	function __construct($ch) {
		$response = curl_exec($ch);
		$this->StatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($response === false) {
			$this->IsSuccess = false;
			$this->ErrorMessage = curl_error($ch);
		}
		else {
			$this->IsSuccess =
				$this->StatusCode >= 200 &&
				$this->StatusCode < 300;

			if ($this->IsSuccess) {
				$this->Content = $response;
			}
			else {
				$this->ErrorMessage = $this->GetErrorMessage($response);
			}
		}
	}

	function GetErrorMessage($response) {
		if (!is_string($response)) {
			return "An unknown error occured.";
		}

		$error = json_decode($response);
		if (
			json_last_error() == JSON_ERROR_NONE &&
			property_exists($error, "Message")
		) {
			return $error->Message;
		}

		return $response;
	}
}
