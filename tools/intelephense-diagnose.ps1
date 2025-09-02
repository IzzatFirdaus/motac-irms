<#
Intelephense & Laravel Livewire diagnostic helper
- Dry-run by default (no destructive actions).
- Use -RunComposer to run composer install/dump-autoload/show for packages.
- Use -ClearIntelephenseCache to delete found .intelephense cache directories inside the workspace.
- Use -DeleteVendor to delete vendor/ (requires -Force to actually run).

Usage examples (PowerShell):
  .\tools\intelephense-diagnose.ps1                # dry run checks
  .\tools\intelephense-diagnose.ps1 -RunComposer  # run composer checks/commands
  .\tools\intelephense-diagnose.ps1 -ClearIntelephenseCache
  .\tools\intelephense-diagnose.ps1 -DeleteVendor -Force
#>
param(
    [switch]$RunComposer,
    [switch]$ClearIntelephenseCache,
    [switch]$DeleteVendor,
    [switch]$Force
)

Set-StrictMode -Version Latest
$root = Split-Path -Path $MyInvocation.MyCommand.Path -Parent | Resolve-Path -Relative
# If script is in tools/, repo root is parent
$repoRoot = Resolve-Path (Join-Path $root "..")
Write-Host "Repository root: $repoRoot" -ForegroundColor Cyan

# Helper: safe-run external command
function Invoke-ExternalCommand {
    param($exe, $arguments)
    # Accept $arguments as string or array. If string, split on spaces to build an array of args.
    if ($null -eq $arguments) {
        Write-Host "\n> Running: $exe" -ForegroundColor DarkGray
        try {
            & $exe 2>&1 | ForEach-Object { Write-Host $_ }
            return $true
        } catch {
            Write-Host "Command failed: $_" -ForegroundColor Yellow
            return $false
        }
    }

    if ($arguments -is [System.Array]) {
        $argArray = $arguments
    } else {
        # Simple split on spaces. This handles common composer CLI usages like 'install --no-interaction'.
        $argArray = $arguments -split ' ' | Where-Object { $_ -ne '' }
    }

    $argString = $argArray -join ' '
    Write-Host "\n> Running: $exe $argString" -ForegroundColor DarkGray
    try {
        & $exe @argArray 2>&1 | ForEach-Object { Write-Host $_ }
        return $true
    } catch {
        Write-Host "Command failed: $_" -ForegroundColor Yellow
        return $false
    }
}

# 1) Check composer.json
$composerJsonPath = Join-Path $repoRoot 'composer.json'
if (Test-Path $composerJsonPath) {
    Write-Host "Found composer.json" -ForegroundColor Green
    $composerJson = Get-Content $composerJsonPath -Raw | ConvertFrom-Json
} else {
    Write-Host "composer.json not found in repo root." -ForegroundColor Red
}

# 2) Check for packages of interest
$packagesToCheck = @('livewire/livewire','illuminate/pagination')
foreach ($pkg in $packagesToCheck) {
    $presentInComposer = $false
    try {
        if ($composerJson.require -and ($composerJson.require.PSObject.Properties.Name -contains $pkg)) { $presentInComposer = $true }
    } catch { }
    if ($presentInComposer) {
        Write-Host "Package $pkg appears in composer.json (as a dependency)." -ForegroundColor Green
    } else {
        Write-Host "Package $pkg NOT found in composer.json (recommended if you rely on it)." -ForegroundColor Yellow
    }
}

# 3) Check vendor directory
$vendorPath = Join-Path $repoRoot 'vendor'
if (Test-Path $vendorPath) {
    Write-Host "vendor/ directory exists." -ForegroundColor Green
} else {
    Write-Host "vendor/ directory NOT found. Many IDE false positives come from missing vendor/" -ForegroundColor Yellow
}

# 4) Locate .intelephense caches inside repo
Write-Host "\nSearching for .intelephense cache folders inside the workspace..." -ForegroundColor Cyan
$inteleCaches = Get-ChildItem -Path $repoRoot -Recurse -Directory -Force -ErrorAction SilentlyContinue | Where-Object { $_.Name -ieq '.intelephense' }
if ($inteleCaches) {
    Write-Host "Found the following .intelephense directories:" -ForegroundColor Green
    $inteleCaches | ForEach-Object { Write-Host " - $_.FullName" }
    if ($ClearIntelephenseCache) {
        foreach ($d in $inteleCaches) {
            Write-Host "Removing $($d.FullName)..." -ForegroundColor Yellow
            Remove-Item -LiteralPath $d.FullName -Recurse -Force -ErrorAction SilentlyContinue
        }
        Write-Host "Cleared local .intelephense caches under the workspace." -ForegroundColor Green
    } else {
        Write-Host "To clear these caches, re-run with -ClearIntelephenseCache." -ForegroundColor DarkGray
    }
} else {
    Write-Host "No .intelephense directories found inside the workspace." -ForegroundColor Gray
    Write-Host "Note: Intelephense also stores caches in your editor's global storage. Use VS Code: Command Palette → 'Intelephense: Clear Cache' if needed." -ForegroundColor DarkGray
}

# 5) Optional composer actions
if ($RunComposer) {
    Write-Host "\n=== Composer actions (requested) ===" -ForegroundColor Cyan
    # composer install
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Invoke-ExternalCommand composer 'install --no-interaction'
        Invoke-ExternalCommand composer 'dump-autoload -o'
        foreach ($pkg in $packagesToCheck) {
            Invoke-ExternalCommand composer "show $pkg"
        }
    } else {
        Write-Host "composer not found in PATH. Please install Composer or run these commands manually from the repo root." -ForegroundColor Red
    }
} else {
    Write-Host "\nComposer commands were NOT run. To run them, re-run with -RunComposer." -ForegroundColor DarkGray
}

# 6) Optional vendor removal (destructive)
if ($DeleteVendor) {
    if (-not $Force) {
        Write-Host "\nDelete vendor requested but -Force not supplied. To actually delete vendor/, re-run with -DeleteVendor -Force" -ForegroundColor Yellow
    } else {
        if (Test-Path $vendorPath) {
            Write-Host "Deleting vendor/ (this is destructive)..." -ForegroundColor Red
            Remove-Item -LiteralPath $vendorPath -Recurse -Force -ErrorAction Stop
            Write-Host "vendor/ deleted. Run 'composer install' afterwards." -ForegroundColor Green
        } else {
            Write-Host "vendor/ not found. Nothing to delete." -ForegroundColor Gray
        }
    }
}

# 7) Tips output
Write-Host "\n=== Quick tips ===" -ForegroundColor Cyan
Write-Host " - If Intelephense still shows 'undefined' after vendor exists, run: In VS Code: Command Palette -> 'Intelephense: Clear Cache', then reload VS Code." -ForegroundColor DarkGray
Write-Host " - Ensure your workspace root is the project root (open VS Code at repo root)." -ForegroundColor DarkGray
Write-Host " - Ensure Livewire Trait usage (e.g. use Livewire\WithPagination;) exists inside Livewire components when using pagination methods like resetPage()." -ForegroundColor DarkGray
Write-Host " - Add phpdoc type hints for properties and return types to help Intelephense if necessary." -ForegroundColor DarkGray

Write-Host "\nDone." -ForegroundColor Green
Exit 0
