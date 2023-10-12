<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class Command implements GeneratorDirectiveInterface
{
    /**
     * @param array<string> $commands
     */
    public function __construct(
        public array $commands = [],
    ) {}
}
