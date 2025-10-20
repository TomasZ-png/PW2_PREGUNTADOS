<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../vista/homeStyles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <title>Preguntados - inicio</title>
</head>
<body>
<main>

    <div class="header-arriba">
        <div class="titulo-logo-container">
            <div class="imagen-contenedor">
                <a href="home.php"><img class="logo" src="../img/ruleta-de-la-fortuna.png" alt="logo preguntados"></a>
            </div>
            <div class="titulo-container">
                <h1>Preguntados</h1>
            </div>
        </div>

        <div class="header-buttons">
            <?php
            session_start();
            if(!isset($_SESSION["id_usuario"])){
                header("Location: login.php");
                exit;
            } elseif(isset($_SESSION["id_usuario"])){
                echo '<a class="header-user" href="">' . $_SESSION["nombre_usuario"] . ' <i class="bi bi-person-circle"></i> <i class="bi bi-caret-down-fill"></i> </a>';
                echo '<ul class="header-dropdown">
                        <li><a href="logout.php"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a></li>
                    </ul>';

            } else {
                echo '<ul>
                        <li><a class="header-btn" href="registrarse.php">Registrarse</a></li>
                        <li><a class="header-btn" href="login.php">Iniciar Sesion</a></li>
                    </ul>';
            }
            ?>
        </div>
    </div>


    <?php
    if(isset($_SESSION["id_usuario"]) && isset($_SESSION["rol_usuario"]) && $_SESSION["rol_usuario"] == "ADMIN"){
        echo '<div class="admin-btn-container">';
        echo '<a class="agregar-btn" href="#"><i class="bi"></i> Buscar Partida</a>';
        echo '</div>';
    }
    ?>


</main>
</body>
</html>



