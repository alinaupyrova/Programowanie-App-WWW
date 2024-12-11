<?php
session_start();
require_once 'cfg.php'; 
require_once 'db_connection.php';

function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1>Panel CMS</h1>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <table>
                <tr><td>Email:</td><td><input type="text" name="login_email" /></td></tr>
                <tr><td>Hasło:</td><td><input type="password" name="login_pass" /></td></tr>
                <tr><td colspan="2"><input type="submit" name="x1_submit" value="Zaloguj" /></td></tr>
            </table>
        </form>
    </div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x1_submit'])) {
    $login_email = $_POST['login_email'] ?? '';
    $login_pass = $_POST['login_pass'] ?? '';

    if ($login_email === $login && $login_pass === $pass) {
        $_SESSION['logged_in'] = true; 
        echo '<p style="color:green;">Logowanie zakończone sukcesem!</p>';
    } else {
        echo '<p style="color:red;">Błąd: Nieprawidłowy login lub hasło.</p>';
        echo FormularzLogowania();
        exit;
    }
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    ListaPodstron($link); 
} else {
    echo FormularzLogowania(); 
}
/** Funkcja wyświetlająca listę podstron w bazie danych. */
function ListaPodstron($link) {
    $result = $link->query("SELECT id, tytul FROM podstrony");

    if ($result->num_rows > 0) {
        echo '<table><tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr><td>' . $row['id'] . '</td><td>' . $row['tytul'] . '</td>
                  <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="edytuj">Edytuj</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="usun" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\');">Usuń</button>
                    </form>
                  </td></tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Brak podstron w bazie danych.</p>';
    }
}

/** Funkcja umożliwiająca edycję podstrony   */

function EdytujPodstrone($link, $id) {
    $stmt = $link->prepare("SELECT tytul, tresc, aktywna FROM podstrony WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        echo '
        <h2>Edytuj podstronę</h2>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <input type="hidden" name="id" value="' . htmlspecialchars($id) . '" />
            <table>
                <tr><td>Tytuł:</td><td><input type="text" name="tytul" value="' . htmlspecialchars($row['tytul']) . '" required /></td></tr>
                <tr><td>Treść:</td><td><textarea name="tresc" rows="10" required>' . htmlspecialchars($row['tresc']) . '</textarea></td></tr>
                <tr><td>Aktywna:</td><td><input type="checkbox" name="aktywna" ' . ($row['aktywna'] ? 'checked' : '') . ' /></td></tr>
                <tr><td colspan="2"><input type="submit" name="zapisz" value="Zapisz zmiany" /></td></tr>
            </table>
        </form>';
    } else {
        echo '<p>Nie znaleziono podstrony o podanym ID.</p>';
    }
    $stmt->close();
}

/** Funkcja umożliwiająca dodawanie nowej podstrony  */ 
function DodajNowaPodstrone($link) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
        $stmt = $link->prepare("INSERT INTO podstrony (tytul, tresc, aktywna) VALUES (?, ?, ?)");
        $stmt->bind_param('ssi', $_POST['tytul'], $_POST['tresc'], isset($_POST['aktywna']) ? 1 : 0);

        if ($stmt->execute()) {
            echo '<p style="color:green;">Nowa podstrona została dodana.</p>';
        } else {
            echo '<p style="color:red;">Błąd: ' . $stmt->error . '</p>';
        }

        $stmt->close();
    }
    echo '
    <h2>Dodaj nową podstronę</h2>
    <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
        <table>
            <tr><td>Tytuł:</td><td><input type="text" name="tytul" required /></td></tr>
            <tr><td>Treść:</td><td><textarea name="tresc" rows="10" required></textarea></td></tr>
            <tr><td>Aktywna:</td><td><input type="checkbox" name="aktywna" /></td></tr>
            <tr><td colspan="2"><input type="submit" name="dodaj" value="Dodaj podstronę" /></td></tr>
        </table>
    </form>';
}

/** Funkcja umożliwiająca usunięcie podstrony  */ 

function UsunPodstrone($link) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usun']) && isset($_POST['id'])) {
        $stmt = $link->prepare("DELETE FROM podstrony WHERE id = ?");
        $stmt->bind_param('i', $_POST['id']);

        if ($stmt->execute()) {
            echo '<p style="color:green;">Podstrona została usunięta.</p>';
        } else {
            echo '<p style="color:red;">Błąd: ' . $stmt->error . '</p>';
        }

        $stmt->close();
    }
    echo '<p>Wybierz podstronę do usunięcia.</p>';
}
?>
