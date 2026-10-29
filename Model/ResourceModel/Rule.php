<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rule extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('tonyapps_product_label_rule', 'rule_id');
    }
}
