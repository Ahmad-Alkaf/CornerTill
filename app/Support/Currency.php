<?php

namespace App\Support;

class Currency
{
    /**
     * U+20C1 SAUDI RIYAL SIGN (SAMA, February 2025; Unicode 17.0).
     * The front end shows it with a bundled font (resources/fonts/saudi-riyal-sign).
     */
    public const SAUDI_RIYAL_SIGN = "\u{20C1}";

    /**
     * Old Saudi riyal symbols. They mean the Saudi riyal in any country.
     */
    public const OLD_SAUDI_RIYAL_SYMBOLS = ['ر.س', 'ر.س.', 'ر. س'];

    /**
     * Symbols that mean the Saudi riyal only for a business in Saudi Arabia.
     * Other countries use them too (for example '﷼' for the Iranian and the Yemeni rial).
     * Compared without case.
     */
    public const OLD_RIYAL_SYMBOLS_IN_SAUDI_ARABIA = ['﷼', 'ريال', 'SAR', 'SR'];

    /**
     * Change an old Saudi riyal symbol to the Saudi riyal sign.
     * Other values do not change.
     */
    public static function normalize(?string $currency, ?string $country): ?string
    {
        if ($currency === null)
            return null;

        $symbol = trim($currency);
        if (in_array($symbol, self::OLD_SAUDI_RIYAL_SYMBOLS, true))
            return self::SAUDI_RIYAL_SIGN;

        if (
            $country === 'Saudi Arabia'
            && in_array(mb_strtoupper($symbol), self::OLD_RIYAL_SYMBOLS_IN_SAUDI_ARABIA, true)
        )
            return self::SAUDI_RIYAL_SIGN;

        return $currency;
    }
}
