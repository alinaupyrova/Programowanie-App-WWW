<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include('cfg.php'); 
include('showpage.php'); 

$idp = isset($_GET['idp']) ? $_GET['idp'] : 'glowna';

$page_ids = [
    'glowna' => 1,
    'history' => 2,
    'background' => 3,
    'recommendation' => 4,
    'films' => 5,
    'news' => 6,
    'criticism' => 7,
    'contact' => 8,
    'movies' => 9
];

$page_id = isset($page_ids[$idp]) ? $page_ids[$idp] : $page_ids['glowna'];

$strona = PokazPodstrone($page_id);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="FilmWeb Team">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Discover award-winning films and read critiques, recommendations, and history of the Oscars.">
    <link rel="stylesheet" href="css/menu.css">
    <title>Filmy Oskarowe</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        if (typeof jQuery === 'undefined') {
            document.write('<script src="path/to/local/jquery.min.js"><\/script>');
        }
    </script>
</head>

<body>
    <header>
        <nav>
            <ul class="menu">
                <li><a href="index.php?idp=glowna">Str
