<?php 
	define('host', 'localhost');
    define('user', 'root');
    define('pass', '');
    define('database', 'rekrutacja');
    $db = mysqli_connect(host, user, pass, database);
    if (!$db) {
        exit("blad polaczenia z serwerem");
    }
?>