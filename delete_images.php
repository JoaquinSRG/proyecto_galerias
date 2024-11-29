<?php
require 'db.php';
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener datos enviados por el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'], $_POST['image_path'])) {
    $image_id = intval($_POST['image_id']);
    $file_path = $_POST['image_path'];

    try {
        // Lógica para administradores
        if ($_SESSION['role'] === 'admin') {
            // Un administrador puede eliminar cualquier imagen
            $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
            $stmt->execute(['id' => $image_id]);

            if (file_exists($file_path)) {
                unlink($file_path);
            }

            header("Location: admin.php");
            exit;
        }

        // Lógica para usuarios normales
        if ($_SESSION['role'] === 'user') {
            // Un usuario normal solo puede eliminar sus propias imágenes
            $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $image_id, 'user_id' => $_SESSION['user_id']]);

            // Verificar si se eliminó el registro antes de eliminar el archivo
            if ($stmt->rowCount() > 0 && file_exists($file_path)) {
                unlink($file_path);
            }

            header("Location: gallery.php");
            exit;
        }

        // Si el rol no es válido, redirigir al inicio de sesión
        header("Location: login.php");
        exit;

    } catch (PDOException $e) {
        echo "Error al eliminar la imagen: " . $e->getMessage();
    }
} else {
    echo "Solicitud inválida. <a href='gallery.php'>Volver</a>";
}
?>
