<?php
$file = 'compteur.txt';

if (!isset($_COOKIE['visited'])) {
    $visits = file_exists($file) ? (int)file_get_contents($file) : 0;
    $visits++;
    file_put_contents($file, $visits);

    setcookie('visited', 'yes', time() + 3600);
}



echo "Nombre de visites : ", file_get_contents($file);
?>