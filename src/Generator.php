<?php

/**
 * @author László Kenéz
 * @copyright 2020
 *
 * https://www.mnb.hu/letoltes/qr-kod-utmutato-20190712.pdf
 */

declare(strict_types=1);

namespace MnbQrCodePayment;

use DateTime;

class Generator
{
    protected $method;
    protected $version = '001';
    protected $characterSet = '1';
    protected $bic;
    protected $name;
    protected $iban;
    protected $amount = 0;
    protected $currency = 'HUF';
    protected $expiration;
    protected $paymentSituation;
    protected $message;
    protected $shopId;
    protected $deviceId;
    protected $invoiceId;
    protected $customerId;
    protected $transactionId;
    protected $loyaltyId;
    protected $navVerificationCode;

    public function __construct()
    {

    }

    public function setMethod(string $code)
    {
        $code = strtoupper($code);

        if (!in_array($code, ['HCT', 'RTP'])) {
            throw new \Exception('Invalid method value: ' . $code);
        }

        $this->method = $code;

        return $this;
    }

    public function setVersion(string $version)
    {
        if (strlen($version) !== 3) {
            throw new \Exception('Version length must be exactly 3 characters');
        }

        $this->version = $version;

        return $this;
    }

    public function setCharacterSet($characterSet)
    {
        if ($characterSet != 1) {
            throw new \Exception('Character set must be set to 1');
        }

        $this->characterSet = $characterSet;

        return $this;
    }

    public function setBic(string $bic)
    {
        if (strlen($bic) > 11) {
            throw new \Exception('BIC code length must be at most 11 characters');
        }

        $this->bic = $bic;

        return $this;
    }

    public function setName(string $name)
    {
        if (mb_strlen($name) > 70) {
            throw new \Exception('Name must be at most 70 characters');
        }

        $this->name = $name;

        return $this;
    }

    public function setIban(string $iban)
    {
        if (strlen($iban) != 28) {
            throw new \Exception('IBAN length must be exactly 28 characters');
        }

        $this->iban = $iban;

        return $this;
    }

    public function setAmount(int $amount)
    {
        if (strlen((string) $amount) > 12) {
            throw new \Exception('Amount length must be at most 12 characters');
        }

        $this->amount = $amount;

        return $this;
    }

    public function setExpiration(DateTime $expiration)
    {
        $timezoneOffset = $expiration->getOffset() / 3600;
        if ($timezoneOffset > 9 || $timezoneOffset < 0) {
            throw new \Exception('Invalid timezone offset for expiration date: ' . $expiration->format(DateTime::ATOM));
        }

        $this->expiration = $expiration;

        return $this;
    }

    public function setPaymentSituation(string $situation)
    {
        if (mb_strlen((string) $situation) > 4) {
            throw new \Exception('Payment situation length must be at most 4 characters');
        }

        $this->paymentSituation = $situation;

        return $this;
    }

    public function setMessage(string $message)
    {
        if (mb_strlen((string) $message) > 70) {
            throw new \Exception('Message length must be at most 70 characters');
        }

        $this->message = $message;

        return $this;
    }

    public function setShopId(string $shopId)
    {
        if (mb_strlen((string) $shopId) > 35) {
            throw new \Exception('Shop ID length must be at most 35 characters');
        }

        $this->shopId = $shopId;

        return $this;
    }

    public function setDeviceId(string $deviceId)
    {
        if (mb_strlen((string) $deviceId) > 35) {
            throw new \Exception('Device ID length must be at most 35 characters');
        }

        $this->deviceId = $deviceId;

        return $this;
    }

    public function setInvoiceId(string $invoiceId)
    {
        if (mb_strlen((string) $invoiceId) > 35) {
            throw new \Exception('Invoice ID length must be at most 35 characters');
        }

        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function setCustomerId(string $customerId)
    {
        if (mb_strlen((string) $customerId) > 35) {
            throw new \Exception('Customer ID length must be at most 35 characters');
        }

        $this->customerId = $customerId;

        return $this;
    }

    public function setTransactionId(string $transactionId)
    {
        if (mb_strlen((string) $transactionId) > 35) {
            throw new \Exception('Transaction ID length must be at most 35 characters');
        }

        $this->transactionId = $transactionId;

        return $this;
    }

    public function setLoyaltyId(string $loyaltyId)
    {
        if (mb_strlen((string) $loyaltyId) > 35) {
            throw new \Exception('Loyalty ID length must be at most 35 characters');
        }

        $this->loyaltyId = $loyaltyId;

        return $this;
    }

    public function setNavVerificationCode(string $navVerificationCode)
    {
        if (mb_strlen((string) $navVerificationCode) > 35) {
            throw new \Exception('NAV verification code length must be at most 35 characters');
        }

        $this->navVerificationCode = $navVerificationCode;

        return $this;
    }

    public function validate(): bool
    {
        $requiredParams = [
            'method',
            'version',
            'characterSet',
            'bic',
            'name',
            'iban',
            'expiration',
        ];

        foreach ($requiredParams as $param) {
            if (empty($this->$param)) {
                throw new \Exception('Missing required parameter: ' . $param);
            }
        }

        // @todo validate character range

        return true;
    }

    public function generate()
    {
        $this->validate();

        $fields = [
            'method',
            'version',
            'characterSet',
            'bic',
            'name',
            'iban',
            'amount',
            'expiration',
            'paymentSituation',
            'message',
            'shopId',
            'deviceId',
            'invoiceId',
            'customerId',
            'transactionId',
            'loyaltyId',
            'navVerificationCode',
        ];

        $result = '';

        foreach ($fields as $field) {
            if ($field === 'amount') {
                $value =  empty($this->amount) ? '' : $this->currency . $this->amount;
            } else if ($field === 'expiration') {
                $timezoneOffset = $this->expiration->getOffset() / 3600;
                $value = sprintf(
                    '%s+%d',
                    $this->expiration->format('YmdHis'),
                    (int) $timezoneOffset
                );
            } else {
                $value = $this->$field;
            }

            $result .= $value . "\n";
        }

        return $result;
    }
}
