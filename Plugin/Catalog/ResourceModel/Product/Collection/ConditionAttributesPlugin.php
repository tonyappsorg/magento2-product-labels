<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Plugin\Catalog\ResourceModel\Product\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Tonyapps\ProductLabels\Model\LabelProvider;

class ConditionAttributesPlugin
{
    public function __construct(private readonly LabelProvider $labelProvider)
    {
    }

    public function beforeLoad(Collection $subject, $printQuery = false, $logQuery = false): ?array
    {
        $storeId = (int) $subject->getStoreId();
        $attributeCodes = $this->labelProvider->getConditionAttributeCodes($storeId);
        if ($attributeCodes) {
            $subject->addAttributeToSelect($attributeCodes, 'left');
        }
        return null;
    }
}
