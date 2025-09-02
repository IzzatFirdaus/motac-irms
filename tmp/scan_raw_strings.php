<?php

// Scan resources/views for __('...') or @lang('...') usages that are not dot-keys (no dot) to find raw phrases.
// Usage: php tmp/scan_raw_strings.php

$root = realpath(__DIR__.'/../resources/views');
$rii  = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

$raw = [];
foreach ($rii as $file) {
    if ($file->isDir()) {
        continue;
    }
    $path = $file->getPathname();
    if (! preg_match('/\.blade\.php$/', $path)) {
        continue;
    }
    $content = file_get_contents($path);
    // Match __('...') and @lang('...')
    if (preg_match_all("/(__\(|@lang\()\s*'([^']+)'\s*\)/u", $content, $m)) {
        foreach ($m[2] as $s) {
            // Heuristic: treat as raw if no dot separator and contains space or non-ascii letters
            if (strpos($s, '.') === false && (preg_match('/\s/u', $s) || preg_match('/[\x{00C0}-\x{FFFF}]/u', $s))) {
                $raw[$s] = true;
            }
        }
    }
}

$out = __DIR__.'/raw_phrases.txt';
ksort($raw);
file_put_contents($out, implode(PHP_EOL, array_keys($raw)));
echo "Wrote raw phrases to $out (".count($raw).")\n";
