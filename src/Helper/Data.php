<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionWrapper\Helper;

use Infrangible\CatalogProductOptionWrapper\Block\Product\View\Options\Option;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    public function renderWrapper(
        AbstractBlock $block,
        \Magento\Catalog\Model\Product\Option $option,
        string $optionHtml,
        string $wrapperBlockName = 'product_option_wrapper'
    ): string {
        /** @var Option $block */
        $block = $block->getChildBlock($wrapperBlockName);

        if ($block) {
            $block->setData(
                'option',
                $option
            );

            $block->setData(
                'option_html',
                $optionHtml
            );

            return $block->toHtml();
        } else {
            return $optionHtml;
        }
    }
}
