<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Tonyapps\ProductLabels\Model\LabelProvider;

class ProductLabels extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly Registry $registry,
        private readonly LabelProvider $labelProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getProduct(): ?Product
    {
        $product = $this->registry->registry('current_product') ?? $this->registry->registry('product');
        return $product instanceof Product ? $product : null;
    }

    public function getLabels(?Product $product): array
    {
        return $product ? $this->labelProvider->getLabels($product) : [];
    }
}
