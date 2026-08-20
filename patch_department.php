<?php
$directory = new RecursiveDirectoryIterator('d:\WILLY ARIF AVINES\MAGANG\revisi\RevisiBackend\hris_system\resources\views');
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($regex as $file) {
    $filePath = $file[0];
    $content = file_get_contents($filePath);
    if (strpos($content, '->department->name') !== false) {
        $content = str_replace('->department->name', '->department?->name', $content);
        file_put_contents($filePath, $content);
        echo "Updated: $filePath\n";
    }
}
echo "Done replacing department?->name\n";
