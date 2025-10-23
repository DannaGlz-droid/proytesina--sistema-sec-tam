<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <h1>{{ $user->name }}</h1>

    <p>Apellido Paterno: {{ $user->first_last_name }}</p>
    <p>Apellido Materno: {{ $user->second_last_name }}</p>
    <p>Email: {{ $user->email }}</p>
    <p>Teléfono: {{ $user->phone }}</p>
    <p>Username: {{ $user->username }}</p>
    <p>Activo: {{ $user->is_active ? 'Sí' : 'No' }}</p>
    <p>Fecha de Registro: {{ $user->registration_date }}</p>
    <p>Última Sesión: {{ $user->last_session }}</p>
    <p>Posición: {{ $user->position->name ?? 'N/A' }}</p>
    <p>Jurisdicción: {{ $user->jurisdiction->name ?? 'N/A' }}</p>
    <p>Rol: {{ $user->role->name ?? 'N/A' }}</p>
</body>
</html>