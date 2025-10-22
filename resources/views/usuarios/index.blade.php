<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <h1>Hola desde la vista de usuarios</h1>

    <h2>Lista de usuarios geniales ♥</h2>



    @if($users->isEmpty())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">No hay usuarios disponibles.</div>
        @else
            <div class="overflow-x-auto bg-white rounded shadow">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Usuario</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Nombre completo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Email</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Teléfono</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Rol</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cargo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Jurisdicción</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Activo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Alta</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Última sesión</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->id }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->username ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->name ?? '—' }} {{ $user->first_last_name ?? '' }} {{ $user->second_last_name ?? '' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->email ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ $user->phone ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ optional($user->role)->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ optional($user->position)->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ optional($user->jurisdiction)->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm">@if($user->is_active) <span class="text-green-600 font-semibold">Sí</span> @else <span class="text-red-600">No</span> @endif</td>
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    @php
                                        $reg = $user->registration_date;
                                        if ($reg instanceof \DateTimeInterface) {
                                            echo $reg->format('d/m/Y');
                                        } elseif (!empty($reg)) {
                                            try { echo \Carbon\Carbon::parse($reg)->format('d/m/Y'); } catch (\Throwable $e) { echo '—'; }
                                        } else { echo '—'; }
                                    @endphp
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">
                                    @php
                                        $last = $user->last_session;
                                        if ($last instanceof \DateTimeInterface) {
                                            echo $last->format('d/m/Y H:i');
                                        } elseif (!empty($last)) {
                                            try { echo \Carbon\Carbon::parse($last)->format('d/m/Y H:i'); } catch (\Throwable $e) { echo '—'; }
                                        } else { echo '—'; }
                                    @endphp
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="mt-4 text-sm text-gray-600">Total usuarios: {{ $users->count() }}</p>

    <p>¡Gracias por visitar nuestra página de usuarios!</p>
</body>
</html>