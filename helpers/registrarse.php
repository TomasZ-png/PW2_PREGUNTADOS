<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="../vista/login-style.css">
    <title>Registrarse</title>
</head>
<body>
<main>

    <div>
        <a class="volver-btn" href="home.php"><i class="bi bi-arrow-left-short"></i>Volver</a>
        <div class="contenedor-principal">
            <h1>Bienvenido a Preguntados!</h1>

            <form action="registrarse.php" method="post" enctype="multipart/form-data">
                <label for="nombre">Nombre:</label>
                <input id="nombre" type="text" name="nombre" placeholder="Nombre">
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

                if($_SERVER["REQUEST_METHOD"] == "POST"){

                    $nombre = $_POST["nombre"];
                    $correo = $_POST["correo"];
                    $contrasenia = $_POST["password"];
                    $errores = [];

                    if(empty($nombre) || empty($correo) || empty($contrasenia)){
                        $errores[] = "<p class='errores'>*Todos los campos son obligatorios</p>";
                    }else{

                        if(strlen($nombre) < 3 || strlen($nombre) > 20 ){
                            $errores[] = "<p class='errores'>*El nombre debe tener al menos entre 3 y 20 caracteres</p>";
                        }

                        $regex_correo = "/^[\w\-.]+@[\w\-]+(\.[a-zA-Z]{2,4}){1,2}$/";

                        if(!preg_match($regex_correo, $correo)){
                            $errores[] = "<p class='errores'>*Correo no valido</p>";
                        }

                        $regex_contrasenia = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\w\-.]{8,}$/";

                        if(!preg_match($regex_contrasenia, $contrasenia)){
                            $errores[] = "Contraseña no valida";
                        }
                    }

                    if(sizeof($errores) > 0){
                        echo implode("<br>", $errores);
                    } else {

                        $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ?");
                        $stmt->bind_param("s", $correo);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if($result->num_rows > 0){
                            echo "<p class='errores'>*El usuario ya existe</p>";
                        } else {
                            $stmt2 = $conn->prepare("INSERT INTO usuario (nombre, correo, password, rol) 
                                                    VALUES(?, ?, ?, 'USER')");
                            $stmt2->bind_param("sss", $nombre, $correo, $contrasenia);
                            $result2 = $stmt2->execute();
                            if($result2){
                                echo "<p>El usuario se registro correctamente</p>";

                                $id_usuario = $conn->insert_id;

                                $stmt3 = $conn->prepare("SELECT u.id_usuario, u.rol, u.nombre FROM usuario u WHERE id_usuario = ?");
                                $stmt3->bind_param("i", $id_usuario);
                                $stmt3->execute();
                                $resultado = ($stmt3->get_result())->fetch_assoc();

                                $_SESSION["id_usuario"] = $id_usuario;
                                $_SESSION["rol_usuario"] = $resultado['rol'];
                                $_SESSION["nombre_usuario"] = $resultado['nombre'];
                                header("location: home.php");
                                exit();
                            } else {
                                echo "<p class='errores'>*El usuario no se registro correctamente</p>";
                                header("location: registrarse.php");
                                exit();
                            }
                        }
                    }
                }
                ?>

                <button type="submit">Registrarse</button>
            </form>
        </div>
    </div>

</main>
</body>
</html>


