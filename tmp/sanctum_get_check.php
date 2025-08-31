<?php

require __DIR__.'/../vendor/autoload.php';

$app    = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a user and role via DB
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

$role = Role::firstOrCreate(['name' => 'Admin']);
$user = User::factory()->create(['email_verified_at' => now()]);
$user->assignRole('Admin');

// Use Sanctum actingAs
Sanctum::actingAs($user);

// Perform a GET request to helpdesk admin route
$request  = Illuminate\Http\Request::create(route('helpdesk.admin.tickets'));
$response = $kernel->handle($request);

echo "Status: {$response->getStatusCode()}\n";
foreach ($response->headers->all() as $k => $v) {
    echo "$k: ".implode(';', $v)."\n";
}

$kernel->terminate($request, $response);
