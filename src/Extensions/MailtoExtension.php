<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Error\RuntimeError;

class MailtoExtension extends AbstractExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('mailto', [$this, 'mailto'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Twig mailto function imported from Smarty
     *
     * @param string $address
     * @param array  $parameters
     *
     * @return string
     */
    public function mailto(string $address, array $parameters = []): string
    {
        $text = $parameters['text'] ?? $address;
        $extra = $parameters['extra'] ?? '';

        $address .= $this->prepareMailParametersString($parameters);

        $encode = (empty($parameters['encode'])) ? 'none' : $parameters['encode'];
        if (!in_array($encode, ['javascript', 'javascript_charcode', 'hex', 'none'])) {
            throw new RuntimeError("mailto: 'encode' parameter must be none, javascript or hex");
        }

        return match ($encode) {
            'javascript' => $this->mailJavascript($address, $text, $extra),
            'javascript_charcode' => $this->mailJavascriptCharcode($address, $text, $extra),
            'hex' => $this->mailHex($address, $text, $extra),
            default => "<a href=\"mailto:$address\" $extra>$text</a>",
        };
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function prepareMailParametersString(array $parameters): string
    {
        // Netscape and Mozilla do not decode %40 (@) in BCC field (bug?), so don't encode it.
        $search = ['%40', '%2C'];
        $replace = ['@', ','];

        $mailParameters = [];
        foreach ($parameters as $var => $value) {
            switch ($var) {
                case 'cc':
                case 'bcc':
                case 'followupto':
                    if ($value) {
                        $mailParameters[] = $var . '=' . str_replace($search, $replace, rawurlencode($value));
                    }
                    break;
                case 'subject':
                case 'newsgroups':
                    $mailParameters[] = $var . '=' . rawurlencode($value);
                    break;
            }
        }

        $parametersString = '';
        if (count($mailParameters)) {
            $parametersString = '?' . implode('&', $mailParameters);
        }

        return $parametersString;
    }

    /**
     * Encode using javascript
     *
     * @param string $address
     * @param string $text
     * @param string $extra
     *
     * @return string
     */
    private function mailJavascript(string $address, string $text, string $extra): string
    {
        $string = "document.write('<a href=\"mailto:$address\" $extra>$text</a>');";

        $jsEncode = '';
        for ($x = 0, $xMax = strlen($string); $x < $xMax; $x++) {
            $jsEncode .= '%' . bin2hex($string[$x]);
        }

        return "<script type=\"text/javascript\">eval(unescape('$jsEncode'))</script>";
    }

    /**
     * Encode using charcode
     *
     * @param string $address
     * @param string $text
     * @param string $extra
     *
     * @return string
     */
    private function mailJavascriptCharcode(string $address, string $text, string $extra): string
    {
        $string = "<a href=\"mailto:$address\" $extra>$text</a>";

        $ord = [];
        for ($x = 0, $y = strlen($string); $x < $y; $x++) {
            $ord[] = ord($string[$x]);
        }

        return
            "<script type=\"text/javascript\" language=\"javascript\">\n"
            . "<!--\n"
            . "{document.write(String.fromCharCode(" . implode(',', $ord) . "))}\n"
            . "//-->\n"
            . "</script>";
    }

    /**
     * Encode using hex
     *
     * @param string $address
     * @param string $text
     * @param string $extra
     *
     * @return string
     */
    private function mailHex(string $address, string $text, string $extra): string
    {
        $match = [];
        preg_match('!^(.*)(\?.*)$!', $address, $match);

        if (!empty($match[2])) {
            throw new RuntimeError("mailto: hex encoding does not work with extra attributes. Try javascript.");
        }

        $addressEncode = '';
        for ($x = 0, $xMax = strlen($address); $x < $xMax; $x++) {
            if (preg_match('!\w!', $address[$x])) {
                $addressEncode .= '%' . bin2hex($address[$x]);
            } else {
                $addressEncode .= $address[$x];
            }
        }

        $textEncode = '';
        for ($x = 0, $xMax = strlen($text); $x < $xMax; $x++) {
            $textEncode .= '&#x' . bin2hex($text[$x]) . ';';
        }

        $mailto = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;";

        return "<a href=\"$mailto$addressEncode\" $extra>$textEncode</a>";
    }
}
