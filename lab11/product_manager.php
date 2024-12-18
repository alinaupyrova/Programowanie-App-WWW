<?php
class ProductManager {
    private $db;
    public function FormularzDodawaniaProduktu() {
        return '
        <div class="dodawanie-produktu">
            <h1>Dodaj Nowy Produkt</h1>
            <form method="post" action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '" enctype="multipart/form-data">
                <table>
                    <tr><td>Tytuł produktu:</td><td><input type="text" name="produkt_tytul" required /></td></tr>
                    <tr><td>Opis produktu:</td><td><textarea name="produkt_opis" required></textarea></td></tr>
                    <tr><td>Cena netto:</td><td><input type="number" name="produkt_cena" step="0.01" required /></td></tr>
                    <tr><td>Podatek VAT (%):</td><td><input type="number" name="produkt_vat" step="0.01" required /></td></tr>
                    <tr><td>Ilość dostępnych sztuk:</td><td><input type="number" name="produkt_ilosc" required /></td></tr>
                    <tr><td>Status dostępności:</td>
                        <td>
                            <select name="produkt_status" required>
                                <option value="dostepny">Dostępny</option>
                                <option value="niedostepny">Niedostępny</option>
                                <option value="wycofany">Wycofany</option>
                            </select>
                        </td>
                    </tr>
                    <tr><td>Kategoria:</td><td><input type="text" name="produkt_kategoria" required /></td></tr>
                    <tr><td>Gabaryt produktu:</td><td><input type="text" name="produkt_gabaryt" required /></td></tr>
                    <tr><td>Data wygaśnięcia:</td><td><input type="date" name="produkt_wygasniecie" required /></td></tr>
                    <tr><td>Zdjęcie produktu:</td><td><input type="file" name="produkt_zdjecie" accept="image/*" /></td></tr>
                    <tr><td colspan="2"><input type="submit" name="produkt_submit" value="Dodaj Produkt" /></td></tr>
                </table>
            </form>
        </div>';
    }
    
    // Konstruktor klasy - połączenie z bazą danych
    public function __construct($db) {
        $this->db = $db;
    }

    // Dodawanie nowego produktu
    public function DodajProdukt($tytul, $opis, $cena_netto, $podatek_vat, $ilosc_dostepnych_sztuk, $status_dostepnosci, $kategoria, $gabaryt_produktu, $data_wygasniecia, $zdjecie_url) {
        $query = "INSERT INTO product (tytul, opis, cena_netto, podatek_vat, ilosc_dostepnych_sztuk, status_dostepnosci, kategoria, gabaryt_produktu, data_wygasniecia, zdjecie_url)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssdiisssss', $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_dostepnych_sztuk, $status_dostepnosci, $kategoria, $gabaryt_produktu, $data_wygasniecia, $zdjecie_url);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Usuwanie produktu po ID
    public function UsunProdukt($id) {
        $query = "DELETE FROM product WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Edytowanie danych produktu
    public function EdytujProdukt($id, $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_dostepnych_sztuk, $status_dostepnosci, $kategoria, $gabaryt_produktu, $data_wygasniecia, $zdjecie_url) {
        $query = "UPDATE product SET tytul = ?, opis = ?, cena_netto = ?, podatek_vat = ?, ilosc_dostepnych_sztuk = ?, status_dostepnosci = ?, kategoria = ?, gabaryt_produktu = ?, data_wygasniecia = ?, zdjecie_url = ?
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('ssdiisssssi', $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_dostepnych_sztuk, $status_dostepnosci, $kategoria, $gabaryt_produktu, $data_wygasniecia, $zdjecie_url, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Wyświetlanie wszystkich produktów
    public function PokazProdukty() {
        $query = "SELECT * FROM product";
        $result = $this->db->query($query);
        $produkty = [];
        while ($row = $result->fetch_assoc()) {
            $produkty[] = $row;
        }
        return $produkty;
    }

    // Sprawdzanie dostępności produktu
    public function SprawdzDostepnosc($produkt) {
        $currentDate = new DateTime();

        // Produkt wycofany
        if ($produkt['status_dostepnosci'] == 'wycofany') {
            return 'Produkt wycofany z oferty.';
        }

        // Brak dostępnych sztuk
        if ($produkt['ilosc_dostepnych_sztuk'] <= 0) {
            return 'Produkt niedostępny (brak na stanie).';
        }

        // Sprawdzanie daty wygaśnięcia
        $expirationDate = new DateTime($produkt['data_wygasniecia']);
        if ($expirationDate < $currentDate) {
            return 'Produkt wygasł (data ważności minęła).';
        }

        return 'Produkt dostępny.';
    }

    // Pobieranie produktu po ID
    public function PobierzProduktPoId($id) {
        $query = "SELECT * FROM product WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
