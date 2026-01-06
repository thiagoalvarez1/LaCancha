<?php
require_once __DIR__ . '/../laravel/vendor/autoload.php';
$app = require_once __DIR__ . '/../laravel/bootstrap/app.php';

// Generar nueva key
echo "Generando APP_KEY...<br>";
$key = 'base64:' . base64_encode(random_bytes(32));

// Actualizar .env
$envPath = __DIR__ . '/../laravel/.env';
$envContent = file_get_contents($envPath);
$envContent = preg_replace('/APP_KEY=.*/', "APP_KEY=$key", $envContent);
file_put_contents($envPath, $envContent);

echo "✅ Nueva APP_KEY: $key<br>";
echo "✅ Archivo .env actualizado<br>";
echo "<a href='/'>Probar sitio</a>";
?>