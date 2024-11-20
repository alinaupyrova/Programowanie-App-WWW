<?php 
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = ''; 
    $dbaza = 'moja_strona';

    $link = new mysqli($dbhost, $dbuser, $dbpass, $dbaza);

    if ($link->connect_error) {
        die("<b>Przerwane połączenie: </b>" . $link->connect_error);
    }
?>
