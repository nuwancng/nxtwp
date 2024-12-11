<?php
session_start();

// Database configuration
$host = 'localhost';
$db = 'nextwp';    // Main database for logging installations
$user = 'root';
$pass = '';
$wp_path = './wordpress'; // Path to the base WordPress files

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $customer_name = htmlspecialchars($_POST['customer_name'] ?? 'Guest');
    $email_address = htmlspecialchars($_POST['email'] ?? 'user@example.com');

    if (empty($customer_name) || empty($email_address)) {
        die("Customer name and email address are required!");
    }

    if (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address!");
    }

    // Connect to the database
    $mysqli = new mysqli($host, $user, $pass, $db);
    if ($mysqli->connect_error) {
        die("Database connection failed: " . $mysqli->connect_error);
    }

    // Log installation details in the main database
    $stmt = $mysqli->prepare("INSERT INTO installations (customer_name, email_address) VALUES (?, ?)");
    $stmt->bind_param("ss", $customer_name, $email_address);

    if (!$stmt->execute()) {
        die("Error inserting data: " . $stmt->error);
    }

    $installation_id = $stmt->insert_id;
    $stmt->close();

    // Create a new database for this WordPress installation
    $new_db_name = "nextwp_$installation_id";
    if (!$mysqli->query("CREATE DATABASE `$new_db_name`")) {
        die("Error creating database: " . $mysqli->error);
    }

    // Create a new directory for WordPress installation
    $new_wp_path = "./nextwp_$installation_id";
    if (!mkdir($new_wp_path, 0755, true)) {
        die("Error creating WordPress directory: $new_wp_path");
    }

    // Copy WordPress files to the new directory
    recurse_copy($wp_path, $new_wp_path);

    // Generate `wp-config.php`
    $secret_keys = file_get_contents("https://api.wordpress.org/secret-key/1.1/salt/");
    $wp_config = "<?php\n";
    $wp_config .= "define('DB_NAME', '$new_db_name');\n";
    $wp_config .= "define('DB_USER', '$user');\n";
    $wp_config .= "define('DB_PASSWORD', '$pass');\n";
    $wp_config .= "define('DB_HOST', 'localhost');\n";
    $wp_config .= "define('DB_CHARSET', 'utf8mb4');\n";
    $wp_config .= "define('DB_COLLATE', '');\n";
    $wp_config .= $secret_keys . "\n";
    $wp_config .= "\$table_prefix = 'wp_';\n";
    $wp_config .= "define('WP_DEBUG', false);\n";
    $wp_config .= "if (!defined('ABSPATH')) {\n";
    $wp_config .= "    define('ABSPATH', dirname(__FILE__) . '/');\n";
    $wp_config .= "}\n";
    $wp_config .= "require_once ABSPATH . 'wp-settings.php';\n";
    file_put_contents("$new_wp_path/wp-config.php", $wp_config);

    // Perform WordPress installation
    define('WP_INSTALLING', true);
    require_once("$new_wp_path/wp-load.php");
    require_once("$new_wp_path/wp-admin/includes/upgrade.php");
    require_once("$new_wp_path/wp-includes/wp-db.php");

    $site_title = "My WordPress Site";
    $admin_user = "admin";

    // Generate a random 12-character password
    function generateRandomPassword($length = 12) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }

    $admin_pass = generateRandomPassword();
    $admin_email = $email_address;

    wp_install($site_title, $admin_user, $admin_email, true, '', $admin_pass);

    // Update site URLs
    $site_url = "http://localhost/nextwp/nextwp_$installation_id";
    update_option('siteurl', $site_url);
    update_option('home', $site_url);

    // Respond with success
    echo json_encode([
        "status" => "success",
        "message" => "WordPress installation complete.",
        "url" => $site_url,
        "username" => $admin_user,
        "password" => $admin_pass
    ]);

    // Close database connection
    $mysqli->close();
} else {
    die("Invalid request method.");
}

// Function to copy files recursively
function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if ($file != '.' && $file != '..') {
            if (is_dir("$src/$file")) {
                recurse_copy("$src/$file", "$dst/$file");
            } else {
                copy("$src/$file", "$dst/$file");
            }
        }
    }
    closedir($dir);
}
