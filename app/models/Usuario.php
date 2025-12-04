<?php
require_once 'Conexion.php';
class Usuario {
    public static function findByEmail($correo) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM administrador WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function updatePasswordHash($id, $hash) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("UPDATE administrador SET password = ? WHERE id_admin = ?");
        return $stmt->execute([$hash, $id]);
    }
    public static function verificar($correo, $password) {
        $user = self::findByEmail($correo);
        if (!$user) return false;
        $stored = $user['password'] ?? '';
        if (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon2') === 0) {
            if (password_verify($password, $stored)) return $user;
            return false;
        }
        if ($stored === $password) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            try { self::updatePasswordHash($user['id_admin'], $newHash); } catch (Exception $e) { /* ignore */ }
            return $user;
        }
        return false;
    }
}
?>