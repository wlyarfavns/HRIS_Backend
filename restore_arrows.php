<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $originalContent = $content;
        
        // Find all <select ... appearance-none ...> ... </select> blocks
        // and check if they are missing an icon before </div>
        
        $content = preg_replace_callback('/(<select[^>]*appearance-none[^>]*>.*?<\/select>\s*)(<\/div>)/is', function($matches) {
            // Check if there is already an svg or expand_more inside the match? No, they would be after </select>
            // Wait, the regex captures exactly </select>\s*</div>.
            // If the SVG or expand_more was there, it would be between </select> and </div>.
            // So if it matches `</select>\s*</div>` EXACTLY, it means there is NO icon!
            
            $svg = '<svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>';
            return $matches[1] . $svg . "\n" . $matches[2];
        }, $content);
        
        if ($content !== $originalContent) {
            file_put_contents($path, $content);
            echo "Fixed missing arrow in: " . $path . "\n";
        }
    }
}
echo "Done.\n";
