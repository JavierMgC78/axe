<?php
declare(strict_types=1);

class Auditoria {
    public static function registrar(int $usuario_id, string $evento, string $recurso, array $detalles = []): void {
        $pdo = require BASE_PATH . '/config/database.php';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $detalles_json = empty($detalles) ? null : json_encode($detalles, JSON_UNESCAPED_UNICODE);

        try {
            $sql = "INSERT INTO bitacora_auditoria (usuario_id, evento, recurso, detalles, ip_origen) 
                    VALUES (:usuario_id, :evento, :recurso, :detalles, :ip)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':evento'     => $evento,
                ':recurso'    => $recurso,
                ':detalles'   => $detalles_json,
                ':ip'         => $ip
            ]);
        } catch (PDOException $e) {
            error_log("Error crítico en Auditoria Axe: " . $e->getMessage());
        }
    }
}
