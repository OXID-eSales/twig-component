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

    /**
     * Renders a template.
     *
     * @param string $name    A template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \RuntimeException if the template cannot be rendered
     */
    public function render(string $name, array $context = []): string
    {
        return $this->engine->render(
            $this->templateChainResolver->getLastChild($name),
            $context
        );
    }

    /**
     * Returns true if the template exists.
     *
     * @param string $name A template name
     *
     * @return bool true if the template exists, false otherwise
     *
     * @throws \RuntimeException if the engine cannot handle the template name
     */
    public function exists(string $name): bool
    {
        return $this->engine->getLoader()->exists($name . '.' . $this->fileExtension);
    }

    /**
     * Renders a fragment of the template.
     *
     * @param string $fragment   The template fragment to render
     * @param string $fragmentId The Id of the fragment
     * @param array  $context    An array of parameters to pass to the template
     *
     * @return string
     */
    public function renderFragment(string $fragment, string $fragmentId, array $context = []): string
    {
        return $this->engine
            ->createTemplate($fragment, $fragmentId)
            ->render($context);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function addGlobal(string $name, $value)
    {
        $this->engine->addGlobal($name, $value);
    }

    /**
     * Returns assigned globals.
     *
     * @return array
     */
    public function getGlobals(): array
    {
        return $this->engine->getGlobals();
    }

    /**
     * @param EscaperInterface $escaper
     */
    public function addEscaper(EscaperInterface $escaper)
    {
        /** @var EscaperExtension $escaperExtension */
        $escaperExtension = $this->engine->getExtension(EscaperExtension::class);
        $escaperExtension->setEscaper($escaper->getStrategy(), [$escaper, 'escape']);
    }
}
