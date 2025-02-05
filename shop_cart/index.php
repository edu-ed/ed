<?php
$conn = new mysqli("localhost", "root", "", "shop_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="header">🛍️ Welcome to Our Store</div>

    <h2>🛒 Available Products</h2>

    <div class="product-grid">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="product-card">
                <h3><?= $row['name'] ?></h3>
                <p class="price">₱<?= number_format($row['price'], 2) ?></p>
                <a href="cart.php?action=add&id=<?= $row['id'] ?>" class="add-to-cart-btn">Add to Cart</a>
            </div>
        <?php } ?>
    </div>

    <div class="cart-link">
        <a href="cart.php">🛒 View Cart</a>
    </div>

    <div class="footer">© 2025 Your Shop | All Rights Reserved</div>
</div>

</body>
</html>

<?php $conn->close(); ?>
