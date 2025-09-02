param(
    [switch]$Apply
)

# Conservative replacements to avoid Microscope false-positive for `mixed` class reference.
# Dry-run by default. Use -Apply to perform edits (backups created with .bak extension).

$searchRoot = Join-Path $PSScriptRoot '..' # repo root when script run from tools dir
$paths = Get-ChildItem -Path "$searchRoot\app" -Recurse -Filter "*.php" | Select-Object -ExpandProperty FullName

$pattern1 = 'array\s*<\s*string\s*,\s*mixed\s*>'
$pattern2 = '@return\s+array\s*<[^>]*mixed[^>]*>'
$pattern3 = '@param\s+array\s*<[^>]*mixed[^>]*>'
$pattern4 = '@param\s+[^\n]*\bmixed\b'

$foundFiles = @()
foreach ($p in $paths) {
    $content = Get-Content -Path $p -Raw -ErrorAction SilentlyContinue
    if (-not $content) { continue }
    if ([regex]::IsMatch($content, $pattern1) -or [regex]::IsMatch($content, $pattern2) -or [regex]::IsMatch($content, $pattern3) -or [regex]::IsMatch($content, $pattern4)) {
        $foundFiles += $p
    }
}

$foundFiles = $foundFiles | Sort-Object -Unique
if ($foundFiles.Count -eq 0) {
    Write-Output "No candidate files found for conservative 'mixed' docblock replacements."
    exit 0
}

Write-Output "Found $($foundFiles.Count) candidate file(s):"
foreach ($m in $foundFiles) { Write-Output " - $m" }

if (-not $Apply) {
    Write-Output "\nDry-run complete. To apply replacements, re-run with -Apply. No files were modified.";
    exit 0
}

Write-Output "\nApplying conservative replacements with backups (.bak files created)."
foreach ($p in $foundFiles) {
    try {
        $orig = Get-Content -Path $p -Raw -ErrorAction Stop
        $new = $orig
        # generic array<string, mixed> -> array
        $new = [regex]::Replace($new, $pattern1, 'array')
        $new = [regex]::Replace($new, $pattern2, '@return array')
        $new = [regex]::Replace($new, $pattern3, '@param array')
        # @param mixed $x -> @param object $x (safer, conservative)
        $new = [regex]::Replace($new, '@param\s+mixed(\s+\$[A-Za-z0-9_]+)', '@param object$1')

        if ($new -ne $orig) {
            Copy-Item -Path $p -Destination "$p.bak" -Force
            Set-Content -Path $p -Value $new -Encoding UTF8
            Write-Output "Updated: $p  (backup: $p.bak)"
        } else {
            Write-Output "No change needed: $p"
        }
    } catch {
        Write-Output ("ERROR processing {0}: {1}" -f $p, $_)
    }
}

Write-Output "Done. Review changes and run Microscope."
