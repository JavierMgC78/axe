<?php

declare(strict_types=1);

/**
 * Clase Seguridad — Calculadora Criptográfica
 *
 * Propósito exclusivo: operaciones criptográficas puras (generación de tokens,
 * hashing y verificación). NO contiene lógica de base de datos ni de negocio.
 *
 * ─── Split Token Pattern ───────────────────────────────────────────────────
 *  El token completo que viaja en la COOKIE tiene la forma:
 *      <selector>|<validador_claro>
 *
 *  Lo que se persiste en la BASE DE DATOS (tabla auth_tokens):
 *      • selector       → permite recuperar el registro sin escaneo completo.
 *      • validador_hash → hash SHA-256 del validador_claro; nunca el texto claro.
 *
 *  De este modo, un volcado de la BD no permite reutilizar tokens existentes,
 *  ya que el atacante obtiene el hash pero no el validador_claro requerido
 *  para que hash_equals() devuelva true.
 * ───────────────────────────────────────────────────────────────────────────
 */
class Seguridad
{
    // =========================================================================
    // CONSTANTES
    // =========================================================================

    /**
     * Separador interno del Split Token dentro del valor de la cookie.
     * Fuente única de verdad: usar Seguridad::TOKEN_SEPARATOR en todo el código.
     *
     * FIX M1: Centralizado para evitar inconsistencias entre LoginController,
     * LogoutController e index.php.
     */
    public const TOKEN_SEPARATOR = '|';

    /**
     * No se permiten instancias; todos los métodos son estáticos.
     */
    private function __construct() {}

    // =========================================================================
    // SPLIT TOKEN
    // =========================================================================

    /**
     * Genera los tres componentes del Split Token.
     *
     * @return array{
     *     selector:        string,   // 16 bytes → 32 chars hex  — se guarda en la BD
     *     validador_claro: string,   // 32 bytes → 64 chars hex  — viaja en la cookie
     *     validador_hash:  string    // SHA-256 del claro        — se guarda en la BD
     * }
     *
     * @throws \Random\RandomException Si el CSPRNG del sistema no puede generar bytes.
     */
    public static function generarSplitToken(): array
    {
        // Selector: identifica el registro en la BD (index único).
        // Se almacena en la BD y también en la cookie para la búsqueda.
        $selector = bin2hex(random_bytes(16));   // 32 chars hex

        // Validador en texto claro: únicamente viaja en la cookie del cliente.
        // NUNCA se persiste en la base de datos.
        $validador_claro = bin2hex(random_bytes(32));  // 64 chars hex

        // Hash del validador: es lo que se persiste en la BD.
        // Permite verificar sin exponer el valor real.
        $validador_hash = hash('sha256', $validador_claro);

        return [
            'selector'        => $selector,
            'validador_claro' => $validador_claro,
            'validador_hash'  => $validador_hash,
        ];
    }

    // =========================================================================
    // VERIFICACIÓN DE TOKEN
    // =========================================================================

    /**
     * Verifica que el validador enviado en la cookie corresponde al hash guardado en la BD.
     *
     * Usa hash_equals() para comparación en tiempo constante y evitar
     * ataques de temporización (timing attacks).
     *
     * @param  string $validador_conocido  Hash SHA-256 recuperado de la BD.
     * @param  string $validador_ingresado Texto claro proveniente de la cookie del cliente.
     * @return bool   true si el token es válido, false en caso contrario.
     */
    public static function verificarToken(string $validador_conocido, string $validador_ingresado): bool
    {
        return hash_equals(
            $validador_conocido,
            hash('sha256', $validador_ingresado)
        );
    }

    // =========================================================================
    // CONTRASEÑAS
    // =========================================================================

    /**
     * Genera el hash seguro de una contraseña en texto plano.
     *
     * Utiliza PASSWORD_DEFAULT para adoptar automáticamente el algoritmo
     * más robusto disponible en la versión actual de PHP (bcrypt por defecto,
     * Argon2 si está compilado).
     *
     * @param  string $password_plano Contraseña sin hashear.
     * @return string Hash listo para almacenar en la BD.
     */
    public static function hashearPassword(string $password_plano): string
    {
        return password_hash($password_plano, PASSWORD_DEFAULT);
    }

    /**
     * Verifica si una contraseña en texto plano coincide con su hash almacenado.
     *
     * password_verify() ya realiza la comparación en tiempo constante
     * e internamente gestiona el salt embebido en el hash.
     *
     * @param  string $password_plano Contraseña enviada en el formulario.
     * @param  string $hash_db        Hash recuperado de la BD.
     * @return bool   true si la contraseña es correcta, false si no.
     */
    public static function verificarPassword(string $password_plano, string $hash_db): bool
    {
        return password_verify($password_plano, $hash_db);
    }
}
