<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
</head>

<body>

    <h2>Register Pelanggan</h2>

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <p style="color:red">{{ $error }}</p>
        @endforeach
    @endif

    <form method="POST" action="/register">
        @csrf

        <input type="text" name="nama" placeholder="Nama" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>

        <button type="submit">Daftar</button>
    </form>

    <a href="/">Sudah punya akun? Login</a>

</body>

</html>