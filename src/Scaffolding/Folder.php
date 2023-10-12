<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class Folder implements GeneratorDirectiveInterface
{
    public function __construct(
        public string $name,
        public Tree $children = new Tree([]),
    ) {}
}
