<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductOptionWrapper\Test\Unit\Block\Product\View\Options;

use FeWeDev\Base\Variables;
use Infrangible\CatalogProductOptionWrapper\Block\Product\View\Options\Option;
use LogicException;
use Magento\Catalog\Model\Product\Option as ProductOption;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\TemplateEngine\Php;
use Magento\Framework\ZendEscaper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * Tests the wrapper of product options: its CSS classes and that the template escapes the values of the option.
 */
class OptionTest extends TestCase
{
    /**
     * Returns a product option with the ID 12 and the title, type and code, as strings like Magento loads them.
     *
     * @param string|null $title
     * @param string|null $type
     * @param string|null $code
     *
     * @return ProductOption
     */
    private function createProductOption(?string $title, ?string $type, ?string $code): ProductOption
    {
        $option = $this->createStub(ProductOption::class);

        $option->method('getId')->willReturn('12');
        $option->method('getTitle')->willReturn($title);
        $option->method('getType')->willReturn($type);
        $option->method('getData')->willReturnCallback(
            fn(string $key = '') => $key === 'code' ? $code : null
        );

        return $option;
    }

    /**
     * Returns the wrapper block with the option and its HTML.
     *
     * @param ProductOption|null $option
     * @param string|null $optionHtml
     *
     * @return Option
     */
    private function createBlock(?ProductOption $option, ?string $optionHtml = null): Option
    {
        return new Option(
            $this->createStub(Context::class),
            new Variables(),
            [
                'option'      => $option,
                'option_html' => $optionHtml
            ]
        );
    }

    /**
     * Returns option titles and the title in the CSS class.
     *
     * @return array
     * @phpstan-return array<string, array{string|null, string}>
     */
    public static function getOptionTitles(): array
    {
        return [
            'words'          => ['Gift Wrap', 'gift__wrap'],
            'camel case'     => ['GiftWrap', 'gift_wrap'],
            'lower case'     => ['size', 'size'],
            'abbreviation'   => ['XL Size', 'xl__size'],
            'no title'       => [null, '']
        ];
    }

    /**
     * Tests the title of the option in the CSS class.
     *
     * @dataProvider getOptionTitles
     *
     * @param string|null $title
     * @param string $expected
     *
     * @return void
     */
    #[DataProvider('getOptionTitles')]
    public function testGetOptionTitle(?string $title, string $expected): void
    {
        $block = $this->createBlock($this->createProductOption($title, 'drop_down', null));

        $this->assertSame($expected, $block->getOptionTitle());
    }

    /**
     * Tests the CSS classes for the type, title and code of the option.
     *
     * @return void
     */
    public function testGetWrapperClasses(): void
    {
        $block = $this->createBlock($this->createProductOption('Gift Wrap', 'checkbox', 'gift'));

        $this->assertSame(
            ['field-wrapper', 'field-wrapper-checkbox', 'field-wrapper-gift__wrap', 'field-wrapper-gift'],
            $block->getWrapperClasses()
        );
    }

    /**
     * Tests that an option without title and code gets no empty classes.
     *
     * @return void
     */
    public function testGetWrapperClassesWithoutTitleAndCode(): void
    {
        $block = $this->createBlock($this->createProductOption(null, 'field', null));

        $this->assertSame(['field-wrapper', 'field-wrapper-field'], $block->getWrapperClasses());
    }

    /**
     * Tests the ID of the option, which Magento loads as string.
     *
     * @return void
     */
    public function testGetOptionId(): void
    {
        $block = $this->createBlock($this->createProductOption('Size', 'field', null));

        $this->assertSame(12, $block->getOptionId());
    }

    /**
     * Tests that the block without option is a programming error.
     *
     * @return void
     */
    public function testGetOptionWithoutOption(): void
    {
        $this->expectException(LogicException::class);

        $this->createBlock(null)->getOption();
    }

    /**
     * Tests that the template escapes the title and code of the option, which admin users enter, and outputs the HTML
     * of the option unchanged.
     *
     * @return void
     * @throws Throwable
     */
    public function testTemplateEscapesOptionValues(): void
    {
        $block = $this->createBlock(
            $this->createProductOption('Size"><script>alert(1)</script>', 'field', '"><b>'),
            '<input type="text" name="options[12]">'
        );

        $html = $this->renderTemplate($block);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('"><b>', $html);
        $this->assertStringContainsString('id="product-option-12"', $html);
        $this->assertStringContainsString('data-option-id="12"', $html);
        $this->assertStringContainsString('<input type="text" name="options[12]">', $html);
    }

    /**
     * Renders the template of the wrapper with Magento's PHP template engine, the block and a real escaper, as Magento
     * does.
     *
     * @param Option $block
     *
     * @return string
     * @throws Throwable
     */
    private function renderTemplate(Option $block): string
    {
        // Magento's escaper gets the inline translation and the Laminas escaper from the object manager
        $objectManager = $this->createStub(ObjectManagerInterface::class);
        $objectManager->method('get')->willReturnCallback($this->createObject(...));
        ObjectManager::setInstance($objectManager);

        return (new Php($objectManager))->render(
            $block,
            __DIR__ . '/../../../../../../view/frontend/templates/product/view/options/option.phtml',
            ['block' => $block, 'escaper' => new Escaper()]
        );
    }

    /**
     * Returns the real Laminas escaper, and a stub of other classes, as the object manager does.
     *
     * @param string $type
     * @phpstan-param class-string $type
     *
     * @return object
     */
    private function createObject(string $type): object
    {
        return $type === ZendEscaper::class ? new ZendEscaper() : $this->createStub($type);
    }
}
