<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consultar la base de datos para encontrar al usuario
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Configurar variables de sesión
        $_SESSION['user_id'] = $user['id'];         // ID del usuario
        $_SESSION['username'] = $user['username']; // Nombre de usuario
        $_SESSION['role'] = $user['role'];         // Rol del usuario

        // Redirigir al panel correspondiente
        header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : 'gallery.php'));
        exit;
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <form action="" method="POST">
        <label for="username">Usuario:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Iniciar Sesión</button>
    </form>
    <a href="index.php">Regresar a la pagina principal</a>
</body>
</html>
