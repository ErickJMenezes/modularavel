<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class File
{
    public function __construct(
        public string $name,
        public string $contents = '',
    ) {}
}
