<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Controller\Adminhtml\Rule;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Rule\Model\Condition\AbstractCondition;

class NewConditionHtml extends AbstractRule implements HttpPostActionInterface
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Tonyapps\ProductLabels\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        private readonly ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context, $ruleFactory, $coreRegistry, $cacheTypeList);
    }

    public function execute()
    {
        $typeParts = explode('|', str_replace('-', '/', (string) $this->getRequest()->getParam('type', '')));
        $type = $typeParts[0] ?? '';
        $allowed = [
            \Magento\CatalogRule\Model\Rule\Condition\Combine::class,
            \Magento\CatalogRule\Model\Rule\Condition\Product::class,
        ];
        if (!in_array($type, $allowed, true)) {
            $this->getResponse()->setBody('');
            return;
        }
        $condition = $this->objectManager->create($type)
            ->setId($this->getRequest()->getParam('id'))
            ->setType($type)
            ->setRule($this->ruleFactory->create())
            ->setPrefix('conditions');
        if (!empty($typeParts[1])) {
            $condition->setAttribute($typeParts[1]);
        }
        if ($condition instanceof AbstractCondition) {
            $formName = (string) $this->getRequest()->getParam('form_namespace', '');
            $condition->setFormName($formName)->setJsFormObject((string) $this->getRequest()->getParam('form', ''));
            $this->getResponse()->setBody($condition->asHtmlRecursive());
        }
    }
}
