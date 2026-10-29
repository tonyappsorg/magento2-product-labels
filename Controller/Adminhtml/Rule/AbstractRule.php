<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Registry;
use Tonyapps\ProductLabels\Model\RuleFactory;

abstract class AbstractRule extends Action
{
    public const ADMIN_RESOURCE = 'Tonyapps_ProductLabels::rules';
    public const REGISTRY_KEY = 'tonyapps_productlabels_current_rule';

    public function __construct(
        Context $context,
        protected readonly RuleFactory $ruleFactory,
        protected readonly Registry $coreRegistry,
        protected readonly TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
    }

    protected function cleanCaches(): void
    {
        $this->cacheTypeList->cleanType('block_html');
        $this->cacheTypeList->cleanType('full_page');
    }
}
