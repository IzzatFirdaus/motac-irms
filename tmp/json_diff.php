<?php

// Compare keys between resources/lang/en.json and resources/lang/ms.json
// Usage: php tmp/json_diff.php

function read_json($path)
{
    if (! file_exists($path)) {
        return [];
    }
    $json = file_get_contents($path);
    $data = json_decode($json, true);
    if (! is_array($data)) {
        return [];
    }

    return $data;
}

$base = __DIR__.'/../resources/lang/';
$en   = read_json($base.'en.json');
$ms   = read_json($base.'ms.json');

$enKeys = array_keys($en);
$msKeys = array_keys($ms);

$missingInEn = array_values(array_diff($msKeys, $enKeys));
$missingInMs = array_values(array_diff($enKeys, $msKeys));

echo sprintf("en.json keys: %d\n", count($enKeys));
echo sprintf("ms.json keys: %d\n", count($msKeys));
echo sprintf("Missing in en.json: %d\n", count($missingInEn));
echo sprintf("Missing in ms.json: %d\n", count($missingInMs));

if (! is_dir(__DIR__)) {
    mkdir(__DIR__, 0777, true);
}
file_put_contents(__DIR__.'/missing_json_in_en.txt', implode(PHP_EOL, $missingInEn));
file_put_contents(__DIR__.'/missing_json_in_ms.txt', implode(PHP_EOL, $missingInMs));

echo "Wrote: tmp/missing_json_in_en.txt and tmp/missing_json_in_ms.txt\n";
