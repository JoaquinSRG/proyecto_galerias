<?php
session_start();
require 'db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Verificar si se pasó el parámetro user_id
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Convertir a entero por seguridad

    // Consultar imágenes del usuario especificado
    try {
        $stmt = $pdo->prepare("SELECT * FROM images WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al consultar imágenes: " . $e->getMessage());
    }
} else {
    die("Usuario no especificado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imágenes del Usuario</title>
</head>
<body>
    <h1>Imágenes del Usuario</h1>
    <p><a href="admin.php">Volver al Panel de Administración</a></p>

    <div>
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $image): ?>
                <div>
                    <img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="Imagen" style="max-width: 200px;">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>El usuario no tiene imágenes.</p>
        <?php endif; ?>
    </div>
</body>
</html>
