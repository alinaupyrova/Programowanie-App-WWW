<?php
session_start();
require_once '../cfg.php';
require_once 'functions.php';

// Обробка додавання товару до кошика
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    addToCart($productId, $quantity, $link);
}

// Запит для отримання продуктів із категоріями
$query = "
    SELECT 
        p.id, 
        p.title, 
        p.description, 
        p.net_price, 
        p.vat_rate, 
        p.stock, 
        p.dimensions, 
        p.image_url, 
        c.nazwa AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.availability_status = TRUE
    ORDER BY p.created_at DESC
";

$result = $link->query($query);
$products = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sklep internetowy</title>
    <link rel="stylesheet" href="/css/shop.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Sklep internetowy</h1>
        <a href="cart.php" class="cart-button">
            <img src="/img/shopping-cart.gif" alt="Koszyk">
            <span class="cart-count"><?= count($_SESSION['cart'] ?? []); ?></span>
        </a>
    </header>
    <main>
        <div class="product-list">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product">
                        <div class="product-image">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?= htmlspecialchars($product['image_url']); ?>" alt="<?= htmlspecialchars($product['title']); ?>">
                            <?php else: ?>
                                <img src="/img/oskar.jpg" alt="Brak zdjęcia">
                            <?php endif; ?>
                        </div>
                        <div class="product-details">
                            <h2><?= htmlspecialchars($product['title']); ?></h2>
                            <p><strong>Kategoria:</strong> <?= htmlspecialchars($product['category_name']); ?></p>
                            <p><strong>Opis:</strong> <?= htmlspecialchars($product['description']); ?></p>
                            <p><strong>Wymiary:</strong> <?= htmlspecialchars($product['dimensions']); ?></p>
                            <p class="price"><strong>Cena brutto:</strong> <?= number_format($product['net_price'] + ($product['net_price'] * $product['vat_rate']), 2); ?> zł</p>
                            <p><strong>Stan magazynu:</strong> <?= $product['stock']; ?></p>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <label for="quantity_<?= $product['id']; ?>">Ilość:</label>
                                <input type="number" id="quantity_<?= $product['id']; ?>" name="quantity" value="1" min="1" max="<?= $product['stock']; ?>">
                                <button type="submit" class="add-to-cart-btn">Dodaj do koszyka</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Brak dostępnych produktów.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>

