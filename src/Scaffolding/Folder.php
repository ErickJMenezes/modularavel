<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class Folder
{
    public function __construct(
        public string $name,
        public Tree $children = new Tree([]),
    ) {}
}
