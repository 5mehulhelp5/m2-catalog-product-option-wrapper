<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionWrapper\Plugin\Catalog\Block\Product\View;

use Infrangible\CatalogProductOptionWrapper\Helper\Data;
use Magento\Catalog\Model\Product\Option;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Options
{
    /** @var Data */
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

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
