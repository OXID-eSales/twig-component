<?php declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\WordwrapLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class WordwrapExtension
 */
class WordwrapExtension extends AbstractExtension
{
    /**
     * @var WordwrapLogic
     */
    private $wordwrapLogic;

    /**
     * WordwrapExtension constructor.
     *
     * @param WordwrapLogic $wordwrapLogic
     */
    public function __construct(WordwrapLogic $wordwrapLogic)
    {
        $this->wordwrapLogic = $wordwrapLogic;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('wordwrap', [$this, 'wordwrap'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string  $string
     * @param integer $length
     * @param string  $wrapper
     * @param bool    $cut
     *
     * @return string
     */
    public function wordWrap($string, $length = 80, $wrapper = "\n", $cut = false): string
    {
        return $this->wordwrapLogic->wordwrap($string, $length, $wrapper, $cut);
    }
}
