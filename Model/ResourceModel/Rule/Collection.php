<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Model\ResourceModel\Rule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tonyapps\ProductLabels\Model\Rule as RuleModel;
use Tonyapps\ProductLabels\Model\ResourceModel\Rule as RuleResource;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(RuleModel::class, RuleResource::class);
    }

    public function addActiveFilter(): self
    {
        $this->addFieldToFilter('is_active', 1);
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);
        $this->setOrder('rule_id', self::SORT_ORDER_ASC);
        return $this;
    }
}
