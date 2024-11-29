<?php
session_start();
require 'db.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Consultar todas las imágenes desde la base de datos
try {
    $stmt = $pdo->query("
        SELECT images.id, images.file_name, images.file_path, users.username
        FROM images
        JOIN users ON images.user_id = users.id
        ORDER BY images.uploaded_at DESC
    ");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar las imágenes: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
</head>
<body>
    <h1>Panel de Administración</h1>
    <p><a href="logout.php">Cerrar sesión</a></p>

    <h2>Imágenes de los Usuarios</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombre de la Imagen</th>
                <th>Imagen</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($image['username']); ?></td>
                        <td><?php echo htmlspecialchars($image['file_name']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($image['file_path']); ?>" alt="Imagen" style="max-width: 100px;"></td>
                        <td>
                            <!-- Formulario para eliminar la imagen -->
                            <form action="delete_images.php" method="POST">
    <input type="hidden" name="image_id" value="<?php echo htmlspecialchars($image['id']); ?>">
    <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($image['file_path']); ?>">
    <button type="submit">Eliminar</button>
</form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay imágenes disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

<?php
// Obtener la lista de usuarios desde la base de datos
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'user'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestión de Usuarios</h2>
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre de Usuario</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td>
                <form action="delete_users.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?')">Eliminar Usuario</button>
                </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</html>
