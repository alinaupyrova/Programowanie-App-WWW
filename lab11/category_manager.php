<?php

class ZarzadzajKategoriami {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }

    // Dodaj nową kategorię
    public function DodajKategorie($nazwa, $matka = 0) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO category_manager (name, parent_id) VALUES (:name, :parent_id)");
            $stmt->execute(['name' => $nazwa, 'parent_id' => $matka]);
            echo "Kategoria została dodana.\n";
        } catch (PDOException $e) {
            echo "Błąd podczas dodawania kategorii: " . $e->getMessage() . "\n";
        }
    }

    // Edytuj istniejącą kategorię
    public function EdytujKategorie($id, $nazwa, $matka = 0) {
        try {
            $stmt = $this->pdo->prepare("UPDATE category_manager SET name = :name, parent_id = :parent_id WHERE id = :id");
            $stmt->execute(['name' => $nazwa, 'parent_id' => $matka, 'id' => $id]);
            echo "Kategoria została zaktualizowana.\n";
        } catch (PDOException $e) {
            echo "Błąd podczas edytowania kategorii: " . $e->getMessage() . "\n";
        }
    }

    // Usuń kategorię (wraz z podkategoriami)
    public function UsunKategorie($id) {
        try {
            $this->pdo->beginTransaction();

            // Usuń podkategorie
            $stmt = $this->pdo->prepare("DELETE FROM category_manager WHERE parent_id = :id");
            $stmt->execute(['id' => $id]);

            // Usuń główną kategorię
            $stmt = $this->pdo->prepare("DELETE FROM category_manager WHERE id = :id");
            $stmt->execute(['id' => $id]);

            $this->pdo->commit();
            echo "Kategoria oraz jej podkategorie zostały usunięte.\n";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            echo "Błąd podczas usuwania kategorii: " . $e->getMessage() . "\n";
        }
    }

    // Wyświetl drzewo kategorii
    public function PokazKategorie() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM category_manager ORDER BY parent_id, id");
            $kategorie = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->GenerujDrzewo($kategorie);
        } catch (PDOException $e) {
            echo "Błąd podczas pobierania kategorii: " . $e->getMessage() . "\n";
        }
    }

    // Funkcja rekurencyjna do generowania drzewa kategorii
    private function GenerujDrzewo($kategorie, $matka = 0, $poziom = 0) {
        foreach ($kategorie as $kategoria) {
            if ($kategoria['parent_id'] == $matka) {
                echo str_repeat("--", $poziom) . $kategoria['name'] . " (ID: " . $kategoria['id'] . ")\n";
                $this->GenerujDrzewo($kategorie, $kategoria['id'], $poziom + 1);
            }
        }
    }
}

// Przykładowe użycie
$zarzadzanie = new ZarzadzajKategoriami('localhost', 'your_database', 'root', 'password');

// Dodawanie kategorii
$zarzadzanie->DodajKategorie('Elektronika');
$zarzadzanie->DodajKategorie('Komputery', 1); // Podkategoria "Komputery" dla kategorii "Elektronika"

// Edytowanie kategorii
$zarzadzanie->EdytujKategorie(2, 'Laptopy', 1);

// Usuwanie kategorii
// $zarzadzanie->UsunKategorie(2);

// Wyświetlanie drzewa kategorii
$zarzadzanie->PokazKategorie();
?>
