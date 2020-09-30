<?php

declare(strict_types=1);

namespace Community\Forum\Lib;

interface UniqueIdentifierGeneratorInterface
{
    public function generate(): string;
}
