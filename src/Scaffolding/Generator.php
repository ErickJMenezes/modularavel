<?php

namespace ErickJMenezes\Modularavel\Scaffolding;

use Symfony\Component\Process\Process;

class Generator
{
    public function generate(Tree $tree, string $baseDirectory = '.'): void
    {
        foreach ($tree->structure as $entity) {
            if ($entity instanceof File) {
                $filename = implode(DIRECTORY_SEPARATOR, [$baseDirectory, $entity->name]);
                if (!file_exists($filename)) {
                    touch($filename);
                    file_put_contents($filename, $entity->contents);
                }
            } elseif ($entity instanceof Command) {
                $process = new Process($entity->commands, $baseDirectory);
                $process->enableOutput();
                $process->setPty(true);
                $process->setTty(true);
                $process->start();
                while ($process->isRunning()) {
                    echo $process->getIncrementalOutput();
                }
            } elseif ($entity instanceof Folder) {
                $folder = implode(DIRECTORY_SEPARATOR, [$baseDirectory, $entity->name]);
                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                $this->generate($entity->children, $folder);
            }
        }
    }
}
