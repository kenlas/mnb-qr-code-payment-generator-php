<?php

declare(strict_types=1);

namespace MnbQrCodePayment;

class Utils
{
    public static function hungarianBbanToIban($accountNumber)
    {
        $accountNumber = strtoupper(preg_replace('/[\s\-]*/', '', $accountNumber));

        if (!in_array(strlen($accountNumber), [16, 24])) {
            throw new \Exception('Account number length is not valid');
        }

        $accountNumber = str_pad($accountNumber, 24, '0');

        $checkString = preg_replace_callback(['/[A-Z]/', '/^[0]+/'], function ($matches) {
            if (substr($matches[0], 0, 1) !== '0') { // may be multiple leading 0's
                return base_convert($matches[0], 36, 10);
            }
            return '';
        }, $accountNumber . 'HU00');

        $mod = function_exists('bcmod') ? bcmod($checkString, 97) : self::bcmod($checkString, 97);
        $code = (string) (98 - $mod);

        return sprintf(
            'HU%s%s',
            str_pad($code, 2, '0', STR_PAD_LEFT),
            $accountNumber
        );
    }

    public static function bcmod($x, $y)
    {
        // how many numbers to take at once? carefull not to exceed (int)
        $take = 1;
        $mod = '';

        do {
            $a = (int) $mod . substr($x, 0, $take);
            $x = substr($x, $take);
            $mod = $a % $y;
        } while (strlen($x));

        return (int) $mod;
    }
}