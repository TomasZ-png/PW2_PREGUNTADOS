<?php

class LoginModel
{

    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function login($correo, $password){
        $stmt = $this->conexion->prepare("SELECT * FROM usuario WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $result = ($stmt->get_result())->fetch_assoc();

        if($result && password_verify($password, $result['password'])){
            return $result;
        }

        return false;
    }


    public function registrarse($nombre, $fecha_nac, $sexo, $email, $password, $foto_perfil){
        $errores = [];

        $registrado = false;

        // == VALIDACIONES DE INPUT DELÑ FORM ==

        if (empty($nombre) || empty($fecha_nac) || empty($sexo) || empty($email) || empty($password) || !isset($_FILES["user_photo"])) {
            $errores[] = "<p class='errores'>*Todos los campos son obligatorios.</p>";
        } else {

            if (strlen($nombre) < 3 || strlen($nombre) > 20) {
                $errores[] = "<p class='errores'>*El nombre debe tener al menos 3 caracteres</p>";
            }

            $regex_correo = "/^[\w\-.]+@[\w\-]+(\.[a-zA-Z]{2,4}){1,2}$/";

            if (!preg_match($regex_correo, $email)) {
                $errores[] = "<p class='errores'>*Correo no valido</p>";
            }

            $regex_contrasenia = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\w\-.]{8,}$/";

            if (!preg_match($regex_contrasenia, $password)) {
                $errores[] = "<p class='errores'>*Contraseña no valida (Al menos 8 caracteres, una mayuscula y un numero)</p>";
            }

            // obtenemos la fecha actual, ingresamos la fecha del form.
            $fecha_actual = time();
            $fecha_ingresada = $fecha_nac;
            $fecha_ingresada_timestamp = strtotime($fecha_ingresada);
            if ($fecha_actual < $fecha_ingresada_timestamp) {
                $errores[] = "<p class='errores'>*No puede ingresar una fecha posterior a la del dia de hoy.</p>";
            }
        }

        // == VERIFICAMOS SI HUBIERON ERRORES, SI LOS HAY CORTAMOS EL FLUJO ==

            if (!empty($errores)) {
                echo implode("<br>", $errores);
                return;
            }

            // == VALIDACIONES DE LA IMAGEN ==

            $nombreFinalImagen = null;

            if (!$_FILES["user_photo"]["name"] == '' && $_FILES["user_photo"]["error"] === UPLOAD_ERR_OK) {
                $nombreImagen = $_FILES["user_photo"]["name"];
                $tipoImagen = $_FILES["user_photo"]["type"];
                $tamanioImagen = $_FILES["user_photo"]["size"];
                $nombreTemporal = $_FILES["user_photo"]["tmp_name"];

                $erroresImagen = [];


                $tamanio_max = 5 * 1024 * 1024;
                if ($tamanioImagen > $tamanio_max) {
                    $erroresImagen[] = "La imagen no puede superar los 5 MB";
                }

                $extensiones_permitidas = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                if (!in_array($tipoImagen, $extensiones_permitidas)) {
                    $erroresImagen[] = "El tipo de imagen no es permitido";
                }

                if (!empty($erroresImagen)) {
                    echo implode("<br>", $erroresImagen);
                    return;
                }

                $directorio = __DIR__ . "/../src/img/user-img/";
                if (!is_dir($directorio)) mkdir($directorio, 0777, true);

                $extensionArchivo = strtolower(pathinfo($nombreImagen, PATHINFO_EXTENSION));
                $nombreFinalImagen = uniqid('user_') . "." . $extensionArchivo;
                $rutaDeImagen = $directorio . $nombreFinalImagen;

                if (!move_uploaded_file($nombreTemporal, $rutaDeImagen)) {
                    echo "<p class='errores'>Error al guardar la imagen.</p>";
                    return;
                }
            }

            // ===== COMPROBAMOS SI EL USUARIO YA EXISTE =====

            $stmt = $this->conexion->prepare("SELECT 1 FROM usuario WHERE correo = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $usuarioExistente = $stmt->get_result();

            if ($usuarioExistente->num_rows > 0) {
                echo "<p class='errores'>El usuario con el correo ingresado ya existe</p>";
                return;
            }

            // ===== REGISTRAMOS EL USUARIO =====

            $stmt2 = $this->conexion->prepare("
            INSERT INTO usuario (nombre_completo, correo, password, anio_nacimiento, sexo, foto_perfil, rol)
            VALUES (?, ?, ?, ?, ?, ?, 'USER')");

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt2->bind_param("ssssss", $nombre, $email, $passwordHash, $fecha_nac, $sexo, $nombreFinalImagen);
            $result = $stmt2->execute();

            if ($result) {
                $id_usuario = $this->conexion->getConexion()->insert_id;

                $stmt3 = $this->conexion->prepare("
                SELECT id_usuario, nombre_completo, rol FROM usuario WHERE correo = ?");

                $stmt3->bind_param("s", $email);
                $stmt3->execute();
                $result2 = $stmt3->get_result()->fetch_assoc();

                $_SESSION['id'] = $id_usuario;
                $_SESSION['nombre'] = $result2['nombre_completo'];
                $_SESSION['rol'] = $result2['rol'];

                echo "Usuario registrado correctamente.";
                $registrado = true;
            } else {
                echo "Error al registrar usuario.";
            }
        return $registrado;
    }
}