<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\DateFormatHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class DateFormatExtension
 *
 * @package OxidEsales\Twig\Extensions\Filters
 */
class DateFormatExtension extends AbstractExtension
{
    /**
     * @var DateFormatHelper
     */
    private $dateFormatHelper;

    /**
     * DateFormatExtension constructor.
     *
     * @param DateFormatHelper $dateFormatHelper
     */
    public function __construct(DateFormatHelper $dateFormatHelper)
    {
        $this->dateFormatHelper = $dateFormatHelper;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [new TwigFilter('date_format', [$this, 'dateFormat'])];
    }

    /**
     * Smarty date_format modifier plugin
     *
     * @param string $string
     * @param string $format
     * @param string $defaultDate
     *
     * @return string|null
     */
    public function dateFormat($string, $format = '%b %e, %Y', $defaultDate = '')
    {
        if ($string != '') {
            $timestamp = $this->getTimestamp($string);
        } elseif ($defaultDate != '') {
            $timestamp = $this->getTimestamp($defaultDate);
        } else {
            return null;
        }

        if (DIRECTORY_SEPARATOR == '\\') {
            $format = $this->dateFormatHelper->fixWindowsTimeFormat($format, $timestamp);
        }

        return strftime($format, $timestamp);
    }

    /**
     * smarty_make_timestamp
     *
     * @param string $string
     *
     * @return false|int
     */
    private function getTimestamp($string)
    {
        if (empty($string)) {
            // use "now":
            $time = time();
        } elseif (is_numeric($string) && strlen((string)$string) === 14) {
            // it is mysql timestamp format of YYYYMMDDHHMMSS?
            $string = (string)$string;
            $time = mktime(
                (int)substr($string, 8, 2),
                (int)substr($string, 10, 2),
                (int)substr($string, 12, 2),
                (int)substr($string, 4, 2),
                (int)substr($string, 6, 2),
                (int)substr($string, 0, 4)
            );
        } elseif (is_numeric($string)) {
            // it is a numeric string, we handle it as timestamp
            $time = (int) $string;
        } else {
            // strtotime should handle it
            $time = strtotime($string);
            if ($time == -1 || $time === false) {
                // strtotime() was not able to parse $string, use "now":
                $time = time();
            }
        }

        return $time;
    }
}
