<?php declare(strict_types=1);

namespace AP\DirectoryClassFinder;


use Composer\Autoload\ClassLoader;
use DirectoryIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RegexIterator;

readonly class DirectoryClassFinderComposerPSR4 implements DirectoryClassFinderInterface
{
    /**
     * @param bool $include_vendor_classes
     * @param bool $recheck_founded_by_psr4_name Recommended to use true, because it can be files with no classes
     * @param bool $recheck_founder_on_classmap By performance reason can be false, on this situation dumped
     *                                          composer's auto-load can speed up the performance a lot
     */
    public function __construct(
        public bool $include_vendor_classes = false,
        public bool $recheck_founded_by_psr4_name = true,
        public bool $recheck_founder_on_classmap = false,
    )
    {
    }

    public static function getVendorDirectory(): string
    {
        return dirname((new ReflectionClass(ClassLoader::class))->getFileName(), 2);
    }

    /**
     * Relation between saved on /composer/autoload_classmap.php optimized autoload cache filenames and classes
     *
     * @return array<string,class-string> key - filename, value - class
     */
    public static function getComposerClassmap(): array
    {
        $classmap_file = self::getVendorDirectory() . "/composer/autoload_classmap.php";
        if (file_exists($classmap_file)) {
            $classmap = include($classmap_file);
            return is_array($classmap) ? array_flip($classmap) : [];
        }
        return [];
    }

    /**
     * Directories - namespaces relation
     *
     * @return array<string,class-string> key - filename, value - class
     */
    public static function getComposerPSR4Relations(bool $include_vendor_classes = false): array
    {
        $vendor_dir = self::getVendorDirectory();
        $psr4_file  = "$vendor_dir/composer/autoload_psr4.php";
        if (file_exists($psr4_file)) {
            $psr4_all = include($psr4_file);
            if (is_array($psr4_all)) {
                $psr4 = [];
                foreach ($psr4_all as $namespace => $folders) {
                    foreach ($folders as $folder) {
                        if (!$include_vendor_classes && !str_starts_with($folder, $vendor_dir)) {
                            $psr4[$folder] = $namespace;
                        }
                    }
                }
                return $psr4;
            }
        }
        return [];
    }

    /**
     * @param string $directory
     * @return Generator<string>
     */
    public static function getPhpFiles(string $directory, bool $recursive = true): Generator
    {
        $regexIterator = new RegexIterator(
            $recursive
                ? new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory))
                : new DirectoryIterator($directory),
            '/\.php$/'
        );
        foreach ($regexIterator as $file) {
            yield $file->getPathname();
        }
    }

    public function getClasses(string $directory, bool $recursive = true): Generator
    {
        $classmap = self::getComposerClassmap();
        $psr4     = null; // lazy load

        foreach (self::getPhpFiles($directory, $recursive) as $file) {
            if (isset($classmap[$file])) {
                if (!$this->recheck_founder_on_classmap || class_exists($classmap[$file])) {
                    yield $classmap[$file];
                }
            } else {
                if (is_null($psr4)) {
                    $psr4 = self::getComposerPSR4Relations($this->include_vendor_classes);
                }
                foreach ($psr4 as $path => $namespace) {
                    if (str_starts_with($file, "$path/")) {
                        $relativePath = substr($file, strlen($path) + 1);
                        $class        = $namespace . str_replace(['/', '.php'], ['\\', ''], $relativePath);
                        if (!$this->recheck_founded_by_psr4_name || class_exists($class)) {
                            yield $class;
                        }
                    }
                }
            }
        }
    }
}