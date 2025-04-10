<?php
$conexion = CreateConnection();
if ($conexion->connect_error) {
    echo "Error en la conexión a la base de datos";
    die("Error en la conexión a la base de datos: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: registro.html");
    exit();
}

// Obtener los datos enviados desde el formulario
$nombre = cleanInput($_POST['nombre']);
$email = cleanInput($_POST['email']);
$contrasena = cleanInput($_POST['contrasena']);

// Validar que los datos no estén vacíos
if (empty($nombre) || empty($email) || empty($contrasena)) {
    header("Location: registro.html");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectToRegister();
    exit();
}

try {
    //Generar sal para agregar el factor de pseudoaleatoriedad
    $salt = random_bytes(16); // Genera un salt aleatorio de 32 caracteres hexadecimales
    $salt_hex = bin2hex($salt); // Convierte el salt a hexadecimal

    $password_with_salt = $contrasena . $salt_hex; // Combina la contraseña con el salt

    $hashed_password = hash('sha256', $password_with_salt); // Hashea la contraseña con el salt

    // Código vulnerable a inyección SQL
    $sql = "INSERT INTO clientes (email, password, salt, names) VALUES (?, ?, ?, ?)";
    // Preparar la consulta
    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        redirectToRegister();
        exit();
    }

    // Ejecutar la consulta directamente sin preparar ni sanitizar


    $stmt->bind_param("ssss", $email, $hashed_password, $salt_hex, $nombre);

    if ($stmt->execute()) {
        redirectToWelcomePage();
    } else {
        redirectToRegister();
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}



// Verificar si se registró el usuario


$conexion->close();

/**
 * Redirige al usuario a la página de bienvenida.
 */
function redirectToWelcomePage()
{
    header("Location: bienvenido.php");
    exit();
}

/**
 * Redirige al usuario a la página de login.
 */
function redirectToRegister()
{
    header("Location: registro.html");
    exit();
}

// Función para limpiar la entrada del usuario
// Esta función es vulnerable a inyección SQL y no debería usarse en producción
function cleanInput($input)
{
    // Eliminar espacios en blanco al inicio y al final
    $input = trim($input);
    // Eliminar barras invertidas
    $input = stripslashes($input);
    // Convertir caracteres especiales a entidades HTML
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}



// Crear conexión a la base de datos
function CreateConnection()
{
    try {
        // Datos de conexión a la base de datos
        $host = "localhost";
        $usuario_db = "security";
        $contrasena_db = "security";
        $nombre_db = "usuarios";

        // Conexión a la base de datos
        $conexion = new mysqli($host, $usuario_db, $contrasena_db, $nombre_db);
        return $conexion;
    } catch (Exception $ex) {
        echo "Error en la conexión a la base de datos" . $ex->getMessage();
        exit();
    }
}
