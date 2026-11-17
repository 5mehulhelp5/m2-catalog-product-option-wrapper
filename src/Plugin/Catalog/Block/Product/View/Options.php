<?php

declare(strict_types=1);

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Infrangible\CatalogProductOptionWrapper\Plugin\Catalog\Block\Product\View;

use Infrangible\CatalogProductOptionWrapper\Helper\Data;
use Magento\Catalog\Model\Product\Option;

/**
 * Wraps the HTML of every custom option on the product page.
 */
class Options
{
    /**
     * Creates the plugin.
     *
     * @param Data $helper
     */
    public function __construct(protected Data $helper)
    {
    }

    /**
     * Wraps the HTML of the option.
     *
     * @param \Magento\Catalog\Block\Product\View\Options $subject
     * @param string $result
     * @param Option $option
     *
     * @return string
     */
    public function afterGetOptionHtml(
        \Magento\Catalog\Block\Product\View\Options $subject,
        string $result,
        Option $option
    ): string {
        return $this->helper->renderWrapper(
            $subject,
            $option,
            $result
        );
    }
}
