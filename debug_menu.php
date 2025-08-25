<?php

// Simple debug script to test menu filtering
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

// Create test user with BPM Staff role
$user = User::factory()->create();
Role::findOrCreate('BPM Staff', 'web');
$user->assignRole('BPM Staff');

echo "User roles: " . json_encode($user->getRoleNames()) . "\n";

// Test menu config
$menuConfig = config('menu.menu');
echo "First menu item type: " . gettype($menuConfig[0]) . "\n";
echo "First menu item content: " . json_encode($menuConfig[0]) . "\n";

// Find the System Settings item
foreach ($menuConfig as $key => $item) {
    if (isset($item['name']) && $item['name'] === 'menu.system_settings.title') {
        echo "Found System Settings item at index $key:\n";
        echo "Item content: " . json_encode($item) . "\n";
        echo "Item role: " . json_encode($item['role'] ?? 'no role') . "\n";
        break;
    }
}

// Test role filtering logic
function testRoleFilter($item, $userRoles) {
    if (is_array($item)) {
        $item = (object) $item;
    }

    echo "Testing item: " . ($item->name ?? $item->menuHeader ?? 'unknown') . "\n";
    echo "Item role: " . json_encode($item->role ?? null) . "\n";
    echo "User roles: " . json_encode($userRoles) . "\n";

    if (!isset($item->role) || empty($item->role)) {
        echo "Result: SHOW (no role restriction)\n";
        return true;
    }

    if (in_array('Admin', $userRoles)) {
        echo "Result: SHOW (user is Admin)\n";
        return true;
    }

    $requiredRoles = is_array($item->role) ? $item->role : [$item->role];
    $hasRole = !empty(array_intersect($userRoles, $requiredRoles));
    echo "Required roles: " . json_encode($requiredRoles) . "\n";
    echo "Has role: " . ($hasRole ? 'YES' : 'NO') . "\n";
    echo "Result: " . ($hasRole ? 'SHOW' : 'HIDE') . "\n";

    return $hasRole;
}

// Test the System Settings item
foreach ($menuConfig as $item) {
    if (isset($item['name']) && $item['name'] === 'menu.system_settings.title') {
        echo "\n=== Testing System Settings filtering ===\n";
        testRoleFilter($item, ['BPM Staff']);
        echo "\n=== Testing with Admin role ===\n";
        testRoleFilter($item, ['Admin']);
        break;
    }
}
