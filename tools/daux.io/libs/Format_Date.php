<?php

declare(strict_types=1);

namespace Todaymade\Daux;

class FormatDate
{
    public static function format($config, $date): string|false
    {
        $locale = $config->getLanguage();
        $datetype = \IntlDateFormatter::LONG;
        $timetype = \IntlDateFormatter::SHORT;
        $timezone = null;

        if (!extension_loaded('intl')) {
            $locale = 'en';
            $timezone = 'GMT';
        }

        $formatter = new \IntlDateFormatter($locale, $datetype, $timetype, $timezone);

        return $formatter->format($date);
    }
}
