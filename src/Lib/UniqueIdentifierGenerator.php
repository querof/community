<?php

declare(strict_types=1);

namespace Community\Forum\Lib;

class UniqueIdentifierGenerator implements UniqueIdentifierGeneratorInterface
{
    public function generate(): string
    {
        return uniqid("", true);
    }
}
