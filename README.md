# AP\DirectoryClassFinder

[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

DirectoryClassFinder is a lightweight PHP library for scanning directories and retrieving class names. It supports both **recursive** and **non-recursive** searches and works with Composer's autoloading mechanisms.

## Installation

```bash
composer require ap-lib/directory-class-finder
```

## Features

- Supports both recursive and non-recursive directory scanning.
- Works with Composer's PSR-4 autoloading and classmap caching for efficiency.
- Allows excluding vendor classes for better performance.
- Provides options for verifying class existence before yielding.

## Requirements

- PHP 8.3 or higher
- Composer for autoloading

## Getting started

### Basic Usage

```php
use AP\DirectoryClassFinder\DirectoryClassFinderComposerPSR4;

$classFinder = new DirectoryClassFinderComposerPSR4(
    include_vendor_classes: false,
    recheck_founded_by_psr4_name: true,
    recheck_founder_on_classmap: false
);

$classes = $classFinder->getClasses('/path/to/directory');

foreach ($classes as $class) {
    echo $class . PHP_EOL;
}
```

### Recursive vs Non-Recursive Search

```php
$recursiveClasses = $classFinder->getClasses('/path/to/directory', recursive: true); // Includes subdirectories
$nonRecursiveClasses = $classFinder->getClasses('/path/to/directory', recursive: false); // Only top-level
```

## Advanced Usage

### Using Composer's Autoload Optimization

If you've run:
```bash
composer dump-autoload --optimize
```
You can leverage Composer's classmap caching to improve performance: