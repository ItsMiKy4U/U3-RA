<?php
   require_once __DIR__ . '/../src/app.php';

   try {
       $nombreBase = db()->query('SELECT DATABASE()')->fetchColumn();
       echo '<p>Conexión correcta. Base seleccionada: ' . h((string) $nombreBase) . '</p>';
   } catch (PDOException $e) {
       http_response_code(500);
       echo '<p>No se pudo conectar. Revisa el host, puerto, nombre de la base y usuario en tu configuración.</p>';
   }