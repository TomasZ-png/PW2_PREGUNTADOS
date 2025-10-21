<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/loginStyles.css">
</head>
<body>

<div class="form-container">
<h1>Bienvenido de nuevo!</h1>
<form action="index.php?controller=LoginController&method=login" method="post">
    <label for="email">Correo</label>
    <input type="text" name="email" id="email" placeholder="Email">
    <label for="password">Contraseña</label>
    <input type="password" name="password" id="password" placeholder="Contraseña">
    <button type="submit">Iniciar Sesion</button>
</form>
    <p>No tenes una cuenta? <a href="index.php?controller=LoginController&method=registrarse">Registrate</a></p>
</div>
</body>
</html>


