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
                $console = new Process($entity->commands, $baseDirectory);
                $console->run();
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
