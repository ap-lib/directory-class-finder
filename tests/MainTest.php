<?php declare(strict_types=1);

namespace AP\DirectoryClassFinder\Tests;


use AP\DirectoryClassFinder\DirectoryClassFinderComposerPSR4;
use AP\DirectoryClassFinder\Tests\SearchDirectory\A;
use AP\DirectoryClassFinder\Tests\SearchDirectory\B;
use AP\DirectoryClassFinder\Tests\SearchDirectory\C;
use AP\DirectoryClassFinder\Tests\SearchDirectory\Sub\Sub\DoubleSubA;
use AP\DirectoryClassFinder\Tests\SearchDirectory\Sub\SubA;
use AP\DirectoryClassFinder\Tests\SearchDirectory\Sub\SubB;
use PHPUnit\Framework\TestCase;

final class MainTest extends TestCase
{
    public function testRecursive(): void
    {
        $classFinder = new DirectoryClassFinderComposerPSR4(
            include_vendor_classes: false,
            recheck_founded_by_psr4_name: true,
            recheck_founder_on_classmap: false
        );

        $classesGenerator = $classFinder->getClasses(
            directory: __DIR__ . "/SearchDirectory",
            recursive: true
        );

        $this->assertEqualsCanonicalizing(
            [
                A::class,
                B::class,
                C::class,
                SubA::class,
                SubB::class,
                DoubleSubA::class,
            ],
            iterator_to_array($classesGenerator)
        );
    }

    public function testNonRecursive(): void
    {
        $classFinder = new DirectoryClassFinderComposerPSR4(
            include_vendor_classes: false,
            recheck_founded_by_psr4_name: true,
            recheck_founder_on_classmap: false
        );

        $classesGenerator = $classFinder->getClasses(
            directory: __DIR__ . "/SearchDirectory",
            recursive: false
        );

        $this->assertEqualsCanonicalizing(
            [
                A::class,
                B::class,
                C::class,
            ],
            iterator_to_array($classesGenerator)
        );
    }

    public function testNonRecursive2(): void
    {
        $classFinder = new DirectoryClassFinderComposerPSR4(
            include_vendor_classes: false,
            recheck_founded_by_psr4_name: true,
            recheck_founder_on_classmap: false
        );

        $classesGenerator = $classFinder->getClasses(
            directory: __DIR__ . "/SearchDirectory/Sub",
            recursive: false
        );

        $this->assertEqualsCanonicalizing(
            [
                SubA::class,
                SubB::class,
            ],
            iterator_to_array($classesGenerator)
        );
    }
}
