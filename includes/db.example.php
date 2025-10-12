<?php
/**
 * Copia este archivo a includes/db.php y reemplaza los PLACEHOLDERS
 * o crea un archivo .env en la raíz del proyecto con las claves DB_*.
 */

// 1) Cargar variables desde .env si existe (opcional)
$envPath = dirname(__DIR__) . '/.env'; // .env en la raíz del repo
$env = is_readable($envPath) ? parse_ini_file($envPath, false, INI_SCANNER_TYPED) : [];

// 2) Placeholders (y fallback a .env si está)
$host    = $env['DB_HOST']    ?? '<DB_HOST>';      // p.ej. localhost
$port    = $env['DB_PORT']    ?? '<DB_PORT>';      // p.ej. 3306
$db      = $env['DB_NAME']    ?? '<DB_NAME>';      // p.ej. gestion_practicas
$user    = $env['DB_USER']    ?? '<DB_USER>';      // p.ej. root
$pass    = $env['DB_PASS']    ?? '<DB_PASS>';      // p.ej. (vacío)
$charset = $env['DB_CHARSET'] ?? 'utf8mb4';
$tz      = $env['DB_TZ']      ?? '-03:00';

// 3) Validar que no queden placeholders sin completar
$placeholders = [$host, $port, $db, $user]; // pass puede quedar vacío
foreach ($placeholders as $v) {
    if (preg_match('/^<.+>$/', (string)$v)) {
        die('Config BD: completa includes/db.php o crea .env con DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS');
    }
}

// 4) Conexión PDO
$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES {$charset} COLLATE utf8mb4_unicode_ci");
    $pdo->exec("SET time_zone = '{$tz}'");
} catch (PDOException $e) {
    die('Error de conexión a BD.');
}

