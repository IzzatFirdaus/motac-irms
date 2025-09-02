# Script to clean and reinstall/update Composer dependencies for a Laravel project.
# This script deletes the vendor folder and composer.lock, clears Composer cache,
# reinstalls dependencies, then updates them.
# It provides color-coded status and error messages for each step.
#
# Run this script in your project directory using one of the following commands:
#
# --- PowerShell (Windows Terminal, VSCode Terminal, or PowerShell window) ---
# powershell -ExecutionPolicy Bypass -File .\Refresh-Dependencies.ps1
#
# --- Command Prompt (CMD) ---
# powershell -ExecutionPolicy Bypass -File .\Refresh-Dependencies.ps1
#
# --- Git Bash ---
# powershell.exe -ExecutionPolicy Bypass -File ./Refresh-Dependencies.ps1
#
# Make sure you have permissions to execute PowerShell scripts.
# If you get execution policy errors, run PowerShell as Administrator or set the policy:
#   Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass

Write-Host "Starting dependency refresh process..." -ForegroundColor Green

# Step 1: Delete the vendor folder if it exists
Write-Host "Deleting vendor folder..."
if (Test-Path -Path "vendor" -PathType Container) {
    try {
        Remove-Item -Recurse -Force -Path "vendor" -ErrorAction Stop
        Write-Host "Vendor folder deleted." -ForegroundColor Green
    } catch {
        Write-Host "Error deleting vendor folder: $($_.Exception.Message)" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "Vendor folder not found." -ForegroundColor Yellow
}

# Step 2: Delete composer.lock file if it exists
Write-Host "Deleting composer.lock file..."
if (Test-Path -Path "composer.lock" -PathType Leaf) {
    try {
        Remove-Item -Force -Path "composer.lock" -ErrorAction Stop
        Write-Host "composer.lock deleted." -ForegroundColor Green
    } catch {
        Write-Host "Error deleting composer.lock: $($_.Exception.Message)" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "composer.lock not found." -ForegroundColor Yellow
}

# Step 3: Clear Composer cache (recommended for fresh dependency fetch)
Write-Host "Clearing Composer cache..."
try {
    composer clear-cache
    Write-Host "Composer cache cleared." -ForegroundColor Green
} catch {
    Write-Host "Error clearing Composer cache (is Composer in PATH?): $($_.Exception.Message)" -ForegroundColor Red
    # Not exiting, as this is not critical
}

# Step 4: Install dependencies (fresh install)
Write-Host "Running composer install (clean install all dependencies)..."
try {
    composer install --no-interaction --prefer-dist
    Write-Host "Composer install completed successfully." -ForegroundColor Green
} catch {
    Write-Host "Composer install failed. Please check for errors: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Step 5: Update dependencies (brings everything up-to-date as per composer.json)
Write-Host "Running composer update (update dependencies to latest allowed by composer.json)..."
try {
    composer update --no-interaction --prefer-dist
    Write-Host "Composer update completed successfully." -ForegroundColor Green
} catch {
    Write-Host "Composer update failed. Please check for errors: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Step 6: Post-update Laravel-specific commands (optional but recommended)
Write-Host "Running Laravel optimization commands..."
try {
    # Clear and cache config/routes/views (optional, but best practice after dependency changes)
    php artisan cache:clear
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan view:clear
    php artisan view:cache
    Write-Host "Laravel caches cleared and rebuilt." -ForegroundColor Green
} catch {
    Write-Host "Error running Laravel artisan cache commands: $($_.Exception.Message)" -ForegroundColor Yellow
    # Not exiting unless you want to enforce artisan health
}

Write-Host "Dependency refresh process finished successfully!" -ForegroundColor Green
exit 0
