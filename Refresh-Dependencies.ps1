# Script to clean and reinstall/update Composer dependencies for a Laravel project.

Write-Host "Starting dependency refresh process..." -ForegroundColor Green

# Step 1: Delete the vendor folder
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

# Step 2: Delete composer.lock file
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

# Step 3: Clear Composer cache (optional but good practice)
Write-Host "Clearing Composer cache..."
try {
    composer clear-cache -ErrorAction Stop
    Write-Host "Composer cache cleared." -ForegroundColor Green
} catch {
    Write-Host "Error clearing Composer cache (is Composer in PATH?): $($_.Exception.Message)" -ForegroundColor Red
    # Decide if you want to exit or continue if cache clear fails
}

# Step 4: Install dependencies
Write-Host "Running composer install..."
try {
    composer install -ErrorAction Stop
    Write-Host "Composer install completed." -ForegroundColor Green
} catch {
    Write-Host "Composer install failed. Please check for errors: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Step 5: Update dependencies (as per your previous successful method)
Write-Host "Running composer update..."
try {
    composer update -ErrorAction Stop
    Write-Host "Composer update completed." -ForegroundColor Green
} catch {
    Write-Host "Composer update failed. Please check for errors: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "Dependency refresh process finished successfully!" -ForegroundColor Green
exit 0
