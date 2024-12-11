<?php

class Contact {
    private $adminEmail = "admin@przyklad.com"; 

    public function PokazKontakt() {
        echo "
            <h2>Skontaktuj się z nami</h2>
            <form method='post' action=''>
                <label for='email'>Twój email:</label><br>
                <input type='email' id='email' name='email' required><br><br>
    
                <label for='subject'>Temat:</label><br>
                <input type='text' id='subject' name='subject' required><br><br>
    
                <label for='message'>Wiadomość:</label><br>
                <textarea id='message' name='message' rows='5' required></textarea><br><br>
    
                <input type='submit' name='submit_contact' value='Wyślij wiadomość'>
            </form>
        ";
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
            $this->WyslijMailKontakt();
        }
    }

    public function WyslijMailKontakt() {
        if (empty($_POST['subject']) || empty($_POST['message']) || empty($_POST['email'])) {
            echo "Nie wypełniłeś wszystkich pól formularza!<br>";
            $this->PokazKontakt();
            return;
        }

        $mail['sender'] = $_POST['email'];
        $mail['subject'] = $_POST['subject'];
        $mail['body'] = $_POST['message'];
        $mail['recipient'] = "kontakt@przyklad.com"; 

        $header = "From: Formularz kontaktowy <" . $mail['sender'] . ">\n";
        $header .= "Content-Type: text/plain; charset=utf-8\n";
        $header .= "MIME-Version: 1.0\n";
        $header .= "X-Mailer: PHP/" . phpversion() . "\n";
        $header .= "Reply-To: " . $mail['sender'] . "\n";
        $header .= "Return-Path: " . $mail['sender'] . "\n";

        if (mail($mail['recipient'], $mail['subject'], $mail['body'], $header)) {
            echo "Wiadomość została wysłana!<br>";
        } else {
            echo "Wystąpił problem podczas wysyłania wiadomości.<br>";
        }
    }

    public function PrzypomnijHaslo() {
        if (empty($_POST['email'])) {
            echo "Proszę podać adres email!<br>";
            return;
        }

        $adresEmail = $_POST['email'];
        $noweHaslo = $this->generujNoweHaslo();

        $tematUzytkownik = "Przypomnienie hasła";
        $wiadomoscUzytkownik = "Twoje nowe hasło to: $noweHaslo";

        $tematAdmin = "Nowe hasło wygenerowane dla użytkownika";
        $wiadomoscAdmin = "Dla użytkownika o adresie email $adresEmail zostało wygenerowane nowe hasło: $noweHaslo";

        $this->wyslijBezposrednio($adresEmail, $tematUzytkownik, $wiadomoscUzytkownik);
        $this->wyslijBezposrednio($this->adminEmail, $tematAdmin, $wiadomoscAdmin);

        echo "Nowe hasło zostało wysłane!<br>";
    }

    private function wyslijBezposrednio($recipient, $subject, $message) {
        $header = "From: kontakt@przyklad.com\n";
        $header .= "Content-Type: text/plain; charset=utf-8\n";
        $header .= "MIME-Version: 1.0\n";

        mail($recipient, $subject, $message, $header);
    }

    private function generujNoweHaslo() {
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
    }
}

$kontakt = new Contact();
$kontakt->PokazKontakt();
?>
