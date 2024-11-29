<?php
require 'db.php'; // Cargar la conexión a la base de datos
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    die("Error: Usuario no autenticado. <a href='login.php'>Inicia sesión</a>");
}

$user_id = $_SESSION['user_id']; // Obtener el ID del usuario autenticado
$upload_dir = "./uploads/$user_id"; // Directorio del usuario

// Crear el directorio si no existe
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Manejo de la subida de imágenes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file = $_FILES['image'];
    $target_file = $upload_dir . '/' . basename($file['name']);
    $file_name = basename($file['name']);

    // Validar el tipo de archivo (opcional)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo "Solo se permiten imágenes JPEG, PNG o GIF.";
    } elseif ($file['size'] > 2 * 1024 * 1024) { // Límite de 2MB
        echo "El archivo es demasiado grande (máximo 2MB).";
    } elseif (move_uploaded_file($file['tmp_name'], $target_file)) {
        try {
            // Guardar la información de la imagen en la base de datos
            $stmt = $pdo->prepare("INSERT INTO images (user_id, file_name, file_path) VALUES (:user_id, :file_name, :file_path)");
            $stmt->execute([
                'user_id' => $user_id,
                'file_name' => $file_name,
                'file_path' => $target_file
            ]);

            echo "Imagen subida y registrada correctamente.";
        } catch (PDOException $e) {
            echo "Error al registrar la imagen en la base de datos: " . $e->getMessage();
        }
    } else {
        echo "Error al mover la imagen al directorio.";
    }
}

// Consultar imágenes del usuario desde la base de datos
try {
    $stmt = $pdo->prepare("SELECT * FROM images WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar imágenes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de <?php echo htmlspecialchars($_SESSION['username']); ?></title>
</head>
<body>
    <h1>Galería de <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <p><a href="logout.php">Cerrar sesión</a></p>

    <!-- Formulario para subir imágenes -->
    <h2>Subir Imagen</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Subir Imagen</button>
    </form>

    <!-- Listado de imágenes -->
    <h2>Mis Imágenes</h2>
    <div>
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $image): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="Imagen" style="max-width: 200px; margin: 10px;">
                    <form action="delete_users.php" method="POST">
    <input type="hidden" name="image_id" value="<?php echo htmlspecialchars($image['id']); ?>">
    <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($image['file_path']); ?>">
    <button type="submit">Eliminar</button>
</form>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes imágenes en tu galería.</p>
        <?php endif; ?>
    </div>
</body>
</html>
