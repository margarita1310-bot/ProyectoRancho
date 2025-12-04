<?php

// Incluir la clase de conexión a base de datos
require_once 'Conexion.php';

/**
 * Usuario
 * Clase encargada de gestionar la autenticación de administradores
 * Proporciona métodos para verificar credenciales y manejar contraseñas con hash seguro
 */
class Usuario
{
    /**
     * Busca un administrador por su correo electrónico
     * Realiza una búsqueda en la tabla administrador usando el correo como clave
     *
     * @param string $correo Correo electrónico del administrador a buscar
     * @return array|false Array asociativo con los datos del administrador o false si no existe
     */
    public static function findByEmail($correo)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta de búsqueda por correo
        $stmt = $db->prepare("SELECT * FROM administrador WHERE correo = ? LIMIT 1");

        // Ejecutar la consulta con el correo
        $stmt->execute([$correo]);

        // Retornar el administrador encontrado o false
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza la contraseña de un administrador con hash seguro
     * Reemplaza la contraseña anterior con un nuevo hash cifrado
     *
     * @param int $id ID del administrador cuya contraseña se actualizará
     * @param string $hash Hash seguro de la contraseña (generado con password_hash)
     * @return bool true si la actualización fue exitosa, false en caso contrario
     */
    public static function updatePasswordHash($id, $hash)
    {
        // Conectar a la base de datos
        $db = Conexion::conectar();

        // Preparar consulta de actualización de contraseña
        $stmt = $db->prepare("UPDATE administrador SET password = ? WHERE id_admin = ?");

        // Ejecutar la consulta y retornar resultado
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Verifica las credenciales de un administrador
     * Valida correo y contraseña, con soporte para contraseñas legadas sin hash
     * Actualiza automáticamente contraseñas antiguas a hash seguro si es necesario
     *
     * @param string $correo Correo electrónico del administrador
     * @param string $password Contraseña en texto plano
     * @return array|false Array con datos del administrador si la verificación es exitosa, false en caso contrario
     */
    public static function verificar($correo, $password)
    {
        // Buscar el administrador por correo
        $user = self::findByEmail($correo);

        // Si el usuario no existe, retornar false
        if (!$user) {
            return false;
        }

        // Obtener la contraseña almacenada
        $stored = $user['password'] ?? '';

        // Verificar si la contraseña está hashada con bcrypt o argon2
        if (strpos($stored, '$2y$') === 0 || strpos($stored, '$2a$') === 0 || strpos($stored, '$argon2') === 0) {
            // Si está hashada, verificar con password_verify
            if (password_verify($password, $stored)) {
                return $user;
            }
            return false;
        }

        // Si no está hashada, verificar comparación directa (contraseña legada)
        if ($stored === $password) {
            // Generar nuevo hash seguro
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            // Intentar actualizar la contraseña a hash seguro
            try {
                self::updatePasswordHash($user['id_admin'], $newHash);
            } catch (Exception $e) {
                // Ignorar excepciones en la actualización, el usuario puede seguir autenticándose
            }

            // Retornar datos del usuario
            return $user;
        }

        // Si no coincide ninguna verificación, retornar false
        return false;
    }
}

?>