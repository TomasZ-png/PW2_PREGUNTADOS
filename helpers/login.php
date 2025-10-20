<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="../vista/login-style.css">
    <title>Iniciar Sesion</title>
</head>


<body>

<div class="header-arriba">
    <div class="titulo-logo-container">
        <div>
            <a href="home.php"><img class="logo" src="../img/ruleta-de-la-fortuna.png" alt="logo preguntados"></a>
        </div>
        <div class="titulo-container">
            <h1>Preguntados</h1>
        </div>
    </div>
</div>

<main>



    <div class="contenedor-principal">

        <h1>Bienvenido de nuevo!</h1>

        <form action="login.php" method="post" enctype="multipart/form-data">
            <label for="correo">Correo:</label>
            <input id="correo" type="text" name="correo" placeholder="Correo">
            <label for="contrasenia">Contraseña:</label>
            <input id="contrasenia" type="password" name="password" placeholder="Contraseña">



            <?php
            session_start();

            if(isset($_SESSION["id_usuario"]) || isset($_SESSION["nombre_usuario"]) || isset($_SESSION["rol_usuario"])){
                header("Location: home.php");
                exit();
            }

            include_once(__DIR__ . "/MyDatabase.php");
            $conexion = new MyDatabase();
            $conn = $conexion->getConexion();

            if($_SERVER["REQUEST_METHOD"]=="POST"){
                $correo = $_POST["correo"];
                $password = $_POST["password"];

                if(empty($correo) || empty($password)){
                    echo "<p class='errores'>*Todos los campos son obligatorios</p>";
                } else {
                    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE correo = ? AND password = ?");
                    $stmt->bind_param("ss", $correo, $password);
                    $stmt->execute();
                    $result = ($stmt->get_result())->fetch_assoc();

                    if($result){
                        $_SESSION["nombre_usuario"] = $result["nombre"];
                        $_SESSION["rol_usuario"] = $result["rol"];
                        $_SESSION["id_usuario"] = $result["id_usuario"];
                        header("location: home.php");
                        exit();
                    } else {
                        echo "<p class='errores'>*Correo o contraseña incorrectos</p>";
                    }
                }
            }

            ?>
            <button type="submit">Iniciar Sesion</button>
            <button><a class="registrarse-btn" href="registrarse.php">Registrarse</a></button>
        </form>
    </div>
    </div>

</main>
</body>
</html>