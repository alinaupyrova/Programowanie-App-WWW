<?php

    $nr_indeksu = '169407';
    $nrGrupy = 'isi4';

    echo 'Jan Kowalski '.$nr_indeksu.' grupa '.$nrGrupy.' <br /><br />';


    echo "a) Metoda include() i require_once(): <br />";
    

    echo "Zastosowanie include() i require_once() pozwala dołączyć plik PHP. <br /><br />";


    echo "b) Warunki if, else, elseif, switch: <br />";
    
    $ocena = 4;
    if ($ocena == 5) {
        echo "Ocena: bardzo dobry <br />";
    } elseif ($ocena == 4) {
        echo "Ocena: dobry <br />";
    } elseif ($ocena == 3) {
        echo "Ocena: dostateczny <br />";
    } else {
        echo "Ocena: niedostateczny <br />";
    }

    $dzień = 3;
    switch ($dzień) {
        case 1:
            echo "Poniedziałek <br />";
            break;
        case 2:
            echo "Wtorek <br />";
            break;
        case 3:
            echo "Środa <br />";
            break;
        default:
            echo "Nieznany dzień <br />";
    }
    echo "<br />";

    echo "c) Pętla while() i for(): <br />";

    $i = 1;
    while ($i <= 5) {
        echo "Pętla while - Iteracja: $i <br />";
        $i++;
    }


    for ($j = 1; $j <= 5; $j++) {
        echo "Pętla for - Iteracja: $j <br />";
    }
    echo "<br />";


    echo "d) Typy zmiennych \$_GET, \$_POST, \$_SESSION: <br />";
    
    echo "Przykład użycia \$_GET: <br />";
    if (isset($_GET['param'])) {
        echo "Parametr GET: ".$_GET['param']."<br />";
    } else {
        echo "Brak parametru GET <br />";
    }

    echo "Przykład użycia \$_POST: <br />";
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nazwa'])) {
        echo "Wysłano przez POST: ".$_POST['nazwa']."<br />";
    } else {
        echo "Brak danych POST <br />";
    }

    session_start();
    $_SESSION['sesja'] = "Przykładowa wartość sesji";
    echo "Wartość sesji: ".$_SESSION['sesja']."<br />";

?>
 