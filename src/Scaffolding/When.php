<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class When implements GeneratorDirectiveInterface
{
    public function __construct(
        public bool $condition,
        public GeneratorDirectiveInterface $directive,
    )
    {}
}
