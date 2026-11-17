<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductOptionWrapper\Test\Unit\Plugin\Catalog\Block\Product\View;

use Infrangible\CatalogProductOptionWrapper\Helper\Data;
use Infrangible\CatalogProductOptionWrapper\Plugin\Catalog\Block\Product\View\Options;
use Magento\Catalog\Model\Product\Option;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the plugin wraps the HTML of the options of the product page.
 */
class OptionsTest extends TestCase
{
    /**
     * Tests that the HTML of the option is wrapped with the wrapper block of the options block.
     *
     * @return void
     */
    public function testAfterGetOptionHtml(): void
    {
        $subject = $this->createStub(\Magento\Catalog\Block\Product\View\Options::class);
        $option = $this->createStub(Option::class);

        $helper = $this->createMock(Data::class);
        $helper->expects($this->once())->method('renderWrapper')->with($subject, $option, '<input>')->willReturn(
            '<div><input></div>'
        );

        $this->assertSame(
            '<div><input></div>',
            (new Options($helper))->afterGetOptionHtml(
                $subject,
                '<input>',
                $option
            )
        );
    }
}
