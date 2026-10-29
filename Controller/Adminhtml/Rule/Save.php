<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Controller\Adminhtml\Rule;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Registry;
use Tonyapps\ProductLabels\Model\ColorValidator;

class Save extends AbstractRule implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        \Tonyapps\ProductLabels\Model\RuleFactory $ruleFactory,
        Registry $coreRegistry,
        TypeListInterface $cacheTypeList,
        private readonly ColorValidator $colorValidator,
        private readonly DataPersistorInterface $dataPersistor
    )
    {
        parent::__construct($context, $ruleFactory, $coreRegistry, $cacheTypeList);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!is_array($data) || !$data) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $id = (int) ($data['rule_id'] ?? 0);
        $rule = $this->ruleFactory->create();
        if ($id) {
            $rule->load($id);
            if (!$rule->getId()) {
                $this->messageManager->addErrorMessage(__('The product label rule no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $label = trim((string) ($data['label'] ?? ''));
        $background = (string) ($data['background_color'] ?? '');
        $foreground = (string) ($data['text_color'] ?? '');
        if ($label === '' || mb_strlen($label) > 80
            || $this->colorValidator->normalize($background) === null
            || $this->colorValidator->normalize($foreground) === null
        ) {
            $this->messageManager->addErrorMessage(__('Enter badge text (up to 80 characters) and valid six-digit hex colors.'));
            $this->dataPersistor->set('tonyapps_product_label_rule', $data);
            return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['rule_id' => $id]);
        }
        $conditions = $data['conditions'] ?? null;
        if (isset($data['rule']['conditions'])) {
            $conditions = $data['rule']['conditions'];
        }
        if (!is_array($conditions) || empty($conditions[1]['conditions'])) {
            $this->messageManager->addErrorMessage(__('Add at least one product condition to the rule.'));
            $this->dataPersistor->set('tonyapps_product_label_rule', $data);
            return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['rule_id' => $id]);
        }
        unset($data['form_key'], $data['rule'], $data['rule_id']);
        $data['conditions'] = $conditions;
        $data['label'] = $label;
        $data['background_color'] = $this->colorValidator->normalize($background);
        $data['text_color'] = $this->colorValidator->normalize($foreground);
        $data['is_active'] = (int) ($data['is_active'] ?? 0) === 1 ? 1 : 0;
        $data['sort_order'] = max(0, (int) ($data['sort_order'] ?? 0));
        $data['store_ids'] = isset($data['store_ids']) && is_array($data['store_ids']) ? $data['store_ids'] : [];

        try {
            $rule->loadPost($data)->addData($data)->save();
            $this->cleanCaches();
            $this->messageManager->addSuccessMessage(__('The product label rule has been saved.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(__('The product label rule could not be saved.'));
            $this->dataPersistor->set('tonyapps_product_label_rule', $data);
            return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['rule_id' => $id]);
        }
    }
}
