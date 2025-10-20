<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>

<div>
<h1>Bienvenido!</h1>
<form action="index.php?controller=LoginController&method=registrarse" method="post" enctype="multipart/form-data">
    <label for="name">Nombre completo</label>
    <input type="text" name="name" id="name" placeholder="Nombre completo">
    <label for="fecha_nac">Fecha de nacimiento</label>
    <input type="date" name="fecha_nac" id="fecha_nac" placeholder="Nombre completo">
    <label for="sexo">Sexo</label>
    <select name="sexo" id="sexo">
        <option disabled selected>-- Seleccione su sexo --</option>
        <option>Masculino</option>
        <option>Femenino</option>
        <option>Prefiero no cargarlo</option>
    </select>
    <label for="email">Correo</label>
    <input type="text" name="email" id="email" placeholder="Email">
    <label for="password">Contraseña</label>
    <input type="password" name="password" id="password" placeholder="Contraseña">
    <label for="foto-perfil">Foto de perfil</label>
    <input type="file" name="user_photo" id="foto-perfil">
    <button type="submit">Registrarse</button>
</form>
    <p>Ya tenes una cuenta? <a href="index.php?controller=LoginController&method=login">Inicia sesion</a></p>
</div>
</body>
</html>

<?php
