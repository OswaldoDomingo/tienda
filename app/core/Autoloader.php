<?php
spl_autoload_register(function ($className) {
    $paths = [
        '../app/controllers/', // Relative from public/index.php
        '../app/models/',    // Relative from public/index.php
        '../app/core/'       // Relative from public/index.php
    ];
    // Check if className already includes a namespace
    $className = str_replace('\\', '/', $className); // Normalize namespace separator

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    // Fallback for classes that might be in the root of app/ (if any)
    $rootAppFile = '../app/' . $className . '.php';
    if (file_exists($rootAppFile)) {
        require_once $rootAppFile;
        return;
    }
});
?>