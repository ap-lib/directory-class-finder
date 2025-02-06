<?php declare(strict_types=1);

namespace AP\DirectoryClassFinder;

use Generator;

interface DirectoryClassFinderInterface
{
    /**
     * @param string $directory
     * @param bool $recursive
     * @return Generator<class-string>
     */
    public function getClasses(string $directory, bool $recursive = true): Generator;
}