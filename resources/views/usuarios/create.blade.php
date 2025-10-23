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

    <form method="POST" action="{{ route('user.store') }}">
        @csrf
        <label for="" name="name" class="@error( 'name') danger @enderror">nombre</label>
        <input type="text" name="name">
        @error('name') <div class="text-red-600">{{ $message }}</div>
        @enderror

        <label for="" name="first_last_name" class="@error( 'first_last_name') danger @enderror">apellido paterno</label>
        <input type="text" name="first_last_name">
        @error('first_last_name') <div class="text-red-600">{{ $message }}</div>
        @enderror

        <label for="" name="second_last_name" class="@error( 'second_last_name') danger @enderror">apellido materno</label>
        <input type="text" name="second_last_name">
        @error('second_last_name') <div class="text-red-600">{{ $message }}</div>
        @enderror

        <label for="" name="email" class="@error( 'email') danger @enderror">email</label>
        <input type="email" name="email">
        @error('email') <div class="text-red-600">{{ $message }}</div>
        @enderror

        <label for="" name="phone" class="@error( 'phone') danger @enderror">phone</label>
        <input type="text" name="phone">
        @error('phone') <div class="text-red-600">{{ $message }}</div>
        @enderror

        <label for="" name="username" class="@error( 'username') danger @enderror">username</label>
        <input type="text" name="username">
        @error('username') <div class="text-red-600">{{ $message }}</div>
        @enderror

        <label for="" name="password" class="@error( 'password') danger @enderror">password</label>
        <input type="password" name="password">

        <label for="" name="password_confirmation" class="@error( 'password_confirmation') danger @enderror">Confirm Password</label>
        <input type="password" name="password_confirmation">

        <label for="" name="active">active</label>
        <input type="checkbox" name="active" value="1">

        <label for="" name="registration_date" class="@error( 'registration_date') danger @enderror">registration_date</label>
        <input type="date" name="registration_date">

        <label for="" name="last_session" class="@error( 'last_session') danger @enderror">last session</label>
        <input type="date" name="last_session">

        <label for="" name="position" class="@error( 'position_id') danger @enderror">Position</label>
        <select name="position_id">
            <option value="">-- Seleccionar --</option>
            @foreach ($positions as $position)
                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
            @endforeach
        </select>
        @error('position_id') <div class="text-red-600">{{ $message }}</div> @enderror

        <label for="" name="jurisdiction" class="@error( 'jurisdiction_id') danger @enderror">Jurisdiction</label>
        <select name="jurisdiction_id">
            <option value="">-- Seleccionar --</option>
            @foreach ($jurisdictions as $jurisdiction)
                <option value="{{ $jurisdiction->id }}" {{ old('jurisdiction_id') == $jurisdiction->id ? 'selected' : '' }}>{{ $jurisdiction->name }}</option>
            @endforeach
        </select>
        @error('jurisdiction_id') <div class="text-red-600">{{ $message }}</div> @enderror

        <label for="" name="role" class="@error( 'role_id') danger @enderror">Role</label>
        <select name="role_id">
            <option value="">-- Seleccionar --</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
        </select>
        @error('role_id') <div class="text-red-600">{{ $message }}</div> @enderror

        <input type="submit" value="Create">
    </form>
</body>
</html>