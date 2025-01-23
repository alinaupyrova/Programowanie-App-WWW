<?php
// Додавання продукту до кошика
function addToCart($productId, $quantity, $link) {
    $stmt = $link->prepare("SELECT id, title, net_price, vat_rate, stock FROM products WHERE id = ?");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product || $quantity > $product['stock']) {
        return false; // Якщо продукт не знайдено або кількість перевищує наявну
    }

    $price = $product['net_price'] + $product['net_price'] * $product['vat_rate'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'title' => $product['title'],
            'price' => $price,
            'quantity' => $quantity,
            'stock' => $product['stock']
        ];
    }
}

// Видалення продукту з кошика
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

// Оновлення кількості продукту в кошику
function updateCartQuantity($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId])) {
        $quantity = min($quantity, $_SESSION['cart'][$productId]['stock']);
        if ($quantity > 0) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$productId]);
        }
    }
}

// Отримання всіх продуктів із кошика
function getCartProducts() {
    return $_SESSION['cart'] ?? [];
}

// Підрахунок загальної вартості продуктів у кошику
function calculateTotalPrice($cartProducts) {
    $total = 0;
    foreach ($cartProducts as $product) {
        $total += $product['price'] * $product['quantity'];
    }
    return number_format($total, 2);
}
