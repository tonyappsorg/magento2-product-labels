<?php
declare(strict_types=1);

namespace Tonyapps\ProductLabels\Model;

class ColorValidator
{
    public function normalize(string $color): ?string
    {
        return preg_match('/^#[0-9A-Fa-f]{6}$/', $color) === 1 ? strtoupper($color) : null;
    }
}
