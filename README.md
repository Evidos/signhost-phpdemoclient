# Signhost client library

This is a client library in PHP to demonstrate the usage of the [signhost api](https://api.signhost.com/) using PHP.
You will need a valid APPKey and APIKey.
You can request a APPKey for signhost at [ondertekenen.nl](https://www.ondertekenen.nl/api-proefversie/).

```php
require_once("signhost.php");

$client = new SignHost("AppName appkey", "apikey");

$transaction = $client->CreateTransaction(CreateSampleTransaction());

$client->AddOrReplaceFile($transaction->Id, "First Document", "PathToFile");
$client->AddOrReplaceFile($transaction->Id, "General Agreement", "PathOtherFile");

# When everything is setup we can start the transaction flow
$client->StartTransaction($transaction->Id);

function CreateSampleTransaction() {
    $signer = new Signer("john.doe@example.com");	    
    $signer->ScribbleName = "John Doe";
    $signer->SignRequestMessage = "Could you please sign this document?";
    $signer->SendSignRequest = true;

    $transaction = new Transaction();
    $transaction->Signers[] = $signer;

    return $transaction;
}
```
