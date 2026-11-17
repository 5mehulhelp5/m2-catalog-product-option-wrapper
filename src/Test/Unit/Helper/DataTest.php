<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductOptionWrapper\Test\Unit\Helper;

use Infrangible\CatalogProductOptionWrapper\Helper\Data;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

/**
 * Tests that the option HTML is wrapped with the wrapper block if the block has one.
 */
class DataTest extends TestCase
{
    /**
     * Tests that the option HTML is unchanged without wrapper block.
     *
     * @return void
     */
    public function testRenderWrapperWithoutWrapperBlock(): void
    {
        $block = $this->createStub(Template::class);
        $block->method('getChildBlock')->willReturn(false);

        $this->assertSame(
            '<input>',
            (new Data())->renderWrapper(
                $block,
                $this->createStub(Option::class),
                '<input>'
            )
        );
    }

    /**
     * Tests that the wrapper block gets the option and its HTML and renders the result.
     *
     * @return void
     */
    public function testRenderWrapperWithWrapperBlock(): void
    {
        $option = $this->createStub(Option::class);

        $wrapperBlock = $this->createMock(Template::class);
        $wrapperBlock->expects($this->exactly(2))->method('setData')->willReturnCallback(
            function (string $key, mixed $value) use ($option, $wrapperBlock): Template {
                $this->assertSame($key === 'option' ? $option : '<input>', $value);

                return $wrapperBlock;
            }
        );
        $wrapperBlock->method('toHtml')->willReturn('<div><input></div>');

        $block = $this->createMock(Template::class);
        $block->expects($this->once())->method('getChildBlock')->with('custom_wrapper')->willReturn($wrapperBlock);

        $this->assertSame(
            '<div><input></div>',
            (new Data())->renderWrapper(
                $block,
                $option,
                '<input>',
                'custom_wrapper'
            )
        );
    }
}
