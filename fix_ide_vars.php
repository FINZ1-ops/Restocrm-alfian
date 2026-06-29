<?php
$dir = __DIR__ . '/app/Views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Find all variables
        preg_match_all('/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', $content, $matches);
        
        if (!empty($matches[1])) {
            $vars = array_unique($matches[1]);
            
            // Filter out system variables and ones assigned in the file
            $exclude = ['this', '_SERVER', '_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_REQUEST', '_ENV'];
            $undefinedVars = [];
            
            foreach ($vars as $var) {
                if (in_array($var, $exclude)) continue;
                
                // Check if the variable is defined in the file (e.g., $var = ... or foreach ($something as $var))
                // This is a naive regex, but covers most common cases
                $isAssigned = preg_match('/\$' . $var . '\s*=>?/', $content) || preg_match('/as\s+\$' . $var . '/', $content) || preg_match('/\$' . $var . '\s*=/', $content) || preg_match('/list\s*\([^)]*\$' . $var . '[^)]*\)\s*=/', $content) || preg_match('/catch\s*\([^)]+\$' . $var . '\s*\)/', $content);
                
                if (!$isAssigned) {
                    $undefinedVars[] = $var;
                }
            }
            
            if (!empty($undefinedVars)) {
                // Generate docblock
                $docblock = "/**\n";
                foreach ($undefinedVars as $var) {
                    $docblock .= " * @var mixed \$$var\n";
                }
                $docblock .= " */\n";
                
                // Prepend docblock after <?php if it exists at the top
                if (strpos($content, "<?php\n/** @var") === false && strpos($content, "<?php\r\n/** @var") === false) {
                    $newContent = preg_replace('/<\?php\s+/', "<?php\n" . $docblock, $content, 1);
                    if ($newContent !== null && $newContent !== $content) {
                        file_put_contents($file->getPathname(), $newContent);
                        echo "Fixed: " . $file->getFilename() . "\n";
                    }
                }
            }
        }
    }
}
echo "Done!\n";
