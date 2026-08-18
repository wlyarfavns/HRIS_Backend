<?php
$dirs = [
    'resources/views/admin',
    'resources/views/hr',
    'resources/views/finance',
    'resources/views/supervisor'
];

$filesToUpdate = [];

foreach ($dirs as $dir) {
    $dirIterator = new RecursiveDirectoryIterator($dir);
    $iterator = new RecursiveIteratorIterator($dirIterator);
    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            // Check if it's a dashboard or a list page
            $path = $file->getPathname();
            if (
                str_contains($path, 'dashboard.blade.php') ||
                str_contains($path, 'verifikasi') ||
                str_contains($path, 'persetujuan')
            ) {
                // Don't add if already added
                $content = file_get_contents($path);
                if (!str_contains($content, '<x-auto-refresh />')) {
                    // find last @endsection
                    $pos = strrpos($content, '@endsection');
                    if ($pos !== false) {
                        $newContent = substr_replace($content, "\n<x-auto-refresh />\n@endsection", $pos, strlen('@endsection'));
                        file_put_contents($path, $newContent);
                        echo "Updated: $path\n";
                    }
                }
            }
        }
    }
}
