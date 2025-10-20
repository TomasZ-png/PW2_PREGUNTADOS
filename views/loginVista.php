<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<div>
<h1>Bienvenido de nuevo!</h1>
<form action="index.php?controller=LoginController&method=login" method="post">
    <label for="email">Correo</label>
    <input type="text" name="email" id="email" placeholder="Email">
    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Password">
    <button type="submit">Iniciar Sesion</button>
</form>
    <p>No tenes una cuenta? <a href="index.php?controller=LoginController&method=registrarse">Registrate</a></p>
</div>
</body>
</html>


<?php
