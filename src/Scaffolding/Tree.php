<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class Tree implements GeneratorDirectiveInterface
{
    /**
     * @param array<\ErickJMenezes\Modularavel\Scaffolding\GeneratorDirectiveInterface> $structure
     */
    public function __construct(
        public array $structure,
    ) {}
}
