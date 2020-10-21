# mnb-qr-code-payment-generator-php
PHP implementation of MNB's (Hungarian National Bank) QR code payment generation algorithm

The complete guide can be found here: https://www.mnb.hu/letoltes/qr-kod-utmutato-20190712.pdf

Uses `endroid/qr-code` for QR code image generation.


## Requirements
PHP 7.2


## Installation
```
composer require kenlas/mnb-qr-code-payment-generator-php
```


## Example usage
```php
$iban = MnbQrCodePayment\Utils::hungarianBbanToIban('11773016-11111018');

$generator = new MnbQrCodePayment\Generator();
$data = $generator
    ->setMethod('HCT')
    ->setBic('OTPVHUHB')
    ->setName('Szabó Jenő')
    ->setIban($iban)
    ->setAmount(1000)
    ->setExpiration(new DateTime('now + 30 minutes'))
    ->setPaymentSituation('GDSV')
    ->setMessage('hello')
    ->setShopId('1234')
    ->setDeviceId('POS')
    ->setInvoiceId('MY-2020/108')
    ->setCustomerId('4682')
    ->setTransactionId('4687-8765-9624-1245-2022')
    ->setLoyaltyId('4682')
    ->setNavVerificationCode('FXC4')
    ->generate();

$image = new MnbQrCodePayment\QrCodeImage($data);
```

You can send the generated QR code directly to the output:
```php
$image->display();
```

You can save it as an image:
```php
$image->saveTo('my.png');
```

Or you can get the QR code as base64 encoded data URI:
```php
echo $image->asDataUri();
```

You can also use your own QR code renderer: (see https://github.com/endroid/qr-code for more examples)
```php
$customRenderer = new Endroid\QrCode\QrCode();
$customRenderer->setSize(400);
$customRenderer->setMargin(20);
$customRenderer->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
$customRenderer->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
$image->setRenderer($customRenderer);
```


## MnbQrCodePayment\Generator available setters

Method name | Required/optional | Maximum length | Description
----------- | ----------------- | -------------- | -----------
setMethod($code) | Required | 3 | Must be `HCT` for transfer orders or `RTP` for payment request
setVersion($version) | Optional | 3 | For future use only, defaults to `001`
setCharacterSet($charset) | Optional | 1 | For compatibility reasons only, defaults to `1`
setBic($bic) | Required | 11 | The bank's BIC code
setName($name) | Required | 70 | The payer/beneficiary name
setIban($iban) | Required | 28 | The payer/beneficiary IBAN account number
setAmount($amount) | Optional | 12 | The payment amount in HUF, integers only
setExpiration($date) | Required | - | PHP Date object for the expiration date
setPaymentSituation($purposeCode) | Optional | 4 | Purpose code for the given payment situation (see https://www.iso20022.org/catalogue-messages/additional-content-messages/external-code-sets)
setMessage($message) | Optional | 70 | Message
setShopId($value) | Optional | 35 | Shop ID
setDeviceId($value) | Optional | 35 | Device ID
setInvoiceId($value) | Optional | 35 | Invoice ID
setCustomerId($value) | Optional | 35 | Customer ID
setTransactionId($value) | Optional | 35 | Transaction ID
setLoyaltyId($value) | Optional | 35 | Loyalty ID
setNavVerificationCode($value) | Optional | 35 | NAV verification code


## MnbQrCodePayment\Utils available helper methods

Method name | Description
----------- | -----------
hungarianBbanToIban($bban) | Convert a hungarian BBAN (16 or 24 character lengths) to IBAN format


## MnbQrCodePayment\QrCodeImage available methods

Method name | Description
----------- | -----------
*constructor* | Optionally initialize with a QR code string, generated by `MnbQrCodePayment\Generator`
setQrString($qrString) | QR code string generated by `MnbQrCodePayment\Generator`
setRenderer($renderer) | Set a new renderer - an instance of `Endroid\QrCode\QrCode`. Can be useful if you want to customize QR code's settings
display() | Send appropriate headers and display QR code as an image (PNG format by default)
saveTo($path) | Save QR code as an image file (PNG format by default)
asDataUri() | Returns the QR code as a base64 encoded data URI - useful to pass to an `img` src attribute


## Contact
If you have any questions feel free to contact me at kenlashu@gmail.com
