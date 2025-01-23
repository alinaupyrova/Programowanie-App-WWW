<?php
session_start();
require_once '../cfg.php';
require_once 'functions.php';

$action = $_GET['action'] ?? null;

if ($action === 'add') {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    addToCart($productId, $quantity, $link);
} elseif ($action === 'remove') {
    $productId = (int)$_GET['product_id'];
    removeFromCart($productId);
} elseif ($action === 'update') {
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    updateCartQuantity($productId, $quantity);
}

$cartProducts = getCartProducts();
$totalPrice = calculateTotalPrice($cartProducts);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
    <link rel="stylesheet" href="/css/cart.css">
</head>
<body>
<div class="container">
    <header>
        <h1>Koszyk</h1>
        <a href="index.php" class="back-to-shop">Powrót do sklepu</a>
    </header>
    <main>
        <?php if (!empty($cartProducts)): ?>
            <table class="cart-table">
                <thead>
                <tr>
                    <th>Produkt</th>
                    <th>Cena</th>
                    <th>Ilość</th>
                    <th>Wartość</th>
                    <th>Akcje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cartProducts as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars($product['title']); ?></td>
                        <td><?= $product['price']; ?> zł</td>
                        <td>
                            <form method="post" action="cart.php?action=update">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <input type="number" name="quantity" value="<?= $product['quantity']; ?>" min="1" max="<?= $product['stock']; ?>" class="quantity-input">
                                <button type="submit" class="update-btn">Zaktualizuj</button>
                            </form>
                        </td>
                        <td><?= number_format($product['price'] * $product['quantity'], 2); ?> zł</td>
                        <td><a href="cart.php?action=remove&product_id=<?= $product['id']; ?>" class="remove-btn">Usuń</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p class="total-price">Łączna wartość: <?= $totalPrice; ?> zł</p>
        <?php else: ?>
            <p>Koszyk jest pusty.</p>
        <?php endif; ?>
    </main>
</div>
</body>
</html>

