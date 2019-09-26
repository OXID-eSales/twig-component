<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\AssignAdvancedLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AssignAdvancedExtension
 *
 * @package OxidEsales\Twig\Extensions
 */
class AssignAdvancedExtension extends AbstractExtension
{
    /**
     * @var AssignAdvancedLogic
     */
    private $assignAdvancedLogic;

    /**
     * AssignAdvancedExtension constructor.
     *
     * @param AssignAdvancedLogic $assignAdvancedLogic
     */
    public function __construct(AssignAdvancedLogic $assignAdvancedLogic)
    {
        $this->assignAdvancedLogic = $assignAdvancedLogic;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'assign_advanced',
                [$this, 'assignAdvanced'],
                ['deprecated' => true, 'alternative' => 'Twig array syntax']
            )
        ];
    }

    /**
     * Calles formatValue function to format arrays and range()
     *
     * @param string $value
     *
     * @return mixed
     */
    public function assignAdvanced(string $value): string
    {
        $formattedValue = $this->assignAdvancedLogic->formatValue($value);

        return $formattedValue;
    }
}
