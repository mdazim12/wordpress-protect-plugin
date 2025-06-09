<?php
/**
 * Plugin Name: Site-Wide Password Protection
 * Description: Protects the entire WordPress website with a single password.
 * Version: 1.0
 * Author: Your Name/AI Assistant
 */

// Define your password here. IMPORTANT: Change 'spindel' to a strong password for security.
// For better security, consider hashing this password and comparing the hash.
define('SITE_PASSWORD_PROTECT', 'spindel'); 

// Start a session if one hasn't been started already.
// This is used to store whether the user has entered the password.
function site_password_protect_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'site_password_protect_start_session');

// Check if the user has entered the password and display the form if not.
function site_password_protect_check() {
    // Exclude wp-admin and wp-login.php from protection to allow site management.
    if (is_admin() || strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        return;
    }

    // Check if the correct password has been submitted.
    if (isset($_POST['site_password']) && $_POST['site_password'] === SITE_PASSWORD_PROTECT) {
        $_SESSION['site_unlocked'] = true; // Set session variable to grant access.
        // Redirect to clear POST data and prevent resubmission warnings.
        wp_redirect(esc_url_raw($_SERVER['REQUEST_URI']));
        exit;
    }

    // If session variable is not set, or password was incorrect, display the form.
    if (!isset($_SESSION['site_unlocked']) || $_SESSION['site_unlocked'] !== true) {
        // Output the HTML for the password form.
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Website Under Protection</title>
            <style>
                /* Basic styling for the password form */
                body {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    background-color: #f0f2f5;
                    color: #333;
                }
                .password-form-container {
                    background-color: #fff;
                    padding: 40px;
                    border-radius: 12px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 400px;
                    width: 90%;
                }
                h1 {
                    color: #2c3e50;
                    font-size: 2em;
                    margin-bottom: 20px;
                }
                p {
                    margin-bottom: 25px;
                    line-height: 1.5;
                }
                input[type="password"] {
                    width: calc(100% - 20px);
                    padding: 12px 10px;
                    margin-bottom: 20px;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    font-size: 1em;
                    box-sizing: border-box;
                }
                button {
                    background-color: #0073aa;
                    color: white;
                    padding: 12px 25px;
                    border: none;
                    border-radius: 8px;
                    font-size: 1.1em;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }
                button:hover {
                    background-color: #005177;
                }
                .error-message {
                    color: #d63638;
                    margin-top: 15px;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="password-form-container">
                <h1>Website Under Protection</h1>
                <p>This website is currently password protected. Please enter the password to gain access.</p>
                <form method="post">
                    <input type="password" name="site_password" placeholder="Enter password" required>
                    <button type="submit">Unlock Website</button>
                </form>
                <?php
                if (isset($_POST['site_password']) && $_POST['site_password'] !== SITE_PASSWORD_PROTECT) {
                    echo '<p class="error-message">Incorrect password. Please try again.</p>';
                }
                ?>
            </div>
        </body>
        </html>
        <?php
        exit; // Stop further WordPress loading.
    }
}
add_action('template_redirect', 'site_password_protect_check');

// Function to log out (destroy session) - Optional: add a link to this somewhere for testing
function site_password_protect_logout() {
    if (isset($_GET['site_logout']) && $_GET['site_logout'] === 'true') {
        session_destroy();
        wp_redirect(home_url()); // Redirect to homepage after logout
        exit;
    }
}
add_action('init', 'site_password_protect_logout');

?>
