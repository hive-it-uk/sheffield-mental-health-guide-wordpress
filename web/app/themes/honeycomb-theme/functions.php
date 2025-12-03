<?php

declare(strict_types=1);

(static function (): void {
    $configFolders = [
        'classes',
        'utils',
        'post-types',
        'taxonomies',
        'endpoints',
        'config',
    ];

    foreach ($configFolders as $folder) {
        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(get_stylesheet_directory() . '/' . $folder)
        );

        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (substr($file->getPathname(), -4) !== '.php') {
                continue;
            }

            (static function () use ($file): void {
                require_once $file;
            })();
        }
    }
})();
