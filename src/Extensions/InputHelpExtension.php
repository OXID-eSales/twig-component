<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\InputHelpLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class InputHelpExtension extends AbstractExtension
{
    public function __construct(private InputHelpLogic $inputHelpLogic)
    {
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('help_id', [$this, 'getHelpId']),
            new TwigFunction('help_text', [$this, 'getHelpText'])
        ];
    }

    /**
     * @param array $ident
     *
     * @return mixed
     */
    public function getHelpId($ident)
    {
        return $this->inputHelpLogic->getIdent(['ident' => $ident]);
    }

    /**
     * @param array $ident
     *
     * @return mixed
     */
    public function getHelpText($ident)
    {
        return $this->inputHelpLogic->getTranslation(['ident' => $ident]);
    }
}
