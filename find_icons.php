<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Find icon wrappers in stat cards
        if (preg_match_all('/<div class="[^"]*rounded-full[^"]*flex items-center justify-center[^"]*">\s*<span class="[^"]*material-symbols-outlined[^"]*">.*?<\/span>\s*<\/div>/is', $content, $matches)) {
            echo "Found in: " . $path . "\n";
            foreach($matches[0] as $m) {
                // print "  " . trim($m) . "\n";
            }
        }
    }
}
