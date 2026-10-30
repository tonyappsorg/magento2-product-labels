<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Model;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\Rule\Condition\Product as ProductCondition;
use Tonyapps\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;

class LabelProvider
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly RuleMatcher $ruleMatcher
    )
    {
    }

    /** @return array<int, array{label:string,background_color:string,text_color:string}> */
    public function getLabels(Product $product): array
    {
        $storeId = (int) $product->getData('store_id');
        $labels = [];
        foreach ($this->getRules($storeId) as $rule) {
            if (!$this->ruleMatcher->matches($rule, $product)) {
                continue;
            }
            $labels[] = [
                'label' => (string) $rule->getData('label'),
                'background_color' => (string) $rule->getData('background_color'),
                'text_color' => (string) $rule->getData('text_color'),
            ];
        }
        return $labels;
    }

    /** @return string[] */
    public function getConditionAttributeCodes(int $storeId): array
    {
        $codes = [];
        foreach ($this->getRules($storeId) as $rule) {
            $this->collectAttributeCodes($rule->getConditions(), $codes);
        }
        return array_values(array_unique($codes));
    }

    /** @return Rule[] */
    private function getRules(int $storeId): array
    {
        return $this->collectionFactory->create()->addActiveFilter()->getItems();
    }

    /** @param string[] $codes */
    private function collectAttributeCodes($condition, array &$codes): void
    {
        if ($condition instanceof ProductCondition) {
            $attribute = (string) $condition->getAttribute();
            if ($attribute !== '' && $attribute !== 'category_ids') {
                $codes[] = $attribute;
            }
            return;
        }
        if (method_exists($condition, 'getConditions')) {
            foreach ((array) $condition->getConditions() as $child) {
                $this->collectAttributeCodes($child, $codes);
            }
        }
    }

}
