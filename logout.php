<?php
session_start();
session_destroy();

setcookie("client", "", time() - 3600, "/");

header("Location: index.php");
exit();
?>
