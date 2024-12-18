<?php
session_start();
require_once 'cfg.php';
require_once 'product_manager.php';

/** Функция для отображения формы логина */
function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1>Panel CMS</h1>
        <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '">
            <table>
                <tr><td>Email:</td><td><input type="text" name="login_email" required /></td></tr>
                <tr><td>Hasło:</td><td><input type="password" name="login_pass" required /></td></tr>
                <tr><td colspan="2"><input type="submit" name="x1_submit" value="Zaloguj" /></td></tr>
            </table>
        </form>
    </div>';
}

/** Проверка логина */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x1_submit'])) {
    $login_email = $_POST['login_email'] ?? '';
    $login_pass = $_POST['login_pass'] ?? '';

    if ($login_email === $login && $login_pass === $pass) { // $login и $pass из cfg.php
        $_SESSION['logged_in'] = true;
        echo '<p style="color:green;">Logowanie zakończone sukcesem!</p>';
    } else {
        echo '<p style="color:red;">Błąd: Nieprawidłowy login lub hasło.</p>';
        echo FormularzLogowania();
        exit;
    }
}

/** Главный блок управления CMS */
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    echo '<h1>Panel CMS</h1>';
    echo '<a href="?dodaj=true">Dodaj nową podstronę</a><br/><br/>';

    // Обработка действий
    if (isset($_POST['edytuj']) && isset($_POST['id'])) {
        EdytujPodstrone($link, $_POST['id']);
    } elseif (isset($_POST['usun']) && isset($_POST['id'])) {
        UsunPodstrone($link);
        ListaPodstron($link);
    } elseif (isset($_GET['dodaj'])) {
        DodajNowaPodstrone($link);
    } elseif (isset($_POST['zapisz']) && isset($_POST['id'])) {
        ZapiszPodstrone($link, $_POST);
        ListaPodstron($link);
    } else {
        ListaPodstron($link);
    }
} else {
    echo FormularzLogowania();
}

/** Функция для отображения списка подстраниц */
function ListaPodstron($link) {
    $result = $link->query("SELECT id, page_title FROM page_list");

    if ($result->num_rows > 0) {
        echo '<table border="1"><tr><th>ID</th><th>Tytuł</th><th>Akcje</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . htmlspecialchars($row['page_title']) . '</td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="edytuj">Edytuj</button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <button type="submit" name="usun" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\');">Usuń</button>
                    </form>
                </td>
            </tr>';
        }
        echo '</table>';
    } else {
        echo '<p>Brak podstron w bazie danych.</p>';
    }
}

/** Функция для редактирования подстраницы */
function EdytujPodstrone($link, $id) {
    $stmt = $link->prepare("SELECT page_title, page_content, status FROM page_list WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        echo '
        <h2>Edytuj podstronę</h2>
        <form method="post" action="">
            <input type="hidden" name="id" value="' . htmlspecialchars($id) . '" />
            <table>
                <tr><td>Tytuł:</td><td><input type="text" name="page_title" value="' . htmlspecialchars($row['page_title']) . '" required /></td></tr>
                <tr><td>Treść:</td><td><textarea name="page_content" rows="10" required>' . htmlspecialchars($row['page_content']) . '</textarea></td></tr>
                <tr><td>Aktywna:</td><td><input type="checkbox" name="status" ' . ($row['status'] == 1 ? 'checked' : '') . ' /></td></tr>
                <tr><td colspan="2"><input type="submit" name="zapisz" value="Zapisz zmiany" /></td></tr>
            </table>
        </form>';
    }
    $stmt->close();
}

/** Сохранение изменений в подстранице */
function ZapiszPodstrone($link, $data) {
    $stmt = $link->prepare("UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ?");
    $status = isset($data['status']) ? 1 : 0;
    $stmt->bind_param('ssii', $data['page_title'], $data['page_content'], $status, $data['id']);

    if ($stmt->execute()) {
        echo '<p style="color:green;">Zmiany zostały zapisane.</p>';
    } else {
        echo '<p style="color:red;">Błąd: ' . $stmt->error . '</p>';
    }
    $stmt->close();
}

/** Функция для добавления новой подстраницы */
function DodajNowaPodstrone($link) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj'])) {
        $status = isset($_POST['status']) ? 1 : 0;
        $stmt = $link->prepare("INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)");

        $stmt->bind_param('ssi', $_POST['page_title'], $_POST['page_content'], $status);

        if ($stmt->execute()) {
            echo '<p style="color:green;">Nowa podstrona została dodana.</p>';
        } else {
            echo '<p style="color:red;">Błąd: ' . $stmt->error . '</p>';
        }
        $stmt->close();
    }

    echo '
    <h2>Dodaj nową podstronę</h2>
    <form method="post" action="">
        <table>
            <tr><td>Tytuł:</td><td><input type="text" name="page_title" required /></td></tr>
            <tr><td>Treść:</td><td><textarea name="page_content" rows="10" required></textarea></td></tr>
            <tr><td>Aktywna:</td><td><input type="checkbox" name="status" /></td></tr>
            <tr><td colspan="2"><input type="submit" name="dodaj" value="Dodaj podstronę" /></td></tr>
        </table>
    </form>';
}

/** Функция для удаления подстраницы */
function UsunPodstrone($link) {
    $stmt = $link->prepare("DELETE FROM page_list WHERE id = ?");
    $stmt->bind_param('i', $_POST['id']);

    if ($stmt->execute()) {
        echo '<p style="color:green;">Podstrona została usunięta.</p>';
    } else {
        echo '<p style="color:red;">Błąd: ' . $stmt->error . '</p>';
    }
    $stmt->close();
}
?>
