<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Block\Adminhtml\Rule;

use Magento\Backend\Block\Template;
use Tonyapps\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;

class Index extends Template
{
    public function __construct(Template\Context $context, private readonly CollectionFactory $collectionFactory, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function getRules()
    {
        return $this->collectionFactory->create()->setOrder('sort_order')->setOrder('rule_id');
    }
}
