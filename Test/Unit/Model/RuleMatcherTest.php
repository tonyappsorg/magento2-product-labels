<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\Rule\Condition\Combine;
use PHPUnit\Framework\TestCase;
use Tonyapps\ProductLabels\Model\Rule;
use Tonyapps\ProductLabels\Model\RuleMatcher;

class RuleMatcherTest extends TestCase
{
    public function testMatchesProductWhenRuleConditionsPass(): void
    {
        $conditions = $this->createMock(Combine::class);
        $conditions->method('getConditions')->willReturn([new \stdClass()]);
        $conditions->expects(self::once())->method('validate')->with(self::isInstanceOf(Product::class))->willReturn(true);
        $rule = $this->createMock(Rule::class);
        $rule->method('getStoreIds')->willReturn([]);
        $rule->method('getConditions')->willReturn($conditions);
        $product = $this->createMock(Product::class);

        self::assertTrue((new RuleMatcher())->matches($rule, $product));
    }

    public function testRejectsRuleScopedToAnotherStore(): void
    {
        $rule = $this->createMock(Rule::class);
        $rule->method('getStoreIds')->willReturn([3]);
        $rule->expects(self::never())->method('getConditions');
        $product = $this->createMock(Product::class);
        $product->method('getData')->with('store_id')->willReturn(2);

        self::assertFalse((new RuleMatcher())->matches($rule, $product));
    }

    public function testRejectsRuleWithoutConditions(): void
    {
        $conditions = $this->createMock(Combine::class);
        $conditions->method('getConditions')->willReturn([]);
        $conditions->expects(self::never())->method('validate');
        $rule = $this->createMock(Rule::class);
        $rule->method('getStoreIds')->willReturn([]);
        $rule->method('getConditions')->willReturn($conditions);

        self::assertFalse((new RuleMatcher())->matches($rule, $this->createMock(Product::class)));
    }
}
