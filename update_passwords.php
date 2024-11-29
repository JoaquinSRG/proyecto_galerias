<?php
require 'db.php'; // Asegúrate de que `db.php` tiene la configuración correcta de conexión

try {
    // Seleccionar todos los usuarios de la tabla
    $stmt = $pdo->query("SELECT id, username, password FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        // Aquí asumimos que la contraseña actual es insegura (ejemplo: SHA1)
        // Si ya tienes las contraseñas en texto plano, reemplaza esta lógica
        $newPassword = 'admin123'; // Cambia esto según el usuario o lo que sepas de las contraseñas
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hashea la nueva contraseña

        // Actualizar la contraseña en la base de datos
        $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $updateStmt->execute([
            'password' => $hashedPassword,
            'id' => $user['id']
        ]);

        echo "Contraseña actualizada para el usuario: " . $user['username'] . "<br>";
    }

    echo "Todas las contraseñas han sido actualizadas.";
} catch (PDOException $e) {
    echo "Error al actualizar contraseñas: " . $e->getMessage();
}
?>
