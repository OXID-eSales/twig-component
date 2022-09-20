<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Twig\Tests\Integration;

use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use PHPUnit\Framework\TestCase;

final class FieldValuesEscapingTest extends TestCase
{
    public function testFieldValuesWithConfigurationSetWillNotBeEscaped(): void
    {
        $stringWithSpecialCharacters = '"&a"\'<some-value>\'';

        $field = new Field($stringWithSpecialCharacters);

        $this->assertEquals($stringWithSpecialCharacters, $field->value);
    }

    public function testReviewFieldsWithConfigurationSetWillNotEscapeAndInsertHtmlLineBreaks(): void
    {
        $reviewType = 'oxrecommlist';
        $objectId = uniqid('id-', true);
        $text = "<script>alert();

new\nline
carriage\rreturn";
        for ($i = 0; $i < 2; $i++) {
            $review = oxNew(Review::class);
            $review->oxreviews__oxobjectid = new Field($objectId);
            $review->oxreviews__oxtype = new Field($reviewType);
            $review->oxreviews__oxlang = new Field(0);
            $review->oxreviews__oxtext = new Field($text);
            $review->save();
        }

        $list = (oxNew(Review::class))->loadList($reviewType, $objectId, true, 0);

        foreach ($list as $review) {
            $this->assertEquals($text, $review->getFieldData('oxtext'));
        }
    }
}
