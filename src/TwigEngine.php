<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateEngineInterface;
use OxidEsales\Twig\Escaper\EscaperInterface;
use OxidEsales\Twig\Resolver\TemplateChain\TemplateChainResolverInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\EscaperExtension;

class TwigEngine implements TemplateEngineInterface
{
    public function __construct(
        private Environment $engine,
        private string $fileExtension,
        private TemplateChainResolverInterface $templateChainResolver,
        iterable $twigExtensions = [],
        iterable $twigEscaper = []
    ) {
        foreach ($twigExtensions as $extension) {
            if (!$this->engine->hasExtension($extension::class)) {
                $this->engine->addExtension($extension);
            }
        }
        foreach ($twigEscaper as $escaper) {
            $this->addEscaper($escaper);
        }
        if ($this->engine->isDebug()) {
            $this->engine->addExtension(new DebugExtension());
        }
    }

    public function render(string $name, array $context = []): string
    {
        return $this->engine->render(
            $this->templateChainResolver->getLastChild($name),
            $context
        );
    }

    public function exists(string $name): bool
    {
        if (!str_ends_with($name, $this->fileExtension)) {
            $name .= ".$this->fileExtension";
        }
        return $this->engine->getLoader()->exists($name);
    }

    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        return $this->engine
            ->createTemplate($fragment, $fragmentId)
            ->render($context);
    }

    public function addGlobal(string $name, $value)
    {
        $this->engine->addGlobal($name, $value);
    }

    public function getGlobals(): array
    {
        return $this->engine->getGlobals();
    }

    public function addEscaper(EscaperInterface $escaper)
    {
        $this->engine
            ->getExtension(EscaperExtension::class)
            ->setEscaper($escaper->getStrategy(), [$escaper, 'escape']);
    }
}
