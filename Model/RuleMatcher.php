<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Model;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\Rule\Condition\Combine;

class RuleMatcher
{
    public function matches(Rule $rule, Product $product): bool
    {
        $storeIds = $rule->getStoreIds();
        if ($storeIds && !in_array((int) $product->getData('store_id'), $storeIds, true)) {
            return false;
        }
        $conditions = $rule->getConditions();
        return $conditions instanceof Combine && (bool) $conditions->getConditions() && $conditions->validate($product);
    }
}
