<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use Tonyapps\ProductLabels\Model\ColorValidator;

class ColorValidatorTest extends TestCase
{
    public function testNormalizesSixDigitHexColor(): void
    {
        $this->assertSame('#A1B2C3', (new ColorValidator())->normalize('#a1B2c3'));
    }

    public function testRejectsCssInjectionAndShortColors(): void
    {
        $validator = new ColorValidator();
        $this->assertNull($validator->normalize('red'));
        $this->assertNull($validator->normalize('#fff'));
        $this->assertNull($validator->normalize('#ffffff;background:url(x)'));
    }
}
