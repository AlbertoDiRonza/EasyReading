<?php
session_start();
session_unset();
session_destroy();

// Reindirizzamento alla pagina di login
header("Location: login.html");
exit();
?>
