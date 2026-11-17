<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductOptionWrapper\Helper;

use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Wraps the HTML of product options with the wrapper block, also in the option lists of other modules.
 */
class Data
{
    /**
     * Renders the option HTML with the wrapper block that is a child of the block.
     *
     * Without such a child, the option HTML is returned unchanged.
     *
     * @param AbstractBlock $block
     * @param Option $option
     * @param string $optionHtml
     * @param string $wrapperBlockName
     *
     * @return string
     */
    public function renderWrapper(
        AbstractBlock $block,
        Option $option,
        string $optionHtml,
        string $wrapperBlockName = 'product_option_wrapper'
    ): string {
        $wrapperBlock = $block->getChildBlock($wrapperBlockName);

        if (! $wrapperBlock instanceof AbstractBlock) {
            return $optionHtml;
        }

        $wrapperBlock->setData(
            'option',
            $option
        );

        $wrapperBlock->setData(
            'option_html',
            $optionHtml
        );

        return $wrapperBlock->toHtml();
    }
}
