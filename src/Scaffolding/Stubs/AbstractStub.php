<?php

namespace ErickJMenezes\Modularavel\Scaffolding\Stubs;

use ErickJMenezes\Modularavel\Scaffolding\File;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

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
        $phpFinder = new PhpExecutableFinder();
        $phpProcess = new Process(
            [$phpFinder->find(), ...$phpFinder->findArguments(), $stubFileName],
            implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'stubs']),
            $replacements,
        );
        $phpProcess->run();
        return $phpProcess->getOutput();
    }
}
