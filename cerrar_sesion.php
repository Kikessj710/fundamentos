<?php
session_start();
session_unset();
session_destroy();

// Impedir que se almacene en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

header("Location: index.html");
exit();
?>
    