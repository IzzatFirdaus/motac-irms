<?php

require __DIR__.'/../vendor/autoload.php';
$app    = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
Spatie\Permission\Models\Role::firstOrCreate(['name' => 'IT Admin', 'guard_name' => 'web']);
echo "Roles ensured\n";

// Now create user and perform request
$user = App\Models\User::factory()->create(['email_verified_at' => now()]);
$user->assignRole('Admin');
Illuminate\Support\Facades\Auth::guard('web')->login($user);

$request  = Illuminate\Http\Request::create(route('helpdesk.admin.tickets'), 'GET');
$response = $app->handle($request);

echo 'Status: '.$response->getStatusCode()."\n";
if ($response->isRedirection()) {
    echo 'Redirect target: '.$response->headers->get('Location')."\n";
}

$app->terminate($request, $response);
