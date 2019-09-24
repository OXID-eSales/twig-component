<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Twig\Tests\Unit\Extensions\Filters;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\FileSizeLogic;
use OxidEsales\Twig\Extensions\Filters\FileSizeExtension;
use PHPUnit\Framework\TestCase;

class FileSizeExtensionTest extends TestCase
{

    public function provider()
    {
        return [
            [1023, '1023 B'],
            [1025, '1.0 KB'],
            [1024 * 1024 * 1.1, '1.1 MB'],
            [1024 * 1024 * 1024 * 1.3, '1.3 GB']
        ];
    }

    /**
     * @param string $fileSize
     * @param string $expectedFileSize
     *
     * @dataProvider provider
     * @covers \OxidEsales\Twig\Extensions\Filters\FileSizeExtension::fileSize
     */
    public function testFileSize($fileSize, $expectedFileSize)
    {
        $fileSizeLogic = new FileSizeLogic();
        $fileSizeExtension = new FileSizeExtension($fileSizeLogic);
        $actualFileSize = $fileSizeExtension->fileSize($fileSize);
        $this->assertEquals($expectedFileSize, $actualFileSize);
    }
}
