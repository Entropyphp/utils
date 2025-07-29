<?php

declare(strict_types=1);

namespace Entropy\Tests\Utils\Attribute;

// Fonction de test avec attribut
#[TestAttribute('test value')]
function testFunctionWithAttribute(): void
{
}

// Fonction de test sans attribut
function testFunctionWithoutAttribute(): void
{
}
