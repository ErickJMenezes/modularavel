<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class File implements GeneratorDirectiveInterface
{
    public function __construct(
        public string $name,
        public string $contents = '',
    ) {}
}
