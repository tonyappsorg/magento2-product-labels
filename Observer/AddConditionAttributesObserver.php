<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Tonyapps\ProductLabels\Model\LabelProvider;

/** Selects rule-condition attributes on product-listing collections only (catalog_block_product_list_collection). */
class AddConditionAttributesObserver implements ObserverInterface
{
    public function __construct(private readonly LabelProvider $labelProvider)
    {
    }

    public function execute(Observer $observer): void
    {
        $collection = $observer->getEvent()->getData('collection');
        if (!$collection instanceof Collection) {
            return;
        }
        $entity = $collection->getEntity();
        // A rule may still reference a deleted attribute; core addAttributeToSelect() would fatal on it.
        $attributeCodes = array_values(array_filter(
            $this->labelProvider->getConditionAttributeCodes((int) $collection->getStoreId()),
            static fn (string $code): bool => (bool) $entity->getAttribute($code)
        ));
        if ($attributeCodes) {
            $collection->addAttributeToSelect($attributeCodes, 'left');
        }
    }
}
