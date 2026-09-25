<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$user = \App\Models\User::where('email', 'sedielectro@gmail.com')->first();
\Illuminate\Support\Facades\Auth::guard('sanctum')->setUser($user);

$req = \Illuminate\Http\Request::create('/api/produits/1', 'POST', [
    '_method' => 'PUT',
    'nom' => 'Test Modifié',
    'prix' => '10',
    'actif' => '0'
]);
$req->headers->set('Accept', 'application/json');

$res = $kernel->handle($req);

echo "STATUS: " . $res->getStatusCode() . "\n";
echo "CONTENT: " . $res->getContent() . "\n";
