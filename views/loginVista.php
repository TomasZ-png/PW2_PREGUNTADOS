<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<h1>Bienvenido al Login</h1>

<form action="index.php?controller=LoginController&method=login" method="post">
    <label for="email">Correo</label>
    <input type="text" name="email" id="email" placeholder="Email">
    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Password">
    <button type="submit">Entrar</button>
</form>
</body>
</html>


<?php
