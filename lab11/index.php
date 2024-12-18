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
                <li><a href="index.php?idp=glowna">Strona Główna</a></li>
                <li><a href="index.php?idp=history">Historia</a></li>
                <li><a href="index.php?idp=recommendation">Recomendacje</a></li>
                <li><a href="index.php?idp=films">Filmy Oskarowe</a></li>
                <li><a href="index.php?idp=news">Nowości</a></li>
                <li><a href="index.php?idp=criticism">Krytyka</a></li>
                <li><a href="index.php?idp=contact">Kontakt</a></li>
                <li><a href="index.php?idp=movies">Filmy</a></li>
            </ul>
        </nav>
    </header>

    

 
    
    <?php echo $strona; ?>

    <footer class="footer">
        <p>Wszelkie prawa zastrzeżone © 2024</p>
    </footer>

    <?php
     $nr_indeksu = '169407';  
     $nrGrupy = 'ISI4';            
     echo 'Autor: Alina Upyrova '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';
    ?>

    <script>
        $("#animacjaTestowa1").on("click", function() {
            $(this).animate({
                width: "500px",
                opacity: 0.4,
                fontSize: "3em",
                borderWidth: "10px"
            }, 1500);
        });
    </script>
</body>
</html>