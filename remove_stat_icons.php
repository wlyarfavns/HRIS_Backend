<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $original = $content;
        
        // Remove circular icons that are clearly in stat cards (w-10, w-12, w-14)
        $content = preg_replace('/<div class="w-1[024] h-1[024] rounded-full[^"]*flex items-center justify-center[^"]*">\s*<span class="[^"]*material-symbols-outlined[^"]*">.*?<\/span>\s*<\/div>/is', '', $content);
        
        if ($content !== $original) {
            file_put_contents($path, $content);
            echo "Removed stat icon in: " . $path . "\n";
        }
    }
}
echo "Done.\n";
