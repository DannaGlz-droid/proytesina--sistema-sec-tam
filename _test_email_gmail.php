<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Publication;
use App\Mail\ReportRejectedMail;
use Illuminate\Support\Facades\Mail;

// Enviar a un Gmail real
$gmailUser = User::where('email', 'dannaglz60@gmail.com')->first();
$pub = Publication::find(198);

if ($gmailUser && $pub) {
    echo "Enviando correo de rechazo a: " . $gmailUser->email . PHP_EOL;
    Mail::to($gmailUser->email)->send(new ReportRejectedMail(
        $pub,
        User::first(),
        'Prueba de rechazo - por favor confirma si te llega este correo',
        'http://localhost/reportes/198'
    ));
    echo "Correo enviado. Revisa tu bandeja de Gmail (y spam)." . PHP_EOL;
} else {
    echo "No se encontró el usuario o la publicación." . PHP_EOL;
    echo "Usuario: " . ($gmailUser ? 'OK' : 'NO') . PHP_EOL;
    echo "Publicación: " . ($pub ? 'OK' : 'NO') . PHP_EOL;
}