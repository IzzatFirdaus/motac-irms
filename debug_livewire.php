<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Create a test BPM Staff user and authenticate them
$user = \App\Models\User::where('email', 'bmp@motac.com')->first();
if (!$user) {
    echo "No BPM Staff user found in database. Creating one for testing...\n";
    $user = \App\Models\User::create([
        'name' => 'Test BPM Staff',
        'email' => 'test-bmp@motac.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);

    // Assign BPM Staff role
    $role = \Spatie\Permission\Models\Role::where('name', 'BPM Staff')->first();
    if ($role) {
        $user->assignRole($role);
    }
}

// Authenticate the user
\Illuminate\Support\Facades\Auth::login($user);

echo "Authenticated as: " . $user->name . " (" . $user->email . ")\n";
echo "User roles: " . $user->getRoleNames()->toJson() . "\n\n";

// Create VerticalMenu component instance
$component = new \App\Livewire\Sections\Menu\VerticalMenu();

echo "=== DEBUG LIVEWIRE COMPONENT ===\n";

// Test getUserRolesProperty
$userRoles = $component->getUserRolesProperty();
echo "User roles: " . $userRoles->toJson() . "\n";

// Test getFilteredMenuDataProperty
$filteredMenu = $component->getFilteredMenuDataProperty();
echo "Filtered menu count: " . count($filteredMenu) . "\n";

foreach ($filteredMenu as $index => $item) {
    $item = (object) $item;
    if (isset($item->menuHeader)) {
        echo "Menu Header [$index]: " . $item->menuHeader . "\n";
    } else {
        echo "Menu Item [$index]: " . ($item->name ?? 'No name') . "\n";
        if (isset($item->role)) {
            echo "  - Required roles: " . json_encode($item->role) . "\n";
        }
        if (isset($item->submenu)) {
            echo "  - Has submenu with " . count($item->submenu) . " items\n";
        }
    }
}

// Look specifically for System Settings
echo "\n=== SYSTEM SETTINGS CHECK ===\n";
foreach ($filteredMenu as $item) {
    $item = (object) $item;
    if (isset($item->menuHeader) && str_contains($item->menuHeader, 'system_settings')) {
        echo "FOUND System Settings header: " . $item->menuHeader . "\n";
    }
    if (isset($item->name) && str_contains($item->name, 'system_settings')) {
        echo "FOUND System Settings item: " . $item->name . "\n";
        echo "  - Roles: " . json_encode($item->role ?? 'none') . "\n";
    }
}

echo "\n=== RENDERING TEST ===\n";
// Test the render method
try {
    $renderResult = $component->render();
    $renderData = $renderResult->getData();

    echo "Render data keys: " . implode(', ', array_keys($renderData)) . "\n";
    if (isset($renderData['menuData'])) {
        echo "MenuData menu count: " . count($renderData['menuData']->menu ?? []) . "\n";

        // Check for System Settings in render data
        foreach (($renderData['menuData']->menu ?? []) as $item) {
            $item = (object) $item;
            if (isset($item->menuHeader) && str_contains($item->menuHeader, 'system_settings')) {
                echo "RENDER: System Settings header present\n";
            }
        }
    }
} catch (Exception $e) {
    echo "Render error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
