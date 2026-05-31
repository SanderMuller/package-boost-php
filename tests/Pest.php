<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

pest()->extend(TestCase::class)->in('Unit');

/**
 * Run $fn against a fresh, uniquely-named temp directory, removing it
 * (and its contents) afterwards even if the test fails. The callback
 * receives the directory path as a typed string.
 *
 * @param  callable(string): void  $fn
 */
function withTempDir(string $prefix, callable $fn): void
{
    $dir = makeTempDir($prefix);
    try {
        $fn($dir);
    } finally {
        removeDir($dir);
    }
}

/**
 * Create a fresh, uniquely-named temp directory.
 */
function makeTempDir(string $prefix): string
{
    $dir = sys_get_temp_dir() . '/' . $prefix . '-' . bin2hex(random_bytes(6));
    if (! mkdir($dir, 0o777, true) && ! is_dir($dir)) {
        throw new RuntimeException("Failed to create temp dir: {$dir}");
    }

    return $dir;
}

/**
 * Recursively remove a directory and its contents (including dotfiles).
 */
function removeDir(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }

    $entries = scandir($dir);
    foreach ($entries === false ? [] : $entries as $entry) {
        if ($entry === '.') {
            continue;
        }

        if ($entry === '..') {
            continue;
        }

        $path = $dir . '/' . $entry;
        if (is_dir($path)) {
            removeDir($path);
        } else {
            unlink($path);
        }
    }

    rmdir($dir);
}
