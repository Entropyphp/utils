# Utils

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg)](https://php.net)
[![Coverage Status](https://coveralls.io/repos/github/Entropyphp/utils/badge.svg?branch=main)](https://coveralls.io/github/Entropyphp/utils?branch=main)
[![Continuous Integration](https://github.com/Entropyphp/utils/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/Entropyphp/utils/actions/workflows/ci.yml)

Utility classes for PHP. A collection of PHP utility classes for file operations, attribute handling, and PHP code parsing.

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

Install via Composer:

```bash
composer require entropyphp/pg-utils
```

## Components

### AttributeLoader

A utility class for working with PHP 8 attributes (annotations). It provides methods to retrieve attributes from classes and methods.

#### Usage

```php
use Koriym\Attributes\AttributeReader;
use Pg\Utils\Attribute\AttributeLoader;
use ReflectionClass;
use ReflectionMethod;

// Initialize the AttributeLoader
$attributeLoader = new AttributeLoader(new AttributeReader());

// Get a single attribute from a method
$reflectionMethod = new ReflectionMethod(MyClass::class, 'myMethod');
$attribute = $attributeLoader->getMethodAttribute($reflectionMethod, MyAttribute::class);

// Get multiple attributes from a method
$attributes = $attributeLoader->getMethodAttributes($reflectionMethod, MyAttribute::class);
foreach ($attributes as $attribute) {
    // Process each attribute
}

// Get a single attribute from a class
$reflectionClass = new ReflectionClass(MyClass::class);
$attribute = $attributeLoader->getClassAttribute($reflectionClass, MyAttribute::class);

// Get multiple attributes from a class
$attributes = $attributeLoader->getClassAttributes($reflectionClass, MyAttribute::class);
foreach ($attributes as $attribute) {
    // Process each attribute
}

// Access the underlying AttributeReader
$reader = $attributeLoader->getReader();
```

### FileUtils

A utility class for file system operations.

#### Usage

```php
use Pg\Utils\File\FileUtils;

// Get all PHP files in a directory (recursively)
$files = FileUtils::getFiles('/path/to/directory', 'php');
foreach ($files as $file) {
    echo $file->getPathname() . PHP_EOL;
}

// Get all PHP files excluding those containing 'test' in the filename
$files = FileUtils::getFiles('/path/to/directory', 'php', 'test');

// Find the project root directory (where composer.json is located)
$projectDir = FileUtils::getProjectDir(__DIR__);

// Get the root path of the installed package
$rootPath = FileUtils::getRootPath();
```

### PhpTokenParser

A utility class for parsing PHP code to extract class information.

#### Usage

```php
use Pg\Utils\Parser\PhpTokenParser;

// Get the fully qualified class name from PHP code
$phpCode = file_get_contents('/path/to/file.php');
$className = PhpTokenParser::findClass($phpCode);

if ($className) {
    echo "Found class: " . $className . PHP_EOL;
} else {
    echo "No class found in the file." . PHP_EOL;
}
```

## Testing

Run the test suite:

```bash
composer run tests
```

Generate code coverage report:

```bash
composer run coverage
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.