<?php

declare(strict_types=1);

// Translation keys audit script for en/ms locales
// - Merges suffixed files (e.g., messages_en.php) and unsuffixed (messages.php)
// - Compares groups and keys between locales
// - Scans codebase for used translation keys and reports missing definitions

const EN_DIR = __DIR__.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'en';
const MS_DIR = __DIR__.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'ms';

function group_from_filename(string $filename): string
{
    $name = preg_replace('/\.php$/', '', $filename);
    // strip locale suffix if present
    $name = preg_replace('/_(en|ms)$/', '', $name);

    return (string) $name;
}

function include_lang_file(string $path): array
{
    try {
        $data = include $path;
        if (is_array($data)) {
            return $data;
        }
    } catch (Throwable $e) {
        fwrite(STDERR, "Error including $path: {$e->getMessage()}\n");
    }

    return [];
}

function load_locale_groups(string $dir): array
{
    if (! is_dir($dir)) {
        return [];
    }
    $groups = [];
    $rii    = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($rii as $file) {
        if ($file->isDir()) {
            continue;
        }
        if (strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        $group = group_from_filename($file->getFilename());
        $arr   = include_lang_file($file->getPathname());
        if (! isset($groups[$group])) {
            $groups[$group] = [];
        }
        // merge recursively but prefer existing keys to avoid overriding
        $groups[$group] = array_replace_recursive($groups[$group], $arr);
    }

    return $groups;
}

function load_locale_json(string $localeDir): array
{
    $jsonPath = dirname($localeDir).DIRECTORY_SEPARATOR.basename($localeDir).'.json';
    // localeDir points to .../lang/en, json should be .../lang/en.json
    $langDir  = dirname($localeDir);
    $locale   = basename($localeDir);
    $jsonPath = $langDir.DIRECTORY_SEPARATOR.$locale.'.json';
    if (is_file($jsonPath)) {
        $json = @file_get_contents($jsonPath);
        if ($json !== false) {
            $data = json_decode($json, true);
            if (is_array($data)) {
                return $data;
            }
        }
    }

    return [];
}

function array_dot_keys(array $arr, string $prefix = ''): array
{
    $keys = [];
    foreach ($arr as $k => $v) {
        $key = $prefix === '' ? (string) $k : $prefix.'.'.$k;
        if (is_array($v)) {
            $keys = array_merge($keys, array_dot_keys($v, $key));
        } else {
            $keys[] = $key;
        }
    }

    return $keys;
}

function scan_code_for_keys(array $paths): array
{
    $used    = [];
    $pattern = '/(?:(?:__|@lang|trans|trans_choice|Lang::get)\(\s*[\'\"])([a-zA-Z0-9_.-]+)(?=[\'\"])/';
    foreach ($paths as $path) {
        if (! is_dir($path)) {
            continue;
        }
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (! in_array($ext, ['php', 'blade.php'])) {
                continue;
            }
            $contents = @file_get_contents($file->getPathname());
            if ($contents === false) {
                continue;
            }
            if (preg_match_all($pattern, $contents, $matches)) {
                foreach ($matches[1] as $match) {
                    $used[$match] = true;
                }
            }
        }
    }
    ksort($used);

    return array_keys($used);
}

function group_key(string $dotKey): array
{
    $parts = explode('.', $dotKey, 2);

    return [$parts[0], $parts[1] ?? ''];
}

function build_defined_keys_by_group(array $groups): array
{
    $defined = [];
    foreach ($groups as $group => $data) {
        $defined[$group] = array_dot_keys($data);
    }

    return $defined;
}

function main(): void
{
    $enGroups = load_locale_groups(EN_DIR);
    $msGroups = load_locale_groups(MS_DIR);
    $enJson   = load_locale_json(EN_DIR);
    $msJson   = load_locale_json(MS_DIR);

    $enDefined = build_defined_keys_by_group($enGroups);
    $msDefined = build_defined_keys_by_group($msGroups);

    $allGroups = array_unique(array_merge(array_keys($enGroups), array_keys($msGroups)));
    sort($allGroups);

    echo "== Groups present by locale ==\n";
    echo 'EN ('.count($enGroups).'): '.implode(', ', array_keys($enGroups))."\n";
    echo 'MS ('.count($msGroups).'): '.implode(', ', array_keys($msGroups))."\n\n";

    $missingGroupsInMS = array_values(array_diff(array_keys($enGroups), array_keys($msGroups)));
    $missingGroupsInEN = array_values(array_diff(array_keys($msGroups), array_keys($enGroups)));

    if ($missingGroupsInMS) {
        echo 'Groups missing in MS: '.implode(', ', $missingGroupsInMS)."\n";
    }
    if ($missingGroupsInEN) {
        echo 'Groups missing in EN: '.implode(', ', $missingGroupsInEN)."\n";
    }

    echo "\n== Key diffs by group ==\n";
    foreach ($allGroups as $group) {
        $enKeys = $enDefined[$group] ?? [];
        $msKeys = $msDefined[$group] ?? [];
        sort($enKeys);
        sort($msKeys);
        $missingInMS = array_values(array_diff($enKeys, $msKeys));
        $missingInEN = array_values(array_diff($msKeys, $enKeys));
        if (! $missingInMS && ! $missingInEN) {
            continue;
        }
        echo "[Group: $group]\n";
        if ($missingInMS) {
            echo ' - Missing in MS ('.count($missingInMS)."):\n";
            foreach (array_slice($missingInMS, 0, 50) as $k) {
                echo "    - $group.$k\n";
            }
            if (count($missingInMS) > 50) {
                echo "    ... and more\n";
            }
        }
        if ($missingInEN) {
            echo ' - Missing in EN ('.count($missingInEN)."):\n";
            foreach (array_slice($missingInEN, 0, 50) as $k) {
                echo "    - $group.$k\n";
            }
            if (count($missingInEN) > 50) {
                echo "    ... and more\n";
            }
        }
    }

    echo "\n== Scan code for used keys ==\n";
    $usedKeys = scan_code_for_keys([
        __DIR__.DIRECTORY_SEPARATOR.'app',
        __DIR__.DIRECTORY_SEPARATOR.'resources',
        __DIR__.DIRECTORY_SEPARATOR.'routes',
        __DIR__.DIRECTORY_SEPARATOR.'config',
        __DIR__.DIRECTORY_SEPARATOR.'database',
    ]);
    echo 'Found used keys: '.count($usedKeys)."\n";

    $missingEn     = [];
    $missingMs     = [];
    $missingJsonEn = [];
    $missingJsonMs = [];

    foreach ($usedKeys as $fullKey) {
        if (strpos($fullKey, '.') === false) {
            // JSON translation string
            if (! array_key_exists($fullKey, $enJson)) {
                $missingJsonEn[] = $fullKey;
            }
            if (! array_key_exists($fullKey, $msJson)) {
                $missingJsonMs[] = $fullKey;
            }

            continue;
        }
        [$group, $key] = group_key($fullKey);
        if ($group === '' || $key === '') {
            continue;
        }
        // Using dot-key path checking for nested arrays
        $enHas = in_array($key, $enDefined[$group] ?? [], true);
        $msHas = in_array($key, $msDefined[$group] ?? [], true);
        if (! $enHas) {
            $missingEn[] = $fullKey;
        }
        if (! $msHas) {
            $missingMs[] = $fullKey;
        }
    }
    $missingEn     = array_values(array_unique($missingEn));
    $missingMs     = array_values(array_unique($missingMs));
    $missingJsonEn = array_values(array_unique($missingJsonEn));
    $missingJsonMs = array_values(array_unique($missingJsonMs));

    echo 'Missing definitions in EN: '.count($missingEn)."\n";
    foreach (array_slice($missingEn, 0, 100) as $k) {
        echo " - $k\n";
    }
    if (count($missingEn) > 100) {
        echo " ... and more\n";
    }

    echo 'Missing definitions in MS: '.count($missingMs)."\n";
    foreach (array_slice($missingMs, 0, 100) as $k) {
        echo " - $k\n";
    }
    if (count($missingMs) > 100) {
        echo " ... and more\n";
    }

    echo "\nMissing JSON entries in EN.json: ".count($missingJsonEn)."\n";
    foreach (array_slice($missingJsonEn, 0, 100) as $k) {
        echo " - $k\n";
    }
    if (count($missingJsonEn) > 100) {
        echo " ... and more\n";
    }

    echo 'Missing JSON entries in ms.json: '.count($missingJsonMs)."\n";
    foreach (array_slice($missingJsonMs, 0, 100) as $k) {
        echo " - $k\n";
    }
    if (count($missingJsonMs) > 100) {
        echo " ... and more\n";
    }
}

main();
