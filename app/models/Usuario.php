<?php
 /*
 * Usuario.php
 * 
 * Modelo para gestionar administradores.
 * Busca usuarios, verifica credenciales y actualiza contraseñas con soporte para migración de hashes.
 * 
 * Tabla: administrador (id_admin, nombre, correo, password)
 * 
 * Métodos:
 * - findByEmail($correo): Busca administrador por correo
 * - verificar($correo, $password): Verifica credenciales (con migración automática de hashes)
 * - updatePasswordHash($id, $hash): Actualiza hash de contraseña en BD
 */

require_once 'Conexion.php';

class Usuario {
     /*
     * findByEmail($correo)
     * Busca un administrador por dirección de correo.
     * @param string $correo - Email del administrador
     * @return array|false - Registro del administrador o false si no existe
     */
    public static function findByEmail($correo) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("SELECT * FROM administrador WHERE correo = ? LIMIT 1");
        $stmt->execute([$correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

     /*
     * updatePasswordHash($id, $hash)
     * Actualiza el hash de la contraseña de un administrador.
     * Se usa cuando se migra de contraseña en texto plano a bcrypt.
     * @param int $id - ID del administrador
     * @param string $hash - Nuevo hash bcrypt
     * @return bool - true si se actualizó, false si hubo error
     */
    public static function updatePasswordHash($id, $hash) {
        $db = Conexion::conectar();
        $stmt = $db->prepare("UPDATE administrador SET password = ? WHERE id_admin = ?");
        return $stmt->execute([$hash, $id]);
    }

     /*
     * verificar($correo, $password)
     * Verifica credenciales de un administrador.
     * @param string $correo - Email del administrador
     * @param string $password - Contraseña en texto plano
     * @return array|false - Registro del usuario si credenciales válidas, false en caso contrario
     */
    public static function verificar($correo, $password) {
        $user = self::findByEmail($correo);
        if (!$user) return false;

        $stored = $user['password'] ?? '';
        // Si el password almacenado parece ser un hash (empieza con $2y$ o $argon2), usar password_verify
        if (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon2') === 0) {
            if (password_verify($password, $stored)) return $user;
            return false;
        }

        // Si no es un hash, comparar en texto plano. Si coincide, re-hashear y actualizar BD.
        if ($stored === $password) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            // intentar actualizar el hash en la base de datos; no detenemos el login si falla
            try { self::updatePasswordHash($user['id_admin'], $newHash); } catch (Exception $e) { /* ignore */ }
            // devolver usuario
            return $user;
        }
        return false;
    }
}
?>