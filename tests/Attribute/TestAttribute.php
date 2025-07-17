<?php

declare(strict_types=1);

namespace PgTests\Utils\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
class TestAttribute
{
    public function __construct()
    {
    }
}
