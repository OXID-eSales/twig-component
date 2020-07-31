<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration\Extensions;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\ScriptLogic;
use OxidEsales\Twig\Extensions\ScriptExtension;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Template;

final class ScriptExtensionTest extends AbstractExtensionTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new ScriptExtension(new ScriptLogic());
    }

    /**
     * @param string $template
     * @param string $expected
     *
     * @covers ScriptExtension::script
     * @dataProvider getScriptTests
     */
    public function testScript(string $template, string $expected): void
    {
        $this->assertEquals($expected, $this->getTemplate($template)->render([]));
    }

    /**
     * @return array
     */
    public function getScriptTests(): array
    {
        return [
            // Empty buffer
            [
                "{{ script() }}",
                ""
            ],
            // One script
            [
                "{{ script({ add: 'alert();' }) }}" .
                "{{ script() }}",
                "<script type='text/javascript'>alert();</script>"
            ],
            // Two scripts
            [
                "{{ script({ add: 'alert(\"one\");' }) }}" .
                "{{ script({ add: 'alert(\"two\");' }) }}" .
                "{{ script() }}",
                "<script type='text/javascript'>alert(\"one\");\n" .
                "alert(\"two\");</script>"
            ],
            // Include
            [
                "{{ script({ include: 'http://someurl/src/js/libs/jquery.min.js' }) }}" .
                "{{ script() }}",
                "<script type=\"text/javascript\" src=\"http://someurl/src/js/libs/jquery.min.js\"></script>"
            ],
            // Two includes
            [
                "{{ script({ include: 'http://someurl/src/js/libs/jquery.min.js' }) }}" .
                "{{ script({ include: 'http://another/src/js/libs/jquery.min.js' }) }}" .
                "{{ script() }}",
                "<script type=\"text/javascript\" src=\"http://someurl/src/js/libs/jquery.min.js\"></script>\n" .
                "<script type=\"text/javascript\" src=\"http://another/src/js/libs/jquery.min.js\"></script>"
            ],
            // Two scripts, two includes
            [
                "{{ script({ add: 'alert(\"one\");' }) }}" .
                "{{ script({ include: 'http://someurl/src/js/libs/jquery.min.js' }) }}" .
                "{{ script({ add: 'alert(\"two\");' }) }}" .
                "{{ script({ include: 'http://another/src/js/libs/jquery.min.js' }) }}" .
                "{{ script() }}",
                "<script type=\"text/javascript\" src=\"http://someurl/src/js/libs/jquery.min.js\"></script>\n" .
                "<script type=\"text/javascript\" src=\"http://another/src/js/libs/jquery.min.js\"></script>" .
                "<script type='text/javascript'>alert(\"one\");\n" .
                "alert(\"two\");</script>"
            ],
            // Include widget
            [
                "{{ script({ include: 'http://someurl/src/js/libs/jquery.min.js' }) }}" .
                "{{ script({ widget: 'somewidget', inWidget: true }) }}",
                <<<HTML
<script type='text/javascript'>
    window.addEventListener('load', function() {
        WidgetsHandler.registerFile('http://someurl/src/js/libs/jquery.min.js', 'somewidget');
    }, false)
</script>
HTML
            ],
            // Add widget
            [
                "{{ script({ add: 'alert();' }) }}" .
                "{{ script({ widget: 'somewidget', inWidget: true }) }}",
                "<script type='text/javascript'>window.addEventListener('load', function() { WidgetsHandler.registerFunction('alert();', 'somewidget'); }, false )</script>"
            ]
        ];
    }

    /**
     * @param string $template
     *
     * @return Template
     */
    protected function getTemplate(string $template): Template
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addGlobal('__oxid_include_dynamic', true);
        $twig->addExtension($this->extension);

        return $twig->loadTemplate($twig->getTemplateClass('index'), 'index');
    }
}
