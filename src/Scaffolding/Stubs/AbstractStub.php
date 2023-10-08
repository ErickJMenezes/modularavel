<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

use ErickJMenezes\Modularavel\Scaffolding\File;

abstract readonly class AbstractStub extends File
{
    public function __construct(string $name, string $stubName, array $replacements)
    {
        parent::__construct($name, $this->buildStub($replacements, $stubName));
    }

    /**
     * @param array<string,string> $replacements
     * @param string               $stubFileName
     *
     * @return string
     * @author ErickJMenezes <erickmenezes.dev@gmail.com>
     */
    private function buildStub(array $replacements, string $stubFileName): string
    {
        return str_replace(
            array_map(fn (string $value) => '{{'.$value.'}}', array_keys($replacements)),
            array_map(fn (string|\Stringable $value) => (string) $value, array_values($replacements)),
            file_get_contents(implode(DIRECTORY_SEPARATOR, [
                __DIR__,
                '..',
                '..',
                '..',
                'stubs',
                $stubFileName
            ])),
        );
    }
}
