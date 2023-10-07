<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

readonly class Command
{
    /**
     * @param array<string> $commands
     */
    public function __construct(
        public array $commands = [],
    ) {}
}
