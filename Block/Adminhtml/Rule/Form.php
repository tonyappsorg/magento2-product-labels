<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Block\Adminhtml\Rule;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as ConditionsRenderer;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Store\Model\System\Store as SystemStore;
use Tonyapps\ProductLabels\Controller\Adminhtml\Rule\AbstractRule;
use Tonyapps\ProductLabels\Model\Rule;

class Form extends Generic
{
    private const FORM_NAME = 'tonyapps_product_label_form';

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        Registry $registry,
        FormFactory $formFactory,
        private readonly ConditionsRenderer $conditionsRenderer,
        private readonly Fieldset $rendererFieldset,
        private readonly Yesno $yesNo,
        private readonly SystemStore $systemStore,
        private readonly DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        /** @var Rule $rule */
        $rule = $this->_coreRegistry->registry(AbstractRule::REGISTRY_KEY);
        $form = $this->_formFactory->create(['data' => [
            'id' => self::FORM_NAME,
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post',
        ]]);
        $form->setHtmlIdPrefix('product_label_');
        $fieldset = $form->addFieldset('general', ['legend' => __('Badge')]);
        if ($rule->getId()) {
            $fieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);
        }
        $fieldset->addField('name', 'text', ['name' => 'name', 'label' => __('Admin Rule Name'), 'required' => true]);
        $fieldset->addField('label', 'text', ['name' => 'label', 'label' => __('Badge Text'), 'required' => true, 'maxlength' => 80]);
        $fieldset->addField('background_color', 'text', ['name' => 'background_color', 'label' => __('Background Color'), 'class' => 'validate-hex-color', 'required' => true, 'note' => __('Hex color, for example #D32F2F.')]);
        $fieldset->addField('text_color', 'text', ['name' => 'text_color', 'label' => __('Text Color'), 'class' => 'validate-hex-color', 'required' => true]);
        $fieldset->addField('is_active', 'select', ['name' => 'is_active', 'label' => __('Active'), 'values' => $this->yesNo->toOptionArray()]);
        $fieldset->addField('sort_order', 'text', ['name' => 'sort_order', 'label' => __('Sort Order'), 'class' => 'validate-digits']);
        $fieldset->addField('store_ids', 'multiselect', ['name' => 'store_ids[]', 'label' => __('Store Views'), 'values' => $this->systemStore->getStoreValuesForForm(false, false), 'note' => __('Leave empty to use in every store view.')]);

        $conditionFieldsetId = $rule->getConditionsFieldSetId(self::FORM_NAME);
        $renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('tonyapps_productlabels/rule/newConditionHtml', [
                'form' => $conditionFieldsetId,
                'form_namespace' => self::FORM_NAME,
            ]))->setFieldSetId($conditionFieldsetId);
        $conditionsFieldset = $form->addFieldset('conditions_fieldset', ['legend' => __('Conditions (match product attributes)')])->setRenderer($renderer);
        $conditionsFieldset->addField('conditions', 'text', [
            'name' => 'conditions', 'label' => __('Conditions'), 'required' => true, 'data-form-part' => self::FORM_NAME,
        ])->setRule($rule)->setRenderer($this->conditionsRenderer);

        $values = array_merge([
            'is_active' => 1,
            'sort_order' => 0,
            'background_color' => '#333333',
            'text_color' => '#FFFFFF',
        ], $rule->getData());
        $values['store_ids'] = $rule->getStoreIds();
        $persisted = $this->dataPersistor->get('tonyapps_product_label_rule');
        if (is_array($persisted)) {
            $values = array_merge($values, $persisted);
            if (is_array($persisted['conditions'] ?? null)) {
                $rule->loadPost(['conditions' => $persisted['conditions']]);
            }
            $this->dataPersistor->clear('tonyapps_product_label_rule');
        }
        $form->setValues($values);
        $form->setUseContainer(false);
        $this->setForm($form);
        $this->setConditionFormName($rule->getConditions(), self::FORM_NAME);
        return parent::_prepareForm();
    }

    private function setConditionFormName(AbstractCondition $condition, string $formName): void
    {
        $condition->setFormName($formName);
        foreach ((array) $condition->getConditions() as $child) {
            if ($child instanceof AbstractCondition) {
                $this->setConditionFormName($child, $formName);
            }
        }
    }
}
