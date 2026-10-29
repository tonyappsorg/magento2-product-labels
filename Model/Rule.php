<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Model;

use Magento\Framework\Data\FormFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\CatalogRule\Model\Rule\Action\CollectionFactory as ActionCollectionFactory;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory;

class Rule extends \Magento\Rule\Model\AbstractModel
{
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        private readonly CombineFactory $conditionFactory,
        private readonly ActionCollectionFactory $actionCollectionFactory,
        ?AbstractResource $resource = null,
        ?AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    protected function _construct(): void
    {
        $this->_init(ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return $this->conditionFactory->create();
    }

    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    public function getConditionsFieldSetId(string $formName = ''): string
    {
        return $formName . 'rule_conditions_fieldset_' . (string) $this->getId();
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $storeIds = $this->getData('store_ids');
        if (is_array($storeIds)) {
            $this->setData('store_ids', implode(',', array_map('intval', $storeIds)));
        }
        return $this;
    }

    /** @return int[] */
    public function getStoreIds(): array
    {
        $ids = $this->getData('store_ids');
        if (is_array($ids)) {
            return array_values(array_filter(array_map('intval', $ids)));
        }
        return array_values(array_filter(array_map('intval', explode(',', (string) $ids))));
    }
}
