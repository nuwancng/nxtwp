<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'] ?? 'Guest';
    echo "Hello, " . htmlspecialchars($customer_name) . "!"; // Sanitize output to prevent XSS
} else {
    echo "Invalid request.";
}
