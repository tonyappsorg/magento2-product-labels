<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use PHPUnit\Framework\TestCase;
use Tonyapps\ProductLabels\Model\LabelProvider;
use Tonyapps\ProductLabels\Model\ResourceModel\Rule\Collection;
use Tonyapps\ProductLabels\Model\ResourceModel\Rule\CollectionFactory;
use Tonyapps\ProductLabels\Model\Rule;
use Tonyapps\ProductLabels\Model\RuleMatcher;

class LabelProviderTest extends TestCase
{
    public function testLoadsActiveRulesOncePerRequest(): void
    {
        $rule = $this->createMock(Rule::class);
        $rule->method('getData')->willReturnMap([
            ['label', null, 'New'],
            ['background_color', null, '#000'],
            ['text_color', null, '#fff'],
        ]);
        $collection = $this->createMock(Collection::class);
        $collection->method('addActiveFilter')->willReturnSelf();
        $collection->method('getItems')->willReturn([$rule]);
        $factory = $this->createMock(CollectionFactory::class);
        $factory->expects(self::once())->method('create')->willReturn($collection);
        $matcher = $this->createMock(RuleMatcher::class);
        $matcher->method('matches')->willReturn(true);
        $provider = new LabelProvider($factory, $matcher);

        $provider->getLabels($this->createMock(Product::class));
        $labels = $provider->getLabels($this->createMock(Product::class));

        self::assertSame([['label' => 'New', 'background_color' => '#000', 'text_color' => '#fff']], $labels);
    }
}
