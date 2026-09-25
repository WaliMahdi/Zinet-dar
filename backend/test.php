<?php
try {
    $user = \App\Models\User::where('email', 'sedielectro@gmail.com')->first();
    \Illuminate\Support\Facades\Auth::guard('sanctum')->setUser($user);
    $req = \Illuminate\Http\Request::create('/api/produits/1', 'POST', [
        '_method' => 'PUT',
        'nom' => 'Test',
        'prix' => '10',
        'actif' => '0'
    ]);
    $req->headers->set('Accept', 'application/json');
    $res = app()->handle($req);
    echo "STATUS: " . $res->getStatusCode() . "\n";
    echo "CONTENT: " . $res->getContent() . "\n";
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
