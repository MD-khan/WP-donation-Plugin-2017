<?php
/*
Recognized fields. The ones with * are required to be passed.

ChapterId*
Salutation - if not provided, it defaults to Mr. or Ms. based on Gender, if provided
FirstName*
MiddleName
LastName*
Gender
Email*
MobilePhone
HomePhone
AddressLine1
AddressLine2
City
State
Zip
PaymentFrequency - Defaults to 0=one-time
PaymentTerms - Defaults to 1=One payment
PaymentMethod*
CreditCardNumber*
NameOnCreditCard*
CreditCardExpirationMonth*
CreditCardExpirationYear*
Amount*
FormId - Defaults to 103=Donation Form
Description - Defaults to "Charitable Donation" if FormId=103 or "Payment to MAS" otherwise
ItemId - Defaults to 9=Mony Donation
DepartmentId - Defaults to 7=General
FundId - Defaults to 4=General
InitiativeId - Defaults to 12934=Online Donation
AccountId*
Notes
OpportunityId - Defaults to 0 as "unspecified"
PostbackUrl
SuccessUrl
FailUrl
recaptcha_challenge_field
recaptcha_response_field

 */
// Needed for the ReCaptcha
//session_start();

//To calculate load time
//$time = microtime();
//$time = explode(' ', $time);
//$time = $time[1] + $time[0];
//$start = $time;
$start = getTimeInSeconds();
//echo "<XMP>";
//var_dump($_SERVER);
//echo "</XMP>";
//exit();

// Validate the request

if (!((stripos($_SERVER["HTTP_REFERER"], "69.195.100.246") !== false or
    stripos($_SERVER["HTTP_REFERER"], "isbcc.org") !== false or
    stripos($_SERVER["HTTP_REFERER"], "masgreaterla.org") !== false)
        
    //and
    //stripos($_SERVER["HTTP_REFERER"], "member_info_form.php") !== false
)
) {
    //echo "<p>Error!<br>Unauthorized requester: " . $_SERVER["HTTP_REFERER"] . "</p>";
    echo "0|Error! Unauthorized requester: " . $_SERVER["HTTP_REFERER"] ;
    exit();
}

//$body = "\n\nREQUEST";
//foreach ($_REQUEST as $key => $value) { $body .= "\n$key: $value"; }

//echo "<xmp>";
//var_dump($_REQUEST);
//echo "</xmp>";
//exit();

//echo "<html><head><title>Processing...</title></head><body><div align='center' id='ProcessingDiv'><img src='/donate/images/wait.gif' alt='Please wait!'><b>Processing...</b><br>Please wait!<br>Thank you!</div>";

if (isset($_POST["recaptcha_challenge_field"]) && isset($_POST["recaptcha_response_field"])) {

    //echo $_POST["recaptcha_response_field"] . " = " . $_SESSION[$_POST["recaptcha_response_field"]];

    if ($_SESSION[$_POST["recaptcha_response_field"]] != "success") {
        $body = "\n\nREQUEST";
        foreach ($_REQUEST as $key => $value) {
            if ($key == 'CreditCardNumber') {
                if (strlen($value) > 8)
                    $value = substr($value, 0, 4) . str_repeat("X", strlen($value) - 8) . substr($value, strlen($value) - 4, 4);
            }
            $body .= "\n$key: $value";
        }

        $body .= "\nError Msg: Captucha Error!";
        $body .= "\nLog: \n$logMsg";
        $body .= "\n\nUser IP: " . getRealIpAddr();

        mail("loaytassaf@yahoo.com", "NS: Online Payment Submission - Captcha Error", $body, "From: fundraising@muslimamericansociety.org \r\n");

        die ("0|Security words validation failed! Please go back and try again.");
    }
    /*
        require_once('recaptcha/recaptchalib.php');

        $privatekey = "6LeTQMwSAAAAAGBBIHzbVeD4FyA-8cltpH-QVDB_";
        $resp = recaptcha_check_answer ($privatekey,
            $_SERVER["REMOTE_ADDR"],
            $_POST["recaptcha_challenge_field"],
            $_POST["recaptcha_response_field"]);

        if (!$resp->is_valid) {
        // What happens when the CAPTCHA was entered incorrectly
            die ("Security words validation failed! Please go back and try again.");
        } else {
        // Your code here to handle a successful verification
            echo "<h1>SUCCESS!</h1>";
        }
    */
}

// Update member's record
//require_once '../membership/netsuite/PHPtoolkit.php';
include 'https://www.muslimamericansociety.org/membership/netsuite/PHPtoolkit.php';
include 'https://www.muslimamericansociety.org/membership/netsuite/login_info.php';

global $myNSclient;
$logMsg = "";

$ChapterId = $_POST['ChapterId'];
ValidateRequiredField($ChapterId, "ChapterId");
$Salutation = $_POST['Salutation'];
$FirstName = stripcslashes(trim($_POST['FirstName']));
ValidateRequiredField($FirstName, "FirstName");
$MiddleName = stripcslashes(trim($_POST['MiddleName']));
$LastName = stripcslashes(trim($_POST['LastName']));
ValidateRequiredField($LastName, "LastName");

// Fraud transaction filter
if($FirstName =='Samuel' && $LastName == 'Pugh')die('0|Failed');
if(strtolower($LastName) == 'filho')die('0|Failed');

$FullName = trim($FirstName . ' ' . $MiddleName) . ' ' . $LastName;

$Gender = $_POST['Gender']; // Can be 1=Female, 2=Male or null.
switch(strtolower($Gender)){
    case 'f':
    case 'female':
        $Gender = 1;
        break;
    case 'm':
    case 'male':
        $Gender = 2;
        break;
}

if ($Salutation == '') {
    if ($Gender == 1 /* Female */) $Salutation = 'Ms.';
    elseif ($Gender == 2 /* Male */) $Salutation = 'Mr.';
}

$CompanyName = stripcslashes(trim($_POST['CompanyName']));
$IsBusiness = $_POST['Business'];

$Email = trim($_POST['Email']);
ValidateRequiredField($Email, "Email");
$MobilePhone = trim($_POST['MobilePhone']);
$HomePhone = trim($_POST['HomePhone']);
$HomeAddress1 = trim(stripslashes($_POST['AddressLine1']));
$HomeAddress2 = trim(stripslashes($_POST['AddressLine2']));
$HomeCity = trim(stripslashes($_POST['City']));
$HomeState = trim($_POST['State']);
$HomeZip = trim($_POST['Zip']);

$PaymentFrequency = $_POST['PaymentFrequency']; // One-time = 0; Monthly = 1; Quarterly = 3; Semi-annually = 6; Annually = 12; ...
if (!isset($PaymentFrequency) || $PaymentFrequency < 0 || $PaymentFrequency == null) $PaymentFrequency = 0; // default one-time

$PaymentTerms = 1; // default to one payment
if (isset($_POST['PaymentTerms'])) $PaymentTerms = $_POST['PaymentTerms']; // Number of payments = -1 for ongoing, 1 for one-time, 2, 3, 4, ...
if (!isset($PaymentTerms) || $PaymentTerms == 0 || $PaymentTerms == null) $PaymentTerms = 1; // default to one payment

$PaymentMethod = $_POST['PaymentMethod']; // VISA, American Express, Discover, Master Card
ValidateRequiredField($PaymentMethod, "PaymentMethod");

$CreditCardNumber = $_POST['CreditCardNumber'];
//ValidateRequiredField($CreditCardNumber, "CreditCardNumber");
$NameOnCreditCard = $_POST['NameOnCreditCard'];
if (!isset($NameOnCreditCard) || $NameOnCreditCard == null) $NameOnCreditCard = $FullName; // Use full name by default
$CreditCardExpirationMonth = $_POST['CreditCardExpirationMonth'];
//ValidateRequiredField($CreditCardExpirationMonth, "CreditCardExpirationMonth");
$CreditCardExpirationYear = $_POST['CreditCardExpirationYear'];

$SaveCC = false;
$SaveCC = trim($_POST["SaveCC"]);

// Validate only if Credit Card payment method
switch($PaymentMethod) {
    case 3:
    case 4:
    case 5:
    case 6:
        ValidateRequiredField($CreditCardExpirationYear, "CreditCardExpirationYear");
        ValidateRequiredField($CreditCardNumber, "CreditCardNumber");
        ValidateRequiredField($CreditCardExpirationMonth, "CreditCardExpirationMonth");
        break;
}

$CreditCardExpiration = date("c", mktime(0, 0, 0, $CreditCardExpirationMonth, 15, $CreditCardExpirationYear));

$Amount = trim($_POST['Amount']);
ValidateRequiredField($Amount, "Amount");

$FormId = $_POST['FormId']; // 103 - Donation Form, 108 - Payment Form
if (!isset($FormId) || $FormId == 0) $FormId = 103; // Default to Donation Form

$Description = stripslashes($_POST['Description']);
if (!isset($Description) || $Description == null) $Description = ($FormId == 103 ? "Charitable Donation" : "Payment to MAS");

$ItemId = $_POST['ItemId']; // 5 = Membership Dues, 9 = Money Donation, 23 = Registration Fees
if (!isset($ItemId) || $ItemId == 0) $ItemId = 9; // Default to Money Donation

$DepartmentId = $_POST['DepartmentId']; // 3 = Membership, 7 = General
if (!isset($DepartmentId) || $DepartmentId == 0) $DepartmentId = 7; // Default to General

$FundId = $_POST['FundId']; // 4 = General
if (!isset($FundId) || $FundId == 0) $FundId = 4; // Default to General

$InitiativeId = $_POST['InitiativeId'];
if (!isset($InitiativeId) || $InitiativeId == 0) $InitiativeId = 13934; // Default to 'Online Donation'

$AccountId = $_POST['AccountId'];
ValidateRequiredField($AccountId, "AccountId");

$Notes = stripslashes(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', iconv("UTF-8", "UTF-8//IGNORE", $_POST['Notes']))));

$CheckNumber = stripslashes(trim($_POST['CheckNumber']));

$OpportunityId = $_POST['OpportunityId']; // 4 = General
if (!isset($OpportunityId) || $OpportunityId == 0) $OpportunityId = 0; // Default to 0 as 'unspeficied'

// Find the donor/customer
$constSearch = new nsComplexObject("CustomerSearchBasic");
$constSearch->setFields(array(
    "firstName" => array("operator" => "is", "searchValue" => $FirstName),
    "email" => array("operator" => "is", "searchValue" => $Email),
    "isInactive" => array("operator" => "is", "searchValue" => false),
    "subsidiary" => array("operator" => "anyOf", "searchValue" => new nsComplexObject('RecordRef', array('internalId' => $ChapterId)))
));

$searchResponse = $myNSclient->search($constSearch);

//var_dump($searchResponse);
//var_dump(isset($searchResponse));
//var_dump(count($searchResponse->recordList));
//var_dump((isset($searchResponse) && count($searchResponse->recordList) > 0));
//exit;

$logMsg .= "\n\n Searching for constituent resulted in:";
if (isset($searchResponse) && count($searchResponse->recordList) > 0) {
    $logMsg .= "\nTotal Records Found: " . count($searchResponse->recordList);
    $CustId = $searchResponse->recordList[0]->getField('internalId');
    $logMsg .= "\nFound Customer Id = $CustId";
} else {
    $logMsg .= "\nCannot find any matches.";
    $CustId = 0;
}
$logMsg .= "\nSeconds lapsed after searching constituent: " . round((getTimeInSeconds() - $start), 4);


// Prepare to add or update the customer record
$customerFields = array();
if ($Salutation != "") $customerFields['salutation'] = $Salutation;
if ($FirstName != "") $customerFields['firstName'] = $FirstName;
if ($MiddleName != "") $customerFields['middleName'] = $MiddleName;
if ($LastName != "") $customerFields['lastName'] = $LastName;
if ($CompanyName != "") $customerFields['companyName'] = $CompanyName;
if ($Email != "") $customerFields['email'] = $Email;
if ($HomePhone != "") $customerFields['homePhone'] = $HomePhone;
if ($MobilePhone != "") $customerFields['mobilePhone'] = $MobilePhone;

if ($IsBusiness == 1) $customerFields['isPerson'] = 'F';
//else $customerFields['isPerson'] = 'T';

// If city and state provided but not the street address, then don't make it default
if($HomeAddress1 == "" && $HomeCity != "" &&  $HomeState != ""){
    $customerFields['addressbookList'] = array(
        'replaceAll' => 'T',
        'addressbook' => array(
            'defaultShipping' => 'F',
            'defaultBilling' => 'F',
            'isResidential' => 'T',
            'label' => "Home address updated online on " . date("n/j/Y"),
            'addressee' => trim($Salutation . " " . $FullName),
            'addr1' => $HomeAddress1,
            'addr2' => $HomeAddress2,
            'city' => $HomeCity,
            'state' => $HomeState,
            'zip' => $HomeZip
        )
    );
}elseif($HomeAddress1 != "" && $HomeCity != "" &&  $HomeState != "" && $HomeZip != ""){
    $customerFields['addressbookList'] = array(
        'replaceAll' => 'T',
        'addressbook' => array(
            'defaultShipping' => 'T',
            'defaultBilling' => 'T',
            'isResidential' => 'T',
            'label' => "Home address updated online on " . date("n/j/Y"),
            'addressee' => trim($Salutation . " " . $FullName),
            'addr1' => $HomeAddress1,
            'addr2' => $HomeAddress2,
            'city' => $HomeCity,
            'state' => $HomeState,
            'zip' => $HomeZip
        )
    );
}

$customerFields['customFieldList'] = array(
    new nsComplexObject('SelectCustomFieldRef', array('value' => new nsListOrRecordRef(array('internalId' => 1)), 'internalId' => 'custentity_nso_f_donortype')), // Individual
);


if ($Gender != "" && ($Gender == 1 || $Gender == 2)) {
    $customerFields['customFieldList'][] = new nsComplexObject('SelectCustomFieldRef', array('value' => new nsListOrRecordRef(array('internalId' => $Gender)), 'internalId' => 'custentity_nso_f_gender'));
}

switch($PaymentMethod) {
    case 3:
    case 4:
    case 5:
    case 6:
        if ($SaveCC){
            $customerFields['creditCardsList'] = array(
                'replaceAll' => 'F',
                'creditCards' => array(
                    "ccDefault" => "T",
                    "ccExpireDate" => $CreditCardExpiration,
                    "ccMemo" => "Provided online on " . date("n/j/Y"),
                    "ccName" => $NameOnCreditCard,
                    "ccNumber" => $CreditCardNumber,
                    "paymentMethod" => array('internalId' => $PaymentMethod)
                )
            );
        }
    break;
}

if ($CustId > 0) {
    // Found a constituent... Update their record
    $logMsg .= "\n\nUpdating constituent record...";
    $customerFields['internalId'] = $CustId;
    $customer = new nsComplexObject("Customer", $customerFields);
    $updateResponse = $myNSclient->update($customer);
    $logMsg .= "\nSeconds lapsed after updateing constituent: " . round((getTimeInSeconds() - $start), 4);
} else {
    // Cannot find a constituent... Create a new record
    $logMsg .= "\n\nCreating new constituent record...";
    $customerFields['subsidiary'] = array('internalId' => $ChapterId);
    $customer = new nsComplexObject("Customer", $customerFields);
    $updateResponse = $myNSclient->add($customer);
    $logMsg .= "\nSeconds lapsed after creating constituent: " . round((getTimeInSeconds() - $start), 4);
}

//echo "<xmp>";
//var_dump($customer);
//echo "</xmp>";
//exit();
//echo ("\nupdateResponse = \n");
//var_dump($updateResponse);

if (!$updateResponse->isSuccess) {
    $success = false;
    $errorMsg = "Error updating constituent information!";
    $logMsg .= "\nFailed!\nDetails:" . $updateResponse->statusDetail[0]->message;
} else {
    $success = true;
    $CustId = $updateResponse->recordRef->getField('internalId');
    $logMsg .= "\nSuccessful!\nCustId = $CustId";
}
$logMsg .= "\nSeconds lapsed after updateing/creating constituent: " . round((getTimeInSeconds() - $start), 4);


//"Online Payment: " . $memo . " x " . $PaymentTerms
// If recurring payment, set up a memorized transaction
switch ($PaymentFrequency) {
    case '0.03':
    case '.03':
        $memo = "MEMORIZE: Daily Payment";
        break;
    case '0.25':
    case '.25':
        $memo = "MEMORIZE: Weekly Payment";
        break;
    case '1':
        $memo = "MEMORIZE: Monthly Payment";
        break;
    case '3':
        $memo = "MEMORIZE: Quarterly Payment";
        break;
    case '6':
        $memo = "MEMORIZE: Semi-annual Payment";
        break;
    case '12':
        $memo = "MEMORIZE: Annual Payment";
        break;
    default:
        $memo = "One-time payment";
}

if ($PaymentFrequency > 0 && $PaymentTerms == -1) $memo .= " ongoing";
if ($PaymentFrequency > 0 && $PaymentTerms > 0) $memo .= " x $PaymentTerms";

if (strlen($Notes) > 0) $memo .= " | Payer's note: $Notes";

if ($success) {

    // For testing purposes only!
    if (isset($_REQUEST["TestingFlag"]) && $_REQUEST["TestingFlag"] == 1) {
        $PaymentMethod = 1; // Cash so the operation completes.
        $memo .= " | TestingFlag=1";
    }

    $cashSaleFields = array(
        'entity' => new nsRecordRef(array('internalId' => $CustId)),
        'paymentMethod' => new nsRecordRef(array('internalId' => $PaymentMethod)),
       // 'creditcardprocessor' =>
        'ccNumber' => $CreditCardNumber,
        'ccExpireDate' => $CreditCardExpiration,
        'ccName' => $NameOnCreditCard,
        'ccStreet' => $HomeAddress1,
        'ccZipCode' => $HomeZip,
        //'ccApproved'	=> 'F',
        'chargeIt' => 'T',
        'memo' => $memo,
        'email' => $Email,
        'toBeEmailed' => true,
        'undepFunds' => false,
        'account' => new nsRecordRef(array('internalId' => $AccountId)),
        'customForm' => new nsRecordRef(array('internalId' => $FormId)),
        'itemList' => array(
            'item' => array(
                'item' => new nsRecordRef(array('internalId' => $ItemId)),
                'description' => $Description,
                'amount' => $Amount,
                'department' => new nsRecordRef(array('internalId' => $DepartmentId)),
                'class' => new nsRecordRef(array('internalId' => $FundId)),

                'customFieldList' => array(
                    new nsComplexObject('SelectCustomFieldRef', array('value' => new nsListOrRecordRef(array('internalId' => $InitiativeId)), 'internalId' => 'custcol_initiative_partner'))
                )
            )
        )
    );

    if($CheckNumber != ""){
        $cashSaleFields['customFieldList'][] = new nsComplexObject('StringCustomFieldRef', array('value' => $CheckNumber, 'internalId' => 'custbody_nso_f_donationchecknumber'));
    }



    // If the payment is associated with an opportunity, handle it.
    // TODO: Still under development and testing.
    if ($OpportunityId > 0) {
        //$cashSaleFields['opportunity'] =  new nsRecordRef(array('internalId' => $OpportunityId));
        $op = new nsComplexObject('InitializeRef', array('type' => 'opportunity', 'internalId' => $OpportunityId));
        $d = new nsComplexObject('InitializeRecord', array('type' => 'cashSale', 'reference' => $op));
        $result = $myNSclient->initialize($d);
        //        var_dump($result);

        if (!$result->isSuccess) {
            echo "0|Error : " . $result->statusDetail[0]->message;
            $cashSale = new nsComplexObject("CashSale", null);
        } else {
            //echo "Success";
            $cashSale = $result->record;
        }
    } else {
        $cashSale = new nsComplexObject("CashSale", null);
    }


    $cashSale->setFields($cashSaleFields);
    //echo ("\nCashSaleFields = \n");
    //var_dump($cashSaleFields);

    //$cashSale = new nsComplexObject("CashSale", $cashSaleFields);

    //echo ("\nCashSale = \n");
    //var_dump($cashSale);

    $addResponse = $myNSclient->add($cashSale);

    //echo ("\naddResponse = \n");
    //var_dump($addResponse);

    if (!$addResponse->isSuccess) {

        $success = false;
        //echo "<font color='red'><b>Failed recording membership payment!<br>" . $addResponse->statusDetail[0]->message . "</b></font>";
        $errorMsg = "Error recording payment! " . $addResponse->statusDetail[0]->message;
        $logMsg .= "\n\nFailed recording payment!\nDetails:" . $errorMsg;
        echo "0| " . $errorMsg;
    } else {

        $success = true;
        //echo("Membership payment recorded successfully!<br>");
        $txnId = $addResponse->recordRef->getField("internalId");
        $tranId = $addResponse->recordRef->getField("tranId");
        $logMsg .= "\n\nPayment recorded successfully! (Txn ID# $txnId or $tranId)";
        echo "1";
    }

    $logMsg .= "\nSeconds lapsed after creating transaction: " . round((getTimeInSeconds() - $start), 4);
}



// Add attendee to event
if($success && isset($_REQUEST["EventId"]) && $_REQUEST["EventId"] > 0) {
    //CalendarEvent
    $eventFields = array();

    $eventFields['internalId'] = $_REQUEST["EventId"];
    //$eventFields['title'] = "Testing " . date("c");

    $eventFields['attendeeList'] = array(
        'replaceAll' => false,
        'attendee' => array(
            'sendEmail' => true,
            "attendee" => array('internalId' => $CustId),
            "response" => '_accepted',
            "attendance" => '_optional'
        )
    );

    //var_dump($eventFields);

    $event = new nsComplexObject("CalendarEvent", $eventFields);
    $updateResponse = $myNSclient->update($event);

    if (!$updateResponse->isSuccess) {
        $success = false;
        $logMsg .= "\n\nError adding attendee ($CustId) to event ({$_REQUEST['EventId']}):" . $updateResponse->statusDetail[0]->message;
    } else {
        $success = true;
        $logMsg .= "\n\nAttendee was added successfully!";
    }

    $logMsg .= "\nSeconds lapsed after adding attendee: " . round((getTimeInSeconds() - $start), 4);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// Notify for changes in chapter, membership level and general updates
$post = array();
$options = array();

$body = "\n\nREQUEST";
foreach ($_REQUEST as $key => $value) {
    if ($key == 'CreditCardNumber') {
        if (strlen($value) > 8)
            $value = substr($value, 0, 4) . str_repeat("X", strlen($value) - 8) . substr($value, strlen($value) - 4, 4);
    }
    $body .= "\n$key: $value";
    $post[$key] = $value;
}
$post["Success"] = $success;
$post["log"] = $logMsg;
$post["errorMsg"] = $errorMsg;
$post["TxnId"] = $txnId;
$post["CustId"] = $CustId;
$post["UserIP"] = getRealIpAddr();
$post["ThisPage"] = $_SERVER['PHP_SELF'];
$post["Referrer"] = $_SERVER["HTTP_REFERER"];
$post["Agent"] = $_SERVER["HTTP_USER_AGENT"];

$body .= "\nError Msg: $errorMsg";
$body .= "\nLog: \n$logMsg";
$body .= "\n\nThis page is: " . $_SERVER['PHP_SELF'];
$body .= "\n\nReferrer: " . $_SERVER["HTTP_REFERER"];
$body .= "\n\nUser IP: " . getRealIpAddr();
$body .= "\n\nAgent: " . $_SERVER['HTTP_USER_AGENT'];

// Caluclate load time
function getTimeInSeconds(){
    $time = microtime();
    $time = explode(' ', $time);
    $time = $time[1] + $time[0];
    return $time;
}

$finish = getTimeInSeconds();
$total_time = round(($finish - $start), 4);

$body .= "\n\nSeconds to complete the job: $total_time secs";

mail("loaytassaf@yahoo.com", "NS: Online Payment Submission", $body, "From: fundraising@muslimamericansociety.org \r\n");

// Postback
if (isset($_POST["PostbackUrl"]) && $_POST["PostbackUrl"] != "") {
    curl_post($_POST["PostbackUrl"], $post, $options);
}
// Redirect user to...
/*$url = "";
if ($success) {
    if (isset($_POST['SuccessUrl']) && $_POST["PostbackUrl"] != "") {
        $url = $_POST['SuccessUrl'];
    } else {
        $url = "https://www.muslimamericansociety.org/donate/thanks.php";
    }
} else {
    if (isset($_POST['FailUrl']) && $_POST['FailUrl'] != "") {
        $url = $_POST['FailUrl'];
    } else {
        $url = "https://www.muslimamericansociety.org/donate/oops.php";
    }
}

if ($url != "") {
    echo "<form action='$url' method='post'>";
    foreach ($post as $key => $value) {
       // $value = htmlentities($value);
        echo "<input type='hidden' name='$key' value='$value'>";
    }


    echo "<input type='hidden' name='Txn_Date' value='" . date('c') . "'>";
    echo "<input type='hidden' name='IP' value='" . htmlentities($_SERVER["HTTP_REFERER"]) . "'>";
    echo "</form>";
    echo "<script>document.forms[0].submit();</script>";
    echo "</body></html>";
}*/
//////////////////////////////////////////////////////////////////////////////////////

function ValidateRequiredField($vField, $vFieldName)
{
    if (isset($vField) && $vField != null) {
        return true;
    } else {
        die("Missing a required field: $vFieldName");
    }
}


/**
 * Send a POST requst using cURL
 * @param string $url to request
 * @param array $post values to send
 * @param array $options for cURL
 * @return string
 */
function curl_post($url, array $post = NULL, array $options = array())
{
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POSTFIELDS => http_build_query($post)
    );

    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if (!$result = curl_exec($ch)) {
        echo(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>
