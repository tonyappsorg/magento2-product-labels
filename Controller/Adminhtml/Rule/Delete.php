<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Controller\Adminhtml\Rule;

use Magento\Framework\App\Action\HttpPostActionInterface;

class Delete extends AbstractRule implements HttpPostActionInterface
{
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('rule_id');
        $rule = $this->ruleFactory->create()->load($id);
        if ($id && $rule->getId()) {
            $rule->delete();
            $this->cleanCaches();
            $this->messageManager->addSuccessMessage(__('The product label rule was deleted.'));
        } else {
            $this->messageManager->addErrorMessage(__('The product label rule no longer exists.'));
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
