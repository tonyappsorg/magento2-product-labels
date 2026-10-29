<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Controller\Adminhtml\Rule;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Edit extends AbstractRule implements HttpGetActionInterface
{
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('rule_id');
        $rule = $this->ruleFactory->create();
        if ($id) {
            $rule->load($id);
            if (!$rule->getId()) {
                $this->messageManager->addErrorMessage(__('The product label rule no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $this->coreRegistry->register(self::REGISTRY_KEY, $rule);
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->setActiveMenu(self::ADMIN_RESOURCE);
        $page->getConfig()->getTitle()->prepend($id ? (string) $rule->getData('name') : __('New Product Label'));
        return $page;
    }
}
