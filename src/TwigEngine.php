<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig;

use OxidEsales\EshopCommunity\Internal\Templating\TemplateEngineInterface;
use OxidEsales\Twig\Escaper\EscaperInterface;
use Twig\Environment;
use Twig\Extension\CoreExtension;
use Twig\Extension\DebugExtension;

/**
 * Class TwigEngine
 */
class TwigEngine implements TemplateEngineInterface
{
    /**
     * @var \Twig_Environment
     */
    private $engine;

    /**
     * TwigEngine constructor.
     *
     * @param Environment $engine
     * @param \iterable    $twigExtensions
     * @param \iterable    $twigEscaper
     */
    public function __construct(Environment $engine, iterable $twigExtensions = [], iterable $twigEscaper = [])
    {
        $this->engine = $engine;

        foreach ($twigExtensions as $extension) {
            $this->engine->addExtension($extension);
        }
        foreach ($twigEscaper as $escaper) {
            $this->addEscaper($escaper);
        }
        if ($this->engine->isDebug()) {
            $this->engine->addExtension(new DebugExtension());
        }
    }

    /**
     * Returns the template file extension.
     *
     * @return string
     */
    public function getDefaultFileExtension(): string
    {
        return 'html.twig';
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
        return $this->engine->render($name, $context);
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
        return $this->engine->getLoader()->exists($name);
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
        $template = $this->engine->createTemplate($fragment, $fragmentId);

        return $template->render($context);
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
        /** @var CoreExtension $coreExtension */
        $coreExtension = $this->engine->getExtension(CoreExtension::class);
        $coreExtension->setEscaper($escaper->getStrategy(), [$escaper, 'escape']);
    }
}
