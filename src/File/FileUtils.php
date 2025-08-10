<?php

declare(strict_types=1);

namespace Entropy\Utils\File;

use CallbackFilterIterator;
use Composer\InstalledVersions;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class FileUtils
{
    /**
     * Get all files of the given extension in the given directory.
     *
     * @param string $path if not exists, return an empty array
     * @param string $ext
     * @param string|null $exclude
     * @return array
     */
    public static function getFiles(string $path, string $ext = 'php', ?string $exclude = null): array
    {
        if (!is_dir($path)) {
            return [];
        }

        // from https://stackoverflow.com/a/41636321
        return iterator_to_array(
            new CallbackFilterIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(
                        $path,
                        FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::SKIP_DOTS
                    ),
                    RecursiveIteratorIterator::CHILD_FIRST
                ),
                fn(SplFileInfo $file) => $file->isFile() &&
                    str_ends_with($file->getFilename(), '.' . $ext) &&
                    !str_starts_with($file->getBasename(), '.') &&
                    (null === $exclude || false === stripos($file->getBasename(), $exclude))
            )
        );
    }

    /**
     * Gets the application root dir (path of the project composer file).
     *
     * https://github.com/symfony/symfony/blob/6.0/src/Symfony/Component/HttpKernel/Kernel.php#method_getProjectDir
     */
    public static function getProjectDir(string $startDir = null): string
    {
        $dir = $startDir ?? dirname(__DIR__);

        while (!is_file($dir . '/composer.json')) {
            if ($dir === dirname($dir)) {
                return $dir;
            }
            $dir = dirname($dir);
        }

        return $dir;
    }

    /**
     * Retrieves the root directory path of the installed root package.
     *
     * @return string The absolute path to the root directory.
     */
    public static function getRootPath(): string
    {
        return realpath(InstalledVersions::getRootPackage()['install_path']);
    }
}
