# pg-sdk-php
This is a PHP library/sdk for making merchant integration an easy process. A minimum of PHP 7.0 or later is required for using this client. For references on accessing the APIs directly, you may visit [developer docs](https://developer.phonepe.com/v1/reference/pay-api).

## Installation
Requirements: 
1) PHP 7.0 or later
2) Composer 2.6.2 or later

#### Mandatory Step
Go to your projects root directory where your composer.json and below repository details.
```json
    "repositories": [
        {
            "type": "package",
            "package": [
                {
                    "dist": {
                        "type": "zip",
                        "url": "https://phonepe.mycloudrepo.io/public/repositories/phonepe-pg-php-sdk/phonepe-pg-php-sdk.zip"
                    },
                    "name": "phonepe/phonepe-pg-php-sdk",
                    "version": "1.0.0",
                    "autoload": {
                        "classmap": ["/"]
                    }
                }
            ]
        }
    ],
```

Go to your projects root directory where your composer.json file is located and execute below command.
```composer
composer require --no-cache --prefer-source phonepe/phonepe-pg-php-sdk
```
Please note that you will have to require the vendor/autoloader.php in order to autoload all the required classes.


## Onboarding
To get your keys, please visit Merchant Onboarding of PhonePe PG:  [Merchant Onboarding](https://developer.phonepe.com/v1/docs/merchant-onboarding)  
You will need three things to get started:
```php
$merchantId = "<merchantId>";  
$saltKey = "<saltKey>";  
$saltIndex = "<saltIndex>";  
```

## Quick start:

### Class Initialisation

To create an instance of the `PhonePePaymentClient` class, you need to provide the following parameters:

Example usage:

```php
const MERCHANTID = "<sample-mid>";
const SALTKEY = "<sample-salt-key>";
const SALTINDEX = "<sample-salt-index>";
const SHOULDPUBLISHEVENTS=true;
$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT,SHOULDPUBLISHEVENTS);
```

### Initiate a transaction via Pay Page
To initiate payment we need to build the request using the `PgPayRequestBuilder` class.
To initiate transaction with PayPage Instrument we use the static method `buildPayPageInstrument` from `InstrumentBuilder` class and pass it in the `PgPayRequestBuilder`.
You can initiate the transaction using the `pay` function.
```php
$merchantTransactionId = "<TestMerchantTransactionId>";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("xxxxxxxxx")
    ->callbackUrl("https://webhook.in/test/status")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
    ->build();

$response = $phonePePaymentsClient->pay($request);
$PagPageUrl = $response->getInstrumentResponse()->getRedirectInfo()->getUrl()
```

### Initiate transaction using UPI Intent
To initiate payment we need to build the request using the `PgPayRequestBuilder` class.
To initiate transaction with PayPage Instrument we use the static method `getUpiIntentInstrumentBuilder` from `InstrumentBuilder` class and pass it in the `PgPayRequestBuilder`.
You can initiate the transaction using the `pay` function.
```php
$merchantTransactionId = "<MerchantTransactionId>";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->deviceContext(Constants::ANDROID)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getUpiIntentInstrumentBuilder()
            ->targetApp("com.phonepe.com")
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$intentUrl = $response->getInstrumentResponse()->getIntentUrl();
```
You will get an intent url. It should be used to complete the transaction.

### Checking the validity of the callback

Let's now verify the validity of the callback received from PhonePe on the merchant endpoint, passed as callbackUrl while initiating the payment. 
You need to pass two things to the verifyCallback function:
1) The `x_verify` property in the headers of the callback response obtained from PhonePe.
2) The response body received from PhonePe.

```php
$xVerify = "a005532637c6a6e4a4b08ebc6f1144384353305a9cd253d995067964427cd0bb###1";
$response = '{
"response":"eyJzdWNjZXNzIjpmYWxzZSwiY29kZSI6IlBBWU1FTlRfRVJST1IiLCJtZXNzYWdlIjoiUGF5bWVudCBGYWlsZWQiLCJkYXRhIjp7Im1lcmNoYW50SWQiOiJtZXJjaGFudElkIiwibWVyY2hhbnRUcmFuc2FjdGlvbklkIjoibWVyY2hhbnRUcmFuc2FjdGlvbklkIiwidHJhbnNhY3Rpb25JZCI6IkZUWDIzMDYwMTE1NDMxOTU3MTYzMjM5IiwiYW1vdW50IjoxMDAsInN0YXRlIjoiRkFJTEVEIiwicmVzcG9uc2VDb2RlIjoiUkVRVUVTVF9ERUNMSU5FX0JZX1JFUVVFU1RFRSIsInBheW1lbnRJbnN0cnVtZW50IjpudWxsfX0="
}';

$isValid = $phonepeClient->verifyCallback($response, $xVerify);
```
If the callback signature is verified, the value of $isValid variable will be true.

### Check Status of a transaction

Let see the details for the transaction after the payment is completed via UPI using the `statusCheck` function.

```php
$checkStatus = $phonePePaymentsClient->statusCheck("<merchantTransactionId>");
```
### Dealing with a failed transaction
If you want to check the status the transaction that failed.

```php
$merchantTransactionId="<merchantTransactionId>";
$checkStatus = $phonePePaymentsClient->statusCheck("<merchantTransactionId>");

$checkStatus->getResponseCode();
$checkStatus->getState();
$checkStatus->getTransactionId();
```

### Refund of a transaction
You can refund a PhonePe transaction using the `refund` function.
To initiate refund we need to build the request using the `PgRefundRequestBuilder` class.

```php
$pgRefundRequest = PgRefundRequestBuilder::builder()
    ->originalTransactionId("<originalMerchantTransactionId>")
    ->merchantId(MERCHANTID)
    ->merchantTransactionId("<merchantTransactionId>")
    ->callbackUrl("https://webhook.in/test/status")
    ->amount(<amountInPaise>)
    ->build();
$response = $phonePePaymentsClient->refund($pgRefundRequest);
```


## Documentation

### Class Initialisation

To create an instance of the `PhonePePaymentClient` class to interact with PG, you need to provide the following parameters.

```php
$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);
```  

| Parameter                | Type      | Mandatory | Description                                                                 |
|--------------------------|-----------|-----------|-----------------------------------------------------------------------------|
| `MERCHANTID`            | `mixed`   | Yes       | Unique merchant ID provided by PhonePe.                                    |
| `SALTKEY`               | `mixed`   | Yes       | Salt key for secure communication with PhonePe.                            |
| `SALTINDEX`             | `mixed`   | Yes       | Salt index for secure communication with PhonePe.                          |
| `env`                    | `mixed`   | Yes       | Environment for the PhonePeClient: `Env.PROD` (production), `Env.UAT` (testing). |
| `SHOLDPUBLISHEVENTS`  | `boolean` | No        | Flag to enable event publishing to PhonePe. Set to `False` to disable.     |



#### Example usage:

```php

const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);
```
### Pay
This method is used to initiate a payment via the PhonePe PG. The various instruments that are supported are [PaymentInstruments](#payment-instruments).

#### Parameters

| Parameter               | Type                | Mandatory | Description                                                                                          |
|-------------------------|---------------------|-----------|------------------------------------------------------------------------------------------------------|
| `merchantId`            | `mixed`             | Yes       | The unique identifier of the merchant Note: merchantId length will be less than 38 characters        |
| `merchantTransactionId` | `mixed`             | Yes       | Unique TransactionID generated by the merchant to track request to PhonePe.Note:  merchantTransactionId length should be less than 36 characters.- No Special characters allowed except underscore "_" and hyphen "-"                          |
| `merchantOrderId`       | `mixed`             | No        | The unique identifier of the order                                                                   |
| `amount`                | `integer`            | Yes       | The amount to be paid in paise [100 paise = 1 rupee].                                                |
| `paymentInstrument`     | `PaymentInstrument` | Yes       | You can use the corresponding builder of the [PaymentInstruments](#payment-instruments)              |
| `merchantUserId`        | `mixed`             | No        | The unique identifier of the merchant user. It is used to associate the payment with a specific user. Note: - merchantUserId length should be less than 36 characters, - No Special characters allowed except underscore "_" and hyphen "-" |
| `cancelRedirectUrl`     | `mixed`             | No        | The URL to which the user should be redirected if the payment is cancelled.                          |
| `redirectUrl`           | `mixed`             | No        | The URL to which the user should be redirected after the payment is completed.                       |
| `redirectMode`          | `mixed`             | No        | The mode of redirection after the payment is completed.                                              |
| `callbackUrl`           | `mixed`             | No        | The URL where PhonePe will send callback notifications after the payment is completed.               |
| `callbackMode`          | `mixed`             | No        | The operating system of the device used for the payment.                                             |
| `deviceOS`              | `PgDeviceContext`             | No        | Represents which OS has been used by the User.                                                       |

#### Example Initiating a pay request with UPI Intent Instrument.

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$merchantTransactionId = "<MerchantTransactionId>";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getUpiIntentInstrumentBuilder()
            ->targetApp("com.phonepe.com")
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$intentUrl = $response->getInstrumentResponse()->getIntentUrl();
```

#### Returns
PgPayResponse class

| Property                | Type                 | Description                                                                                                                       |
|-------------------------|----------------------|-----------------------------------------------------------------------------------------------------------------------------------|
| `merchantId`           | `mixed`              | The ID of the merchant associated with the transaction.                                                                           |
| `merchantTransactionId` | `mixed`              | The unique identifier of the merchant transaction.                                                                                |
| `transactionId`        | `mixed`              | The unique identifier of the transaction gerenated by PhonePe.                                                                    |
| `InstrumentResponse`   | `InstrumentResponse` | InstrumentResponse Object contains the instrument details used in initiating the request and other necessary transaction details. |


## Payment Instruments
### Let's look at the various instruments offered by the PhonePe PG.

##### Following instruments are supported:

| Instrument Type | Builder Function                                                  |
|-----------------|-------------------------------------------------------------------|
| CARD            | [CardPayRequestBuilder](#card-payment-instrument)            |
| TOKEN           | [TokenPayRequestBuilder](#token-payment-instrument)          |
| UPI_QR          | [UPIQRPayRequestBuilder](#upi-qr-instrument)                 |
| SAVED_CARD      | [SavedCardPayRequestBuilder](#saved-card-instrument)         |
| NET_BANKING     | [NetBankingPayRequestBuilder](#netbanking-payment-instrument) |
| PAY_PAGE        | [PayPagePayRequestBuilder](#pay-page-payment-instrument)          |
| UPI_INTENT      | [UPIIntentPayRequestBuilder](#upi-intent-instrument)         |
| UPI_COLLECT     | [UPICollectPayRequestBuilder](#upi-collect-instrument)       |


### Pay Page Payment Instrument

Builds PgPayRequest with PayPage as the payment instrument.

#### Instrument Parameters
`buildPayPageInstrument` method from class `InstrumentBuilder` This function directly returns the Instrument Object and no need to call build() method on it.

#### Example usage
```php


const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$merchantTransactionId = 'PHPSDK' . date("ymdHis") . "payPageTest";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9325326080")
    ->callbackUrl("https://webhook.in/test/status")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
    ->build();

$response = $phonePePaymentsClient->pay($request);
$url=$response->getInstrumentResponse()->getRedirectInfo()->getUrl();
```


### UPI Collect Instrument

Builds PgPayRequest with UPI Collect as the payment instrument.

#### Instrument Parameters

| Parameter | Type    | Mandatory | Description                                                                                                                                       |
|-----------|---------|-----------|---------------------------------------------------------------------------------------------------------------------------------------------------|
| `vpa`     | `mixed` | Yes       | The Virtual Payment Address (VPA) to send pay request.<br/> You can validate the VPA using the [ValidateVPA](#validate-vpa) function |

before initiating the transaction with UPI Collect with vpa you need to validate the vpa using the `validateVpa` function.

#### Example usage

```php

const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

// validating vpa before initiating payment.
$vpa = "12345678@ybl";
$pgValidateVpaResponse = $phonePePaymentsClient->validateVpa($vpa);
echo json_encode($pgValidateVpaResponse);

//If validateVpa function does not throws PhonePeException with Invalid Vpa message go ahead with following request
$merchantTransactionId = ""<merchantTransactionId>"";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test ")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getUpiCollectInstrumentBuilder()
            ->vpa($vpa)
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
```

---
### UPI Intent Instrument

Builds PgPayRequest with UPI Intent as the payment instrument.

#### Instrument Parameters

| Parameter    | Type    | Mandatory | Description                                                                                                                    |
|--------------|---------|-----------|--------------------------------------------------------------------------------------------------------------------------------|
| `targetApp` | `mixed` | Yes       | The target app identifier for the UPI Intent flow.<br/> For IOS: `PHONEPE`,`GPAY`, `PAYTM`<br/>For Android:`com.phonepe.app`, `net.one97.paytm` |

#### Example usage for IOS UPI INTENT

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->deviceContext(Constants::IOS)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getUpiIntentInstrumentBuilder()
            ->targetApp("PHONEPE")
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$intentUrl = $response->getInstrumentResponse()->getIntentUrl();
```
#### Example usage for Android

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$merchantTransactionId = 'PHPSDK' . date("ymdHis") . "upiIntentPayTest";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->deviceContext(Constants::ANDROID))
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getUpiIntentInstrumentBuilder()
            ->targetApp("com.phonepe.com")
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$intentUrl = $response->getInstrumentResponse()->getIntentUrl();
```

#### Note
For Intent instrument, you have to pass the `deviceContext` (ANDROID or IOS) in the request to get app specific intent urls.

---
### UPI QR Instrument

Builds PgPayRequest with UPI QR as the payment instrument.

#### Instrument Parameters

`buildUpiQrInstrument` method from class `InstrumentBuilder` function directly returns the Instrument Object and no need to call build() method on it.

#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$merchantTransactionId = "merchantTransactinoId";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test ")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(InstrumentBuilder::buildUpiQrInstrument())
    ->build();

$response = $phonePePaymentsClient->pay($request);
$imageBase64Data = $response->getInstrumentResponse()->getQrData();
$intentUrl = $response->getInstrumentResponse()->getIntentUrl();
```



---
### Card Payment Instrument

Builds PgPayRequest with Card as the payment instrument.

#### Instrument Parameters

| Parameter               | Type      | Mandatory | Description                                                                                                                |
|-------------------------|-----------|-----------|----------------------------------------------------------------------------------------------------------------------------|
| `authMode`             | `mixed`   | Yes       | The authentication mode for the card payment. Generally `3DS` is used [example 3DS, H2H]                            |
| `saveCard`             | `boolean` | Yes       | Whether to save the card for future use.                                                                                   |
| `encryptedCardNumber` | `mixed`   | Yes       | The encrypted card number.  You can use the [EncryptedData](#encrypting-data) function to encrypt unencrypted card number. |
| `encryptionKeyId`     | `integer` | Yes       | The ID of the encryption key used for the card.                                                                            |
| `cardHolderName`      | `mixed`   | Yes       | The name of the cardholder.                                                                                                |
| `expiryMonth`          | `mixed`   | Yes       | The expiry month of the card.                                                                                              |
| `expiryYear`           | `mixed`   | Yes       | The expiry year of the card.                                                                                               |
| `encryptedCvv`         | `mixed`   | Yes       | The encrypted CVV of the card.                                                                                             |
| `addressLine1`         | `mixed`   | No        | The address line 1 for billing.                                                                                            |
| `addressLine2`         | `mixed`   | No        | The address line 2 for billing.                                                                                            |
| `addressCity`          | `mixed`   | No        | The city for billing.                                                                                                      |
| `addressState`         | `mixed`   | No        | The state for billing.                                                                                                     |
| `addressZip`           | `mixed`   | No        | The ZIP code for billing.                                                                                                  |
| `addressCountry`       | `mixed`   | No        | The country for billing.                                                                                                   |

#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$authMode = "3DS";
$saveCard = true;
$encryptionKeyId = 20;

$encryptedCvv = $phonePePaymentsClient->encryptedData(PRODPUBLICKEY, "123");
$encryptedCardNumber = $phonePePaymentsClient->encryptedData(PRODPUBLICKEY, "5272559973463145");
$expiryMonth = "10";
$expiryYear = "2025";
$cardHolderName = "TEST USER";

$merchantTransactionId = "merchantTransactionId";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getNewCardInstrumentBuilder()
        ->authMode($authMode)
        ->saveCard($saveCard)
        ->encryptedCvv($encryptedCvv)
        ->encryptedCardNumber($encryptedCardNumber)
        ->expiryMonth($expiryMonth)
        ->expiryYear($expiryYear)
        ->encryptionKeyId($encryptionKeyId)
        ->cardHolderName($cardHolderName)
        ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$url = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();
```


### Token Payment Instrument

Builds PgPayRequest with Token as the payment instrument.

#### Instrument Parameters

| Parameter               | Type      | Mandatory | Description                                                                                                                                                      |
|-------------------------|-----------|-----------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `authMode`             | `mixed`   | Yes       | The authentication mode for the token payment. Generally `3DS`, example 3DS, H2H.                                                                                |
| `encryptedCvv`         | `mixed`   | Yes       | The encrypted CVV of the tokenized card.  You can use the [EncryptedData](#encrypting-data)  function to encrypt unencrypted cvv.                                |
| `cryptogram`            | `mixed`   | Yes       | The cryptogram associated with the tokenized card.                                                                                                               |
| `encryptedToken`       | `mixed`   | Yes       | The encrypted token representing the card.  You can use the [EncryptedData](#encrypting-data)  function to encrypt unencrypted token.                            |
| `encryptionKeyid`     | `integer` | Yes       | The ID of the encryption key used for the tokenized card.  You can use the [EncryptedData](#encrypting-data)  function to encrypt unencrypted encryption key id. |
| `expiryMonth`          | `mixed`   | Yes       | The expiry month of the tokenized card.                                                                                                                          |
| `expiryYear`           | `mixed`   | Yes       | The expiry year of the tokenized card.                                                                                                                           |
| `panSuffix`            | `mixed`   | Yes       | The last 4 digits of the tokenized card number.                                                                                                                  |
| `cardHolderName`      | `mixed`   | Yes       | The name of the cardholder associated with the tokenized card.                                                                                                   |

#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$tokenCvv = "elsmatmiYs+ZzXwdO2/BXf96DoZQ2uGkkHfN34331edarer6LFhUN9GpkYYHH8ebPD4q8Fst4v4GAXAcwqzesvstaTkpfouOosWvXUR6wQDXV6yy8gwf88fR+GGMyUXjmnEjiXqVSNhhJcd1HVobJLInvFWl1Y1zeuo8T7IFEAxC0Mu5CfrFD3uNgNiIJwp0BRc67bCFQyiOtXK+MmeijVBa5PeP2/CZdHW+8sn2wLrm5R6MRS7HIfs8S2vytUVUW74VVPZRRVMVgMs3sPQyR/YbaEeRby1Ec01r/trW2H+2smusuyelTzQTCwLbRG153ZjbtUyl3TrRfbgEhfZwDxcBa9EbIKDQkTRnh4ooKLK7HaUb8t7K70hpzV/cJ7b5AML/RMjG4DCyomegGRP/NbrXFZbrk8QEacO4ruvNVLOQG9RqAdIBm9neMPUrfc4Uoahy253Gx/VWtqN1kilwmAKZvu6KAX7JNGyPz32QseY8HDHRyPM5HHGZSkVm7XZ+hahEIHedEKWbbdM2fV0kbvzzbArBkTlA7B9m3022sILwwrnc0hMuy4xbRClB/BoT0EE08hEutGz0nzurGm8vdQag43FdnKykcmCS4xW+jC1XUi5ok4lZHdUwMSF1oGabriiJjXFf+vz4ESgJDBXzzKzJhaGB5pIoPydwQ15Ziv2m9OY=";
$encryptedToken = "RwC47xWWNvdlwCSNBRN14jl0tW5gla/MomKp43435sr3334dmYUYmLHQRBXoratDTOi7lKlYNyLUXD6n6Q0txGNE7bL7Vc49yUAkmENmB9RiVGxcAaPJGDi37628xOXRIOtbCpq4s2Cy2ti2Gx9G7sKvU98+eN2XVs39f9jSexsa7c/FIUeedaZ0cbdzWVvj/kq6y9Fsx9lyHs/VnYaxfJtWI2LWt/VmPsx/0z1WYx9wK6jFpSX9de/tNL+hyCn6F0ygjyc1nGU/UuQT9jt+dmxns1SGNGrfMOlk/jMsQMGztWDgrPzpmJR6sjr1apjmwBAbYU+5dl6Hcc/QIjjSKSzBxYeXANAtx2/LcUl8Wtscg8Dk64TRsMs1GuJm8FYfxz974jXvh2DnRhOufieCAe5mc6ZjP61WjPMQBBjesY7lSZ1TKeLNcoAbHCL+yc3IFdYohEz3bRGLf+9UMsFOe3K8NQP7kx68dIKacnf++nU/EJ+fphzfSMaHfYksayB+fdVsM0VeRXNAGRjxuo9cqWhgX5cJ7IILGycheBtEhgZli3Ji8tj6d39FHVZXq3NeKbPnyTyUU3LPUG2zuQqyxTwCNIP4LaYwf2bk8DcAIuOCK9GUxdYP3PsNPxQfuWYIHerYvAIbkPk37uzn2RndgOiMem3TP5Unnhd+f8Qo+5Qbfaw=";
$cryptogram = "69696181054119923371";
$expiryYear = "2026";
$expiryMonth = "01";
$panSuffix = "8209";
$encryptionKeyid = 20;
$authMode = "3DS";

$merchantTransactionId = "merchantTransactionId";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getTokenInstrumentBuilder()
        ->authMode($authMode)
        ->encryptedCvv($tokenCvv)
        ->encryptedToken($encryptedToken)
        ->cryptogram($cryptogram)
        ->expiryYear($expiryYear)
        ->expiryMonth($expiryMonth)
        ->panSuffix($panSuffix)
        ->encryptionKeyId($encryptionKeyid)
        ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$url = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();
```


### NetBanking Payment Instrument

Builds PgPayRequest with NetBanking as the payment instrument.

#### Instrument Parameters

| Parameter | Type    | Mandatory | Description                                                                                                                                             |
|-----------|---------|-----------|---------------------------------------------------------------------------------------------------------------------------------------------------------|
| `bankId`  | `mixed` | Yes       | The ID of the bank for Net Banking payment. You can find the possible values in [PaymentOptions](#payment-options) response with `includeNetBankingBanksList=True` |

#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

// To get the bankList Id's and other payment options
$includeNetBankingBanksList = true;
$paymentOptionsResponse = $phonePePaymentsClient->paymentOptions($includeNetBankingBanksList);

$merchantTransactionId = "<merchantTRansactionId>";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test ")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getNetbankingInstrumentBuilder()
            ->bankId("HDFC")
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$url = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();
```



---

### Saved Card Instrument

Builds PgPayRequest with Saved Card as the payment instrument.

#### Parameters

| Parameter         | Type      | Mandatory | Description                                                                                                                                                    |
|-------------------|-----------|-----------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `authMode`        | `mixed`   | Yes       | The authentication mode for the saved card. Generally `3DS` is used, example 3DS. H2H                                                                          |
| `cardId`          | `mixed`   | Yes       | The ID of the saved card. You can find this in the callback from the callback of Card instrument, when `save_card=True`                                        |
| `encryptedCvv`    | `mixed`   | Yes       | The encrypted CVV of the saved card.</br> You can use the [EncryptedData](#encrypting-data)  function to encrypt unencrypted cvv.                              |
| `encryptionKeyid` | `integer` | Yes       | The ID of the encryption key used for the saved card. </br> You can use the [EncryptedData](#encrypting-data) function to encrypt unencrypted encryptionKeyid. |

#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$authMode = "3DS";
$encryptedCvv = $phonePePaymentsClient->encryptedData(PRODPUBLICKEY, "123");
$encryptionKeyId = 3;
$cardId = "cardId";

$merchantTransactionId = "merchantTransactionId";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9090909090")
    ->callbackUrl("https://webhook.in/test")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(
        InstrumentBuilder::getSavedCardInstrumentBuilder()
            ->authMode($authMode)
            ->encryptedCvv($encryptedCvv)
            ->encryptionKeyId($encryptionKeyId)
            ->cardId($cardId)
            ->build()
    )
    ->build();

$response = $phonePePaymentsClient->pay($request);
$url = $response->getInstrumentResponse()->getRedirectInfo()->getUrl();
```




### Check status
This method is used to check the status of a transaction.

#### Parameters:


| Parameter        | Type    | Mandatory | Description                                                |
|------------------|---------|-----------|------------------------------------------------------------|
| merchantTransactionId | `mixed` | Yes       | Merchant transaction Id for which status is to be fetched. |

#### Example checking the status of a transaction completed via UPI.

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$checkStatus = $phonePePaymentsClient->statusCheck("merchantTransactionId");
$state=$checkStatus->getState();

```

#### Returns
The function returns a `PgCheckStatusResponse` object with the following properties:

| Property | Type                | Description                                                     |  
|----------|---------------------|-----------------------------------------------------------------|  
| `merchantId` | `mixed`             | The merchant ID.                                                |  
| `merchantTransactionId` | `mixed`             | The merchant transaction ID.                                    |  
| `transactionId` | `mixed`             | The transaction ID.                                             |  
| `amount` | `integer`            | The transaction amount in paise [100 paise = 1 rupee].          |  
| `responseCode` | `mixed`             | The response code.                                              |  
| `state` | `mixed`             | The transaction state. Can be PENDING, COMPLETED, FAILED        |  
| `paymentInstrument` | `PaymentInstrument` | The PhonePe payment instrument used to perform the transaction. |  

#### Let see the details for the transaction after the payment is completed via UPI using the `checkStatus` function.

```php
$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);
$checkStatus = $phonePePaymentsClient->statusCheck("merchantTransactionId");

$paymentInstrument = $checkStatus->getPaymentInstrument();
$utr = $paymentInstrument->getUtr();
```

#### Let see the details for the transaction after the payment is completed via Card using the `checkStatus` function.

```php
$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);
$checkStatus = $phonePePaymentsClient->statusCheck("merchantTransactionId");

$paymentInstrument = $checkStatus->getPaymentInstrument();
$pgTransactionId = $paymentInstrument->getPgTransactionId();
$pgAuthorizationCode = $paymentInstrument->getPgAuthorizationCode();
$bankId = $paymentInstrument->.getBankId();
$bankId = $paymentInstrument->.getArn();
```

#### Let see the details for the transaction after the payment is completed via NetBanking using the `checkStatus` function.

```php
$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);
$checkStatus = $phonePePaymentsClient->statusCheck("merchantTransactionId");

$paymentInstrument = $checkStatus->getPaymentInstrument();
$bankId = $paymentInstrument->getBankId();
$bankTransactionId = $paymentInstrument->getBankTransactionId();
```

### Refund

#### Request Parameters:

| Parameter                 | Type     | Mandatory | Description                                                                                                                                   |
|---------------------------|----------|-----------|-----------------------------------------------------------------------------------------------------------------------------------------------|
| `merchantId`           | `mixed`  | Yes       | The ID of the merchant associated with the transaction.                                                                                       |
| `merchantTransactionId` | `mixed`  | Yes       | Unique Refund Transaction ID generated by the merchant. This should be different from the transaction ID of the debit transaction.  |
| `originalTransactionId` | `mixed`  | Yes       | Merchant transaction ID of the forward transaction which needs to be reversed.                                                  |
| `amount`                  | `integer` | Yes       | Reversal amount in paise. Up to a maximum of the amount of the original payment transaction.                                   |
| `callbackUrl`          | `mixed`  | No        | The URL where PhonePe will send callback notifications after the refund is completed.                                                         |


#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$pgRefundRequest = PgRefundRequestBuilder::builder()
        ->originalTransactionId("<originalTransactionId>")
        ->merchantId(MERCHANTID)
        ->merchantTransactionId("<merchantTransactionId>")
        ->callbackUrl("https://webhook.in/test/status")
        ->amount(<amountInPaise>)
        ->build();
$response = $phonePePaymentsClient->refund($pgRefundRequest);
$responseCode = $response->getResponseCode();
```


#### Returns
PgRefundResponse
##### PgRefundResponse properties

| Property                      | Type     | Description                                             |
|----------------------------|----------|---------------------------------------------------------|
| `merchantId`              | `mixed`  | The ID of the merchant associated with the transaction. |
| `merchantTransactionId`  | `mixed`  | The unique identifier of the merchant transaction.      |
| `transactionId`           | `mixed`  | The unique identifier of the refund transaction.        |
| `amount`                   | `integer` | The refunded amount in paise [100 paise = 1 rupee].                                   |
| `state`                    | `mixed`  | The state of the refund transaction.                    |
| `responseCode`            | `mixed`  | The response code indicating the status of the refund.  |

### Validate VPA
Used to check if the given vpa is valid or not


#### Parameters

| Parameter | Type    | Mandatory | Description |
|-----------|---------|-----------|-------------|
| `vpa` | `mixed` | Yes       | The Virtual Payment Address (VPA) to validate. |



#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

String vpa="abc@ybl";
try {
    $validateVpaResponse = $phonePePaymentsClient->validateVpa("abc@ybl");
    $name = $validateVpaResponse->getName();
}
catch (Excpetion $e) {
    // Handle PhonePeException thrown in case of invalid vpa.
}
```


#### Returns

The function returns a `PgValidateVpaResponse` object with the following properties if vpa is valid:

##### PgValidateVpaResponse Properties

| Property | Type    | Description |  
|----------|---------|-------------|  
| `vpa` | `mixed` | The VPA sent in the request. |  
| `name` | `mixed` | The name linked to VPA. |  




### Payment Options
This method is used to retrieve available payment options.


#### Parameters

| Parameter                        | Type     | Mandatory | Description                                                                                        |
|----------------------------------|----------|-----------|----------------------------------------------------------------------------------------------------|
| `includeNetBankingBanksList` | `boolean` | No        |  If set to `True`, the response will include the list of Net Banking banks. Default: `False`|


#### Example usage

```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

$paymentOptionsResponse = $phonePePaymentsClient->paymentOptions(true);
```
#### Returns
The function returns a `PgPaymentsOptionsResponse` object with the following properties:

##### PgPaymentsOptionsResponse Properties

| Property                     | Type              | Description                                     |
|------------------------------|-------------------|-------------------------------------------------|
| `upiCollect`                | `PaymentOption`    | UPI Collect options                        |
| `intent`                     | `PaymentOption`        | Intent options                              |
| `cards`                      | `PaymentOption` | External cards options                      |
| `netBanking`                | `PaymentOptionNetBanking` | Net Banking options                         |


----

## Callback verification
You need to pass two things to the verifyResponse function:
### Parameters

| Parameter   | Type    | Mandatory | Description                                                                            |
|-------------|---------|-----------|----------------------------------------------------------------------------------------|
| `x_verify`  | `mixed` | Yes       | The `x_verify` property in the headers of the callback response obtained from PhonePe. |
| `response`  | `mixed` | Yes       | The response body recieved in the callback from PhonePe.                               |

### Returns
`true` if the `x_verify` is valid for the given data.

#### Example usage
```php
$xVerify = "a005532637c6a6e4a4b08ebc6f1144384353305a9cd253d995067964427cd0bb###1";
$response = '{
"response":"eyJzdWNjZXNzIjpmYWxzZSwiY29kZSI6IlBBWU1FTlRfRVJST1IiLCJtZXNzYWdlIjoiUGF5bWVudCBGYWlsZWQiLCJkYXRhIjp7Im1lcmNoYW50SWQiOiJtZXJjaGFudElkIiwibWVyY2hhbnRUcmFuc2FjdGlvbklkIjoibWVyY2hhbnRUcmFuc2FjdGlvbklkIiwidHJhbnNhY3Rpb25JZCI6IkZUWDIzMDYwMTE1NDMxOTU3MTYzMjM5IiwiYW1vdW50IjoxMDAsInN0YXRlIjoiRkFJTEVEIiwicmVzcG9uc2VDb2RlIjoiUkVRVUVTVF9ERUNMSU5FX0JZX1JFUVVFU1RFRSIsInBheW1lbnRJbnN0cnVtZW50IjpudWxsfX0="
}';

$isValid = $phonepeClient->verifyCallback($response, $xVerify );
echo $isValid;
```
If the callback signature is verified, that will result in the value to be true.
```php  
1
```  

----

## Encrypting data
You need to pass to two things to the `encryptdata` function and returns the encrypted data using the public key shared:
### Parameters

| Parameter   | Type    | Mandatory | Description                                                                            |
|-------------|---------|-----------|----------------------------------------------------------------------------------------|
| `data`      | `mixed` | Yes       | The data to be encrypted.                                                              |
| `publicKey` | `mixed` | Yes       | The public key to be used for encryption, this will be shared by PhonePe. |

Returns

| Parameter   | Description                                                               |
|-------------|---------------------------------------------------------------------------|
| `data`      | The encrypted data                                        |

#### Example usage
```php
const PLUBLICKEY = "<publickey>"
$data= "105";
$encryptedData = $phonePePaymentsClient->encryptedData(PRODPUBLICKEY, $data);
```


----


## Exception Handling
Exception raised from PhonePe SDK.

#### Attributes

| Attribute          | Description                                               |
|--------------------|-----------------------------------------------------------|
| `code`             | The status code of the http response.                     |
| `message`          | The http error message.                                   |

#### Example usage
```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

// If a already used transactionId is used  to initiated new transaction
$merchantTransactionId = "<merchantTransactionId>";
$request = PgPayRequestBuilder::builder()
    ->mobileNumber("9089785645")
    ->callbackUrl("https://webhook.in/test/status")
    ->merchantId(MERCHANTID)
    ->merchantUserId("<merchantUserId>")
    ->amount(<amountInPaise>)
    ->merchantTransactionId($merchantTransactionId)
    ->paymentInstrument(InstrumentBuilder::buildPayPageInstrument())
    ->build();
try{
    $response = $phonePePaymentsClient->pay($request);
}
catch(PhonePePgException $phonePeException){
    
    $phonePeCode = $phonePeException->getPhonePeResponse()->getCode();
    $phonePeMessage = $phonePeException->getPhonePeResponse()->getMessage();
}
```

#### Possible Exceptions thrown

##### PhonePeException: this exception is thrown after making request to PhonePe Server.

If a Duplicate TransactionId is passed
```php
// PhonePeException
$code = 417;
$message = '417 Received HTTP response: {"success":false,"code":"INVALID_TRANSACTION_ID","message":"The transaction id you have entered seems to be invalid.","data":{}}';
```

if passed incorrect saltKey to PhonePeTransactionClient
```php
// PhonePeException
$code = 401;
$message = "401: Unauthorised Please check you saltKey, SaltIndex and merchantId <response>";
```  

if passed incorrect merchantId to PhonePeTransactionClient
```php
// PhonePeException
$code = 404;
$message = "404 https://api.phonepe.com/apis/hermes/pg/v1/pay Not found <response>";
```  

If 500 resppnse is received from PhonePe server
```php
// PhonePeException
$code = 500;
$message = "500: Internal server error: Please retry after some Time <response>";
```  

#### Validate vpa example usage and exceptions 
```php
const $MERCHANTID="<merchantId>";
const $SALTKEY="<saltKey>";
const $SALTINDEX="<saltIndex>";
const $env=Env::UAT;
const $SHOULDPUBLISHEVENTS=true;

$phonePePaymentsClient = new PhonePePaymentClient(MERCHANTID, SALTKEY, SALTINDEX, Env::UAT, SHOLDPUBLISHEVENTS);

try{
    $vpa = "12345678@ybl";
    $pgValidateVpaResponse = $phonePePaymentsClient->validateVpa($vpa);
    echo json_encode($pgValidateVpaResponse);
}
catch(PhonePePgException $phonePeException){
    
    $phonePeCode = $phonePeException->getPhonePeResponse()->getCode();
    $phonePeMessage = $phonePeException->getPhonePeResponse()->getMessage();
}
```
If 417 response is received from PhonePe server
```php
// PhonePeException
$code = 500;
$message = '417 Received HTTP response: {"success":false,"code":"INVALID_VPA","message":"Incorrect UPI details. Please check the UPI ID entered.","data":{}} ';
```  







