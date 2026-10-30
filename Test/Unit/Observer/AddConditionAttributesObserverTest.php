<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Test\Unit\Observer;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use PHPUnit\Framework\TestCase;
use Tonyapps\ProductLabels\Model\LabelProvider;
use Tonyapps\ProductLabels\Observer\AddConditionAttributesObserver;

class AddConditionAttributesObserverTest extends TestCase
{
    public function testSkipsAttributesThatNoLongerExist(): void
    {
        $entity = $this->createMock(AbstractEntity::class);
        $entity->method('getAttribute')->willReturnMap([
            ['color', $this->createMock(AbstractAttribute::class)],
            ['deleted_attribute', false],
        ]);
        $collection = $this->createMock(Collection::class);
        $collection->method('getEntity')->willReturn($entity);
        $collection->method('getStoreId')->willReturn(1);
        $collection->expects(self::once())->method('addAttributeToSelect')->with(['color'], 'left');
        $labelProvider = $this->createMock(LabelProvider::class);
        $labelProvider->method('getConditionAttributeCodes')->with(1)->willReturn(['deleted_attribute', 'color']);

        (new AddConditionAttributesObserver($labelProvider))->execute($this->observer($collection));
    }

    public function testDoesNothingWhenNoConditionAttributeExists(): void
    {
        $entity = $this->createMock(AbstractEntity::class);
        $entity->method('getAttribute')->willReturn(false);
        $collection = $this->createMock(Collection::class);
        $collection->method('getEntity')->willReturn($entity);
        $collection->expects(self::never())->method('addAttributeToSelect');
        $labelProvider = $this->createMock(LabelProvider::class);
        $labelProvider->method('getConditionAttributeCodes')->willReturn(['deleted_attribute']);

        (new AddConditionAttributesObserver($labelProvider))->execute($this->observer($collection));
    }

    private function observer(Collection $collection): Observer
    {
        return new Observer(['event' => new Event(['collection' => $collection])]);
    }
}
