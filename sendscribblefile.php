<?php

require_once("signhost.php");
require_once("vularrays.php");
$emailnamen[0]="John Doe" $emailadres[0]="JohnDoe@mailurl.com"
$emailnamen[1]="Jane Doe" $emailadres[1]="JaneDoe@mailurl.com"

function ZendMail($ArrayNummer){
    global $emailnamen;
    global $emailadres;

    $client = new SignHost("$AppKey1 $AppKey2" ", "$Token");
    $fnaam = $emailnamen[$ArrayNummer].".pdf" ;
    copy ( "scribblepdf.fdp" , $fnaam ) ;
    $transaction = $client->CreateTransaction(CreateSampleTransaction($ArrayNummer));
    $SignerId = $transaction->Signers[0]->Id;
    $FMetadata = new FileMetadata();
    $FMetadata->Signers = array($SignerId => new FormSets(array("$fnaam")));
    $client->AddOrReplaceMetadata($transaction->Id, "$fnaam", $FMetadata);
    $client->AddOrReplaceFile($transaction->Id, "$fnaam", "$fnaam");

    # When everything is setup we can start the transaction flow
    $client->StartTransaction($transaction->Id);
    unlink (  $fnaam ) ;
    sleep(10); # I think it is best that I make a pause. There were some adresses not sent.
}

function CreateSampleTransaction($arrayIndex) {
    global $emailnamen;
    global $emailadres;
    $signer = new Signer( $emailadres[$arrayIndex] );
    $signer->ScribbleName = $emailnamen[$arrayIndex];
    $signer->ScribbleNameFixed = TRUE ;
    $signer->returnUrl = "https://www.ondertekenen.nl/bedankt";
    $signer->RequireScribble = TRUE;
    $signer->SendSignRequest = TRUE;
    $signer->SendEmailNotification = TRUE;
    $signer->SendSignConfirmation = TRUE;
    $signer->Reference = "Ondertekening Toestemmingsverklaring gebruik persoonsgegevens van de Naturistenvereniging Hertogstad" ;
    $signer->IntroText = "
Dear  $emailnamen[$arrayIndex],

Please sign the document
Including the checks

Thx!" ;
    

    $signer->SignRequestMessage = "Dear $emailnamen[$arrayIndex],

Please Sign the document
Thx
Peter" ;
    $receiver= new Receiver ("Peter Pan", "PeterPan@mymail.com","Here is the document");
    $receiver->Reference="For Peter Pan ";
    $transaction = new Transaction();
    $transaction->SendEmailNotifications = TRUE ;
    $transaction->Signers[] = $signer;
    $transaction->Receivers[] = $receiver;

    return $transaction;
}

 ZendMail (0);
 ZendMail (1);

# for ($x = 0; $x <= 1; $x++) { if ($emailnamen[$x] != "") ZendMail ($x) ;}


