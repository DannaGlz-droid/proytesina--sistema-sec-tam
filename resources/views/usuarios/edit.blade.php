<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    <a href="{{ route('user.index') }}">Back to User List</a>

    <form method="POST" action="{{ route('user.update', $user->id) }}">
        @method('PUT')
        @csrf
        <label for="" name="name">nombre</label>
        <input type="text" name="name" value="{{ $user->name }}">

        <label for="" name="first_last_name">apellido paterno</label>
        <input type="text" name="first_last_name" value="{{ $user->first_last_name }}">

        <label for="" name="second_last_name">apellido materno</label>
        <input type="text" name="second_last_name" value="{{ $user->second_last_name }}">

        <label for="" name="email">email</label>
        <input type="email" name="email" value="{{ $user->email }}">

        <label for="" name="phone">phone</label>
        <input type="text" name="phone" value="{{ $user->phone }}">

        <label for="" name="username">username</label>
        <input type="text" name="username" value="{{ $user->username }}">

        <label for="" name="password">password</label>
        <input type="password" name="password">

        <label for="" name="password_confirmation">Confirm Password</label>
        <input type="password" name="password_confirmation">

    <label for="" name="is_active">Active</label>
    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>

        <label for="" name="registration_date">registration_date</label>
        <input type="date" name="registration_date" value="{{ $user->registration_date }}">

        <label for="" name="last_session">last session</label>
        <input type="date" name="last_session" value="{{ $user->last_session }}">

        <label for="" name="position">Position</label>
        <select name="position_id">
            <option value="">-- Seleccionar --</option>
            @foreach ($positions as $position)
                <option value="{{ $position->id }}" {{ $user->position_id == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
            @endforeach
        </select>
        @error('position_id') <div class="text-red-600">{{ $message }}</div> @enderror

        <label for="" name="jurisdiction">Jurisdiction</label>
        <select name="jurisdiction_id">
            <option value="">-- Seleccionar --</option>
            @foreach ($jurisdictions as $jurisdiction)
                <option value="{{ $jurisdiction->id }}" {{ $user->jurisdiction_id == $jurisdiction->id ? 'selected' : '' }}>{{ $jurisdiction->name }}</option>
            @endforeach
        </select>
        @error('jurisdiction_id') <div class="text-red-600">{{ $message }}</div> @enderror

        <label for="" name="role">Role</label>
        <select name="role_id">
            <option value="">-- Seleccionar --</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role_id') <div class="text-red-600">{{ $message }}</div> @enderror

        <input type="submit" value="Update">
    </form>
</body>
</html>