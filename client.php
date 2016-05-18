<?php

// Sample object for context property
class ContextSample
{
	public $foo = 'bar';
}
$sampleobject = new ContextSample;

	error_reporting(E_ALL ^ E_NOTICE);
	require_once("signhost.php");

	if(isset ($_POST["createNewTransaction"])) {
		$ondertekenen = new SignHost("https://api.signhost.com", $_POST["appName"], $_POST["appKey"], $_POST["apiKey"]);

		$newTransaction = new Transaction($_FILES["file"]["name"], (bool)$_POST["seal"], $_POST["reference"], $_POST["postbackUrl"], $_POST["sendEmailNotifications"], $_POST["signRequestMode"], $_POST["daysToExpire"], $sampleobject);

		foreach($_POST["signers"] as $signer) {
			$newTransaction->AddSigner(
				$signer["email"],
				$signer["mobile"],
				$signer["iban"],
				(bool)$signer["requireScribble"],
				(bool)$signer["requireScribbleName"],
				(bool)$signer["requireEmailVerification"],
				(bool)$signer["requireSmsVerification"],
				(bool)$signer["requireIdealVerification"],
				(bool)$signer["sendSignRequest"],
				(bool)$signer["sendSignConfirmation"],
				$signer["signRequestMessage"],
				$signer["language"],
				$signer["scribbleName"],
				(bool)$signer["scribbleNameFixed"],
				$signer["reference"],
				$signer["returnUrl"],
				$signer["daysToRemind"],
				$sampleobject);
		}

		if (isset($_POST["receivers"])) {
			foreach($_POST["receivers"] as $receiver) {
				$newTransaction->AddReceiver(
					$receiver["name"],
					$receiver["email"],
					$receiver["message"],
					$receiver["language"],
					$receiver["reference"],
					$sampleobject);
			}
		}

		$transaction = $ondertekenen->CreateTransaction($newTransaction);
		$uploadResponse = $ondertekenen->UploadFileContent($transaction->File->Id, $_FILES["file"]["tmp_name"]);
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ondertekenen.nl HTML Gateway</title>
<style type="text/css">
	td {
		border: 1px solid black;
		word-wrap: break-word;
	}
	th {
		text-align: left;
		border: 1px solid black;
	}
 </style>
</head>

<body>

	<h1>Ondertekenen.nl API</h1>

	<form method="post" enctype="multipart/form-data">

		<fieldset>
			<legend>Transaction</legend>
			<input type="text" name="appName" value="<?php echo $_POST["appName"]; ?>" /> APP Name<br />
			<input type="text" name="appKey" value="<?php echo $_POST["appKey"]; ?>" /> APP Key<br />
			<input type="text" name="apiKey" value="<?php echo $_POST["apiKey"]; ?>" /> API Key<br />
			<input type="text" name="reference" value="" /> Reference<br />
			<input type="text" name="postbackUrl" value="" /> Postback URL<br />
			<input type="text" name="signRequestMode" value="2" /> Sign Request Mode<br />
			<input type="text" name="daysToExpire" value="30" /> Days To Expire<br />
			<input type="text" name="context" value="" placeholder="Array or Object" disabled /> Context (sample object is sent)<br />
			<input type="checkbox" name="sendEmailNotifications" value="1" checked />Send Email Notifications<br /><br />
			<div class="signerFieldsetContainer">

			</div>
			<p><a href="" class="addSignerLink">Add signer</a></p>
			<div class="receiverFieldsetContainer">

			</div>
			<p><a href="" class="addReceiverLink">Add receiver</a></p>
			<p>
				<input type="file" name="file"> <input type="checkbox" name="seal" value="1" checked />Seal
			</p>
			<input type="submit" name="createNewTransaction" value="Create" />
		</fieldset>

	</form>

	<fieldset id="signerFieldset" style="display: none;">
		<legend>Signer <span class="signerNumber">1</span></legend>
		<input type="text" name="signers[0][email]" value="" /> Email Receiver<br />
		<input type="text" name="signers[0][mobile]" value="" /> Mobile Receiver<br />
		<input type="text" name="signers[0][iban]" value="" /> Iban Receiver<br />
		<input type="text" name="signers[0][reference]" value="" /> Reference<br />
		<input type="text" name="signers[0][returnUrl]" value="http://ondertekenen.nl" /> Return URL<br />
		<input type="text" name="signers[0][scribbleName]" value="" /> Scribble Name<br />
		<input type="text" name="signers[0][daysToRemind]" value="15" /> Days To Remind<br />
		<input type="text" name="signers[0][context]" value="" placeholder="Array or Object" disabled /> Context (sample object is sent)<br />
		<p>
			<input type="checkbox" name="signers[0][requireScribble]" value="1" checked />Require Scribble<br />
			<input type="checkbox" name="signers[0][requireScribbleName]" value="1">Require scribble name (if Require scribble is not set)<br/>
			<input type="checkbox" name="signers[0][requireEmailVerification]" value="1" checked />Require Email Verification<br />
			<input type="checkbox" name="signers[0][requireSmsVerification]" value="1" />Require Sms Verification<br />
			<input type="checkbox" name="signers[0][requireIdealVerification]" value="1" />Require Ideal Verification<br />
			<input type="checkbox" name="signers[0][sendSignRequest]" value="1" checked />Send Sign Request<br />
			<input type="checkbox" name="signers[0][sendSignConfirmation]" value="1" checked />Send Sign Confirmation<br />
			<input type="checkbox" name="signers[0][scribbleNameFixed]" value="1" />Scribble Name Fixed<br />
			Language: <select name="signers[0][language]">
				<option value="en-US">English</option>
				<option value="nl-NL">Nederlands</option>
				<option value="de-DE">Deutsch</option>
				<option value="fr-FR">Français</option>
				<option value="it-IT">Italiano</option>
				<option value="es-ES">Español</option>
			</select>
		</p>
		Sign Request Message:<br />
		<textarea name="signers[0][signRequestMessage]" rows="4" cols="50">This is a test sign request.</textarea>
	</fieldset>

	<fieldset id="receiverFieldset" style="display: none;">
		<legend>Receiver <span class="receiverNumber">1</span></legend>
		<input type="text" name="receivers[0][name]" value="" /> Name<br />
		<input type="text" name="receivers[0][email]" value="" /> E-mail<br />
		<input type="text" name="receivers[0][reference]" value="" /> Reference<br />
		<input type="text" name="receivers[0][context]" value="" placeholder="Array or Object" disabled /> Context (sample object is sent)<br />

		<p>
			Language: <select name="receivers[0][language]">
				<option value="en-US">English</option>
				<option value="nl-NL">Nederlands</option>
				<option value="de-DE">Deutsch</option>
				<option value="fr-FR">Français</option>
				<option value="it-IT">Italiano</option>
				<option value="es-ES">Español</option>
			</select>
		</p>
		Message:<br />
		<textarea name="receivers[0][message]" rows="4" cols="50">This is a test message.</textarea>
	</fieldset>

	<pre>
	<?php
		if(isset($newTransaction)) {
			var_dump($newTransaction);
		}
	?>

	<?php
		if(isset($transaction)) {
			var_dump($transaction);
		}
	?>

	<?php
		if(isset($uploadResponse)) {
			var_dump($uploadResponse);
		}
	?>
	</pre>

	<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
	<script>

		var signerCounter = 0;
		var receiverCounter = 0;
		$(function() {
			$('.addSignerLink').on('click', function(e) {
				e.preventDefault();

				signerCounter++;

				var fieldset = $('#signerFieldset').clone();

				$('.signerNumber', fieldset).html(signerCounter);

				$('input, textarea', fieldset).attr('name', function(index, attr) {
					return attr.replace(/\d+/, signerCounter-1);
				});

				fieldset.insertAfter('.signerFieldsetContainer:last').removeAttr('id').show();
			});

			$('.addReceiverLink').on('click', function(e) {
				e.preventDefault();

				receiverCounter++;

				var fieldset = $('#receiverFieldset').clone();

				$('.receiverNumber', fieldset).html(receiverCounter);

				$('input, textarea', fieldset).attr('name', function(index, attr) {
					return attr.replace(/\d+/, receiverCounter-1);
				});

				fieldset.insertAfter('.receiverFieldsetContainer:last').removeAttr('id').show();
			});
		});
	</script>
</body>

</html>
