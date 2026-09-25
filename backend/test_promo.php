<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = Illuminate\Http\Request::create('/api/produits', 'GET', ['promo' => 'true']);
$kernelHttp = $app->make(Illuminate\Contracts\Http\Kernel::class);
$res = $kernelHttp->handle($req);

echo "COUNT WHERE REMISE > 0: " . \App\Models\Produit::query()->where('remise', '>', 0)->count() . "\n";
echo "PROMO BOOLEAN: " . ($req->boolean('promo') ? 'true' : 'false') . "\n";
echo "RESPONSE FROM API: \n" . $res->getContent();
