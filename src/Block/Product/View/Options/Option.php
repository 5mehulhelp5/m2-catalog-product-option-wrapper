<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductOptionWrapper\Block\Product\View\Options;

use FeWeDev\Base\Variables;
use LogicException;
use Magento\Catalog\Model\Product\Option as ProductOption;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Renders the element around a product option that identifies the option by its ID, type, title and code.
 */
class Option extends Template
{
    /**
     * Creates the block.
     *
     * @param Context $context
     * @param Variables $variables
     * @param array $data
     *
     * @phpstan-param array<string, mixed> $data
     */
    public function __construct(Context $context, protected Variables $variables, array $data = [])
    {
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Returns the product option to wrap.
     *
     * @return ProductOption
     * @throws LogicException
     */
    public function getOption(): ProductOption
    {
        $option = $this->getData('option');

        if (! $option instanceof ProductOption) {
            throw new LogicException('No product option to wrap');
        }

        return $option;
    }

    /**
     * Returns the HTML of the product option to wrap.
     *
     * @return string
     */
    public function getOptionHtml(): string
    {
        return $this->stringValue($this->getData('option_html'));
    }

    /**
     * Returns the ID of the product option.
     *
     * @return int
     */
    public function getOptionId(): int
    {
        return $this->variables->intValue($this->getOption()->getId());
    }

    /**
     * Returns the title of the product option as part of a CSS class.
     *
     * The title is in lower case, with spaces replaced by underscores and an underscore before upper case letters, for
     * example "gift__wrap" for "Gift Wrap" and "gift_wrap" for "GiftWrap".
     *
     * @return string
     */
    public function getOptionTitle(): string
    {
        $title = preg_replace(
            '/\s/',
            '_',
            $this->stringValue($this->getOption()->getTitle())
        );

        $title = preg_replace(
            '/[A-Z]([A-Z](?![a-z]))*/',
            '_$0',
            (string)$title
        );

        return ltrim(
            strtolower((string)$title),
            '_'
        );
    }

    /**
     * Returns the CSS classes of the wrapper.
     *
     * These are field-wrapper and one class each for the type, title and code of the option.
     *
     * @return string[]
     */
    public function getWrapperClasses(): array
    {
        $option = $this->getOption();

        $classes = ['field-wrapper'];

        $names = [
            $this->stringValue($option->getType()),
            $this->getOptionTitle(),
            $this->stringValue($option->getData('code'))
        ];

        foreach ($names as $name) {
            if ($name !== '') {
                $classes[] = 'field-wrapper-' . $name;
            }
        }

        return $classes;
    }

    /**
     * Converts the value to a string, null to an empty string.
     *
     * @param mixed $value
     *
     * @return string
     */
    private function stringValue(mixed $value): string
    {
        return $value === null ? '' : $this->variables->stringValue($value);
    }
}
