<?php

require __DIR__.'/../vendor/autoload.php';
$app    = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a user
$user = App\Models\User::factory()->create(['email_verified_at' => now()]);
$user->assignRole('Admin');

// Simulate actingAs using the session guard
Illuminate\Support\Facades\Auth::guard('web')->login($user);

$request  = Illuminate\Http\Request::create(route('helpdesk.admin.tickets'), 'GET');
$response = $app->handle($request);

// Output status and headers
echo 'Status: '.$response->getStatusCode()."\n";
foreach ($response->headers->all() as $k => $v) {
    echo $k.': '.implode(', ', $v)."\n";
}

// If redirect, show the target
if ($response->isRedirection()) {
    echo 'Redirect target: '.$response->headers->get('Location')."\n";
}

// Terminate
$app->terminate($request, $response);
