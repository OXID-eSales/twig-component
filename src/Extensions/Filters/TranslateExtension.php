<?php declare(strict_types=1);

namespace OxidEsales\Twig\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TranslateFilterLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class TranslateExtension
 */
class TranslateExtension extends AbstractExtension
{
    /**
     * @var TranslateFilterLogic
     */
    private $multiLangFilterLogic;

    /**
     * TranslateExtension constructor.
     *
     * @param TranslateFilterLogic $multiLangFilterLogic
     */
    public function __construct(TranslateFilterLogic $multiLangFilterLogic)
    {
        $this->multiLangFilterLogic = $multiLangFilterLogic;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('translate', [$this, 'translate'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param string $ident
     * @param mixed  $arguments
     *
     * @return string
     */
    public function translate($ident, $arguments = null): string
    {
        return $this->multiLangFilterLogic->multiLang($ident, $arguments);
    }
}
