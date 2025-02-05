<?php
$conn = new mysqli("localhost", "root", "", "shop_cart");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle cart actions
if (isset($_GET['action'])) {
    $product_id = $_GET['id'] ?? 0;
    
    // Add item to the cart
    if ($_GET['action'] == 'add') {
        $stmt = $conn->prepare("INSERT INTO cart (product_id, quantity) 
                                VALUES (?, 1)
                                ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    }
    
    // Remove item from the cart
    elseif ($_GET['action'] == 'remove') {
        // Ensure the ID exists in the cart before deleting it
        $stmt = $conn->prepare("DELETE FROM cart WHERE product_id = ?");  // Delete the specific product from cart
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
    }
    
    // Update item quantity in the cart
    elseif ($_GET['action'] == 'update' && isset($_POST['quantity'])) {
        $quantity = $_POST['quantity'];
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();
    }
}

// Display Cart
$result = $conn->query("SELECT cart.id, products.name, products.price, cart.quantity, products.id AS product_id
                        FROM cart 
                        JOIN products ON cart.product_id = products.id");

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
    <div class="header">🛒 Shopping Cart</div>
    
    <table>
        <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>

        <?php
        $total = 0;
        while ($row = $result->fetch_assoc()) {
            $subTotal = $row['price'] * $row['quantity'];
            $total += $subTotal;
        ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td>₱<?= number_format($row['price'], 2) ?></td>
                <td>
                    <form method="post" action="cart.php?action=update&id=<?= $row['product_id'] ?>">
                        <input type="number" name="quantity" value="<?= $row['quantity'] ?>" min="1">
                        <button type="submit">Update</button>
                    </form>
                </td>
                <td>₱<?= number_format($subTotal, 2) ?></td>
                <td>
                    <!-- Ensure product_id is valid and remove only the selected item -->
                    <a href="cart.php?action=remove&id=<?= $row['product_id'] ?>" class="remove-btn">Remove</a>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td><strong>₱<?= number_format($total, 2) ?></strong></td>
            <td></td>
        </tr>
    </table>

    <div class="cart-actions">
        <a href="index.php" class="continue-btn">⬅ Continue Shopping</a>
    </div>

    <div class="footer">© 2025 Edward Shop | All Rights Reserved</div>
</div>

</body>
</html>

<?php $conn->close(); ?>
