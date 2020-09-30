<?php

declare(strict_types=1);

namespace Community\Forum\Lib;

use PHPUnit\Framework\TestCase;

final class UniqueIdentifierGeneratorTest extends TestCase
{

    public function testGenerateUniqueIdentifier()
    {
        $generator = new UniqueIdentifierGenerator();

        $this->assertTrue(is_string($generator->generate()));
        $this->assertNotEquals($generator->generate(), $generator->generate());
    }
}
