<?php
/* Conexión de Banco. Usa ../comun/conexion.php (la misma de los demás apartados) y deja
   mysqli en el mismo modo que el resto del proyecto (los errores se devuelven, no se lanzan).
   Nada de aquí lanza errores: si algo falla, conectar() devuelve null o cuenta_llamar() devuelve ok = false. */

function bd_intentar(): array {
    static $r = null;                         // se intenta una sola vez por visita
    if ($r !== null) return $r;

    $archivo = dirname(__DIR__, 2) . '/comun/conexion.php';
    if (!is_file($archivo)) return $r = ['db' => null, 'motivo' => 'archivo'];

    $nivel = error_reporting();
    try {
        if (!defined('CONEXION_SILENCIOSA')) define('CONEXION_SILENCIOSA', true);
        $conexion = null;
        error_reporting(0);
        include $archivo;                     // define $conexion (o la deja en null si falla)
        error_reporting($nivel);

        if ($conexion instanceof mysqli) return $r = ['db' => $conexion, 'motivo' => 'ok'];

        $clave = getenv('DB_PASSWORD');
        if ($clave === false || $clave === '') return $r = ['db' => null, 'motivo' => 'clave'];
        return $r = ['db' => null, 'motivo' => 'servidor', 'errno' => mysqli_connect_errno()];
    } catch (Throwable $ex) {
        error_reporting($nivel);
        return $r = ['db' => null, 'motivo' => 'servidor', 'errno' => 0];
    }
}

function conectar(): ?mysqli {
    return bd_intentar()['db'];
}

// Explicación corta y segura (sin host, usuario ni contraseña).
function bd_motivo(): string {
    $r = bd_intentar();
    switch ($r['motivo']) {
        case 'ok':      return '';
        case 'archivo': return 'No se encuentra comun/conexion.php.';
        case 'clave':   return 'Falta la contraseña de la base de datos (variable DB_PASSWORD o archivo .env).';
        default:        return 'El servidor no respondió o rechazó la conexión (código ' . (int)($r['errno'] ?? 0) . ').';
    }
}

/* Llama a un procedimiento almacenado (CALL ... con ?).
   Devuelve ['ok' => bool, 'filas' => array, 'error' => string]. */
function cuenta_llamar(string $sql, array $params = []): array {
    $db = conectar();
    if (!$db) return ['ok' => false, 'filas' => [], 'error' => 'Sin conexión con la base de datos.'];
    try {
        $st = $db->prepare($sql);
        if (!$st) return ['ok' => false, 'filas' => [], 'error' => (string)$db->error];

        $params = array_values($params);
        if ($params) $st->bind_param(str_repeat('s', count($params)), ...$params);

        if (!$st->execute()) {
            $error = (string)$st->error;
            $st->close();
            while ($db->more_results() && $db->next_result());
            return ['ok' => false, 'filas' => [], 'error' => $error];
        }
        $res = $st->get_result();
        $filas = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        if ($res) $res->free();
        $st->close();
        while ($db->more_results() && $db->next_result());   // limpia lo que deja el CALL

        return ['ok' => true, 'filas' => $filas, 'error' => ''];
    } catch (Throwable $ex) {
        return ['ok' => false, 'filas' => [], 'error' => $ex->getMessage()];
    }
}

// ¿Existen las tablas y los procedimientos de Banco? Devuelve [bool, detalle].
function bd_revisar(): array {
    if (!conectar()) return [false, 'Sin conexión.'];
    $pruebas = [
        ['CALL sp_cuenta_buscar_cliente(?)', ['']],
        ['CALL sp_cuenta_listar_abonos(?)',  [0]],
        ['CALL sp_cuenta_consignar(?,?,?,?)', [0, '2000-01-01', 'x', 0]],   // monto 0: no inserta nada
        ['CALL sp_cuenta_retirar(?,?,?,?)',   [0, '2000-01-01', 'x', 0]],
    ];
    foreach ($pruebas as [$sql, $p]) {
        $r = cuenta_llamar($sql, $p);
        if (!$r['ok']) return [false, $r['error']];
    }
    return [true, ''];
}

// Crea las tablas y los procedimientos (se puede repetir sin perder datos).
function bd_preparar(): void {
    $db = conectar();
    if (!$db) throw new RuntimeException('Sin conexión con la base de datos.');
    foreach (require __DIR__ . '/esquema.php' as $sql) {
        if ($db->query($sql) === false) throw new RuntimeException((string)$db->error);
    }
}
