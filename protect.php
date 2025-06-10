<?php
/**
 * Plugin Name: Site-Wide Password Protection PRO
 * Description: Protect the entire site with a single password using a modern UI. Includes admin settings and security features.
 * Version: 2.2.0 // Cleaned for production
 * Author: Azim Uddin
 * Text Domain: swppro
 */

// IMPORTANT: THIS MUST BE THE VERY FIRST LINE. NO WHITESPACE OR NEWLINES ABOVE.
// Start output buffering as early as possible to catch any accidental output before headers.
// This is critical for preventing "Headers already sent" errors for redirects.
if (!ob_get_level()) {
    ob_start();
}

// Start session
add_action('init', function () {
    if (!session_id()) session_start();
});

// Load plugin textdomain
add_action('plugins_loaded', function () {
    load_plugin_textdomain('swppro', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Admin menu
add_action('admin_menu', function () {
    add_options_page(
        __('Site Protection', 'swppro'),
        __('Site Protection', 'swppro'),
        'manage_options',
        'swp-settings',
        'swp_settings_page'
    );
});

// Register settings and add fields
add_action('admin_init', function () {
    // Register the settings group
    register_setting('swp_settings_group', 'swp_password_hash', [
        'type' => 'string',
        'sanitize_callback' => 'swp_sanitize_password_hash', // Custom callback for password hashing
        'default' => '',
    ]);
    register_setting('swp_settings_group', 'swp_enabled', ['type' => 'boolean', 'default' => 0]);
    register_setting('swp_settings_group', 'swp_login_message', ['type' => 'string', 'default' => __('This website is currently password protected. Please enter the password to gain access.', 'swppro')]);
    register_setting('swp_settings_group', 'swp_session_timeout', ['type' => 'integer', 'default' => 60]);
    register_setting('swp_settings_group', 'swp_allow_cookies', ['type' => 'boolean', 'default' => 0]);
    register_setting('swp_settings_group', 'swp_whitelist_ips', ['type' => 'string', 'default' => '']);
    register_setting('swp_settings_group', 'swp_dark_mode', ['type' => 'boolean', 'default' => 0]);

    // Add a settings section
    add_settings_section(
        'swp_main_settings_section',
        __('General Settings', 'swppro'),
        null, // No description callback needed for this section
        'swp_settings_group'
    );

    // Add settings fields
    add_settings_field(
        'swp_enabled_field',
        __('Enable Protection', 'swppro'),
        function () {
            ?>
            <input type="checkbox" name="swp_enabled" value="1" <?php checked(1, get_option('swp_enabled'), true); ?> />
            <?php
        },
        'swp_settings_group',
        'swp_main_settings_section'
    );

    add_settings_field(
        'swp_new_password_field',
        __('New Password', 'swppro'),
        'swp_new_password_callback', // Callback to render the password input
        'swp_settings_group',
        'swp_main_settings_section'
    );

    add_settings_field(
        'swp_login_message_field',
        __('Login Message', 'swppro'),
        function () {
            ?>
            <input type="text" name="swp_login_message" value="<?php echo esc_attr(get_option('swp_login_message', 'This website is currently password protected. Please enter the password to gain access.')); ?>" class="regular-text" />
            <p class="description"><?php _e('Message displayed on the password protection page.', 'swppro'); ?></p>
            <?php
        },
        'swp_settings_group',
        'swp_main_settings_section'
    );

    add_settings_field(
        'swp_session_timeout_field',
        __('Session Timeout (minutes)', 'swppro'),
        function () {
            ?>
            <input type="number" name="swp_session_timeout" value="<?php echo esc_attr(get_option('swp_session_timeout', 60)); ?>" min="1" class="small-text" />
            <p class="description"><?php _e('How long the access session lasts after successful login.', 'swppro'); ?></p>
            <?php
        },
        'swp_settings_group',
        'swp_main_settings_section'
    );

    add_settings_field(
        'swp_allow_cookies_field',
        __('Use Cookies', 'swppro'),
        function () {
            ?>
            <input type="checkbox" name="swp_allow_cookies" value="1" <?php checked(1, get_option('swp_allow_cookies'), true); ?> />
            <p class="description"><?php _e('Enable to use cookies for session persistence instead of PHP sessions.', 'swppro'); ?></p>
            <?php
        },
        'swp_settings_group',
        'swp_main_settings_section'
    );

    add_settings_field(
        'swp_whitelist_ips_field',
        __('Whitelist IPs (comma-separated)', 'swppro'),
        function () {
            ?>
            <input type="text" name="swp_whitelist_ips" value="<?php echo esc_attr(get_option('swp_whitelist_ips', '')); ?>" class="regular-text" />
            <p class="description"><?php _e('IP addresses that will bypass the password protection. Separate multiple IPs with commas.', 'swppro'); ?></p>
            <?php
        },
        'swp_settings_group',
        'swp_main_settings_section'
    );

    add_settings_field(
        'swp_dark_mode_field',
        __('Enable Dark Mode', 'swppro'),
        function () {
            ?>
            <input type="checkbox" name="swp_dark_mode" value="1" <?php checked(1, get_option('swp_dark_mode'), true); ?> />
            <p class="description"><?php _e('Toggle dark mode for the password entry page.', 'swppro'); ?></p>
            <?php
        },
        'swp_settings_group',
        'swp_main_settings_section'
    );
});

/**
 * Renders the new password input field in admin settings.
 */
function swp_new_password_callback() {
    ?>
    <input type="password" name="swp_new_password" value="" class="regular-text" autocomplete="new-password" />
    <p class="description"><?php _e('Leave blank to keep the current password. Enter a new password to change it.', 'swppro'); ?></p>
    <?php
}

/**
 * Sanitizes and hashes the new password before saving.
 * This is the sanitize_callback for 'swp_password_hash'.
 *
 * @param string $old_password_hash The existing password hash.
 * @return string The new (or old) password hash.
 */
function swp_sanitize_password_hash($old_password_hash) {
    if (isset($_POST['swp_new_password']) && !empty($_POST['swp_new_password'])) {
        $new_password = sanitize_text_field($_POST['swp_new_password']);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        return $hashed_password;
    }
    // If no new password is provided, retain the existing one.
    return $old_password_hash;
}


// Admin page function (now just handles form rendering)
function swp_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Site-Wide Password Protection Settings', 'swppro'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('swp_settings_group'); ?>
            <?php do_settings_sections('swp_settings_group'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Protection logic
add_action('template_redirect', function () {
    // Bypass for admin area and wp-login.php, and AJAX requests
    if (is_admin() || strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false || (defined('DOING_AJAX') && DOING_AJAX) || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    // Return if protection is not enabled
    if (!get_option('swp_enabled')) {
        return;
    }

    // Whitelist IP check
    $whitelisted_ips = array_map('trim', explode(',', get_option('swp_whitelist_ips', '')));
    if (in_array($_SERVER['REMOTE_ADDR'], $whitelisted_ips)) {
        return;
    }

    $timeout = intval(get_option('swp_session_timeout', 60)) * 60; // Convert minutes to seconds
    $now = time();
    $access_granted = false;
    $error = ''; // Initialize error message for display

    // 1. Check for existing access via cookies or session FIRST
    if (get_option('swp_allow_cookies')) {
        if (isset($_COOKIE['swp_access_token']) && isset($_COOKIE['swp_access_time']) && ($_COOKIE['swp_access_time'] + $timeout) > $now) {
            // Re-set cookie to extend session
            setcookie('swp_access_time', $now, [
                'expires' => $now + $timeout,
                'path' => '/',
                'secure' => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            $access_granted = true;
        }
    } else {
        if (isset($_SESSION['swp_access_granted']) && $_SESSION['swp_access_granted'] === true && isset($_SESSION['swp_access_time']) && ($_SESSION['swp_access_time'] + $timeout) > $now) {
            // Update session time
            $_SESSION['swp_access_time'] = $now;
            $access_granted = true;
        }
    }

    // 2. Handle password submission IF access is NOT yet granted
    if (!$access_granted && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['swp_password'])) {
        $hash = get_option('swp_password_hash');
        $submitted = sanitize_text_field($_POST['swp_password']);
        $success = password_verify($submitted, $hash);

        swp_log_attempt($submitted, $success); // Log the attempt

        if ($success) {
            // Set session/cookies and REDIRECT IMMEDIATELY if password is correct
            if (get_option('swp_allow_cookies')) {
                setcookie('swp_access_token', md5($submitted), [
                    'expires' => $now + $timeout,
                    'path' => '/',
                    'secure' => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
                setcookie('swp_access_time', $now, [
                    'expires' => $now + $timeout,
                    'path' => '/',
                    'secure' => is_ssl(),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            } else {
                $_SESSION['swp_access_granted'] = true;
                $_SESSION['swp_access_time'] = $now;
            }

            // IMPORTANT: Clear any buffered output and redirect immediately BEFORE any HTML output
            if (ob_get_level()) { // Check if output buffering is active
                ob_clean(); // Clear the output buffer
            }

            // Attempt the WordPress safe redirect to the current URI or home_url() as a fallback
            wp_safe_redirect(esc_url_raw($_SERVER['REQUEST_URI'])); // Try to redirect to the current URL
            exit; // Stop further execution
        } else {
            $error = __('Incorrect password. Please try again.', 'swppro');
        }
    }

    // 3. IF access is still NOT granted at this point, render the password protection form
    if (!$access_granted) {
        $message = get_option('swp_login_message', 'This website is currently password protected. Please enter the password to gain access.');
        $dark_mode = get_option('swp_dark_mode');

        // Basic dark mode styles
        $dark_mode_styles = '';
        if ($dark_mode) {
            $dark_mode_styles = '
            body {
                background: #2c3e50;
                color: #ecf0f1;
            }
            .login-box {
                background: #34495e;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }
            .login-box p {
                color: #bdc3c7;
            }
            input[type="password"] {
                background: #233140;
                border-color: #4a667f;
                color: #ecf0f1;
            }
            input[type="password"]::placeholder {
                color: #95a5a6;
            }
            button {
                background-color: #3498db;
            }
            button:hover {
                background-color: #2980b9;
            }
            .error {
                color: #e74c3c;
            }
            ';
        }

        echo '<!DOCTYPE html>
        <html lang="en" class="' . ($dark_mode ? 'dark-mode' : '') . '">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . esc_html__('Protected Site', 'swppro') . '</title>
            <style>
                body {
                    margin: 0;
                    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
                    background: #f3f5f9;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    color: #2c3e50;
                    padding: 20px;
                    box-sizing: border-box;
                }
                .login-box {
                    background: #fff;
                    padding: 40px;
                    border-radius: 12px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 450px;
                    width: 100%;
                    box-sizing: border-box;
                }
                .login-box h2 {
                    margin-bottom: 15px;
                    font-weight: 700;
                    font-size: 1.8rem;
                    color: #333;
                }
                .login-box p {
                    font-size: 1rem;
                    margin-bottom: 30px;
                    color: #555;
                    line-height: 1.5;
                }
                input[type="password"] {
                    width: calc(100% - 24px); /* Account for padding */
                    padding: 12px;
                    border-radius: 8px;
                    border: 1px solid #ddd;
                    margin-bottom: 20px;
                    font-size: 1.1rem;
                    box-sizing: border-box;
                    outline: none;
                    transition: border-color 0.3s ease;
                }
                input[type="password"]:focus {
                    border-color: #0073aa;
                }
                button {
                    padding: 14px 30px;
                    background-color: #0073aa;
                    color: #fff;
                    border: none;
                    border-radius: 8px;
                    font-size: 1.1rem;
                    cursor: pointer;
                    transition: background-color 0.3s ease, transform 0.2s ease;
                    font-weight: 600;
                }
                button:hover {
                    background-color: #005177;
                    transform: translateY(-2px);
                }
                button:active {
                    transform: translateY(0);
                }
                .error {
                    color: #e74c3c;
                    font-weight: bold;
                    margin-top: 20px;
                    font-size: 1rem;
                }
                /* Responsive adjustments */
                @media (max-width: 600px) {
                    .login-box {
                        padding: 30px 20px;
                    }
                    .login-box h2 {
                        font-size: 1.6rem;
                    }
                    .login-box p {
                        font-size: 0.95rem;
                    }
                    input[type="password"], button {
                        font-size: 1rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2>' . esc_html__('Website Under Protection', 'swppro') . '</h2>
                <p>' . esc_html($message) . '</p>
                <form method="post">
                    <input type="password" name="swp_password" placeholder="' . esc_attr__('Enter password', 'swppro') . '" required autocomplete="current-password">
                    <button type="submit">' . esc_html__('Unlock Website', 'swppro') . '</button>
                </form>';
        if (!empty($error)) {
            echo '<div class="error">' . esc_html($error) . '</div>';
        }
        echo '
            </div>
        </body>
        </html>';
        exit; // Stop further rendering of the page
    }
    // If $access_granted is true at this point, the function simply returns, allowing WordPress to load the page.
});

// Logging function
function swp_log_attempt($submitted, $success) {
    // Ensure the log directory exists and is writable if needed (WordPress handles wp-content usually)
    $log_file = WP_CONTENT_DIR . '/swp_login.log';
    $log_message = sprintf("[%s] IP: %s | Status: %s\n", date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $success ? 'SUCCESS' : 'FAIL');
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Manual logout via URL
add_action('init', function () {
    if (isset($_GET['swp_logout']) && $_GET['swp_logout'] === 'true') {
        // Clear cookies
        setcookie('swp_access_token', '', time() - 3600, '/', '', is_ssl(), true);
        setcookie('swp_access_time', '', time() - 3600, '/', '', is_ssl(), true);

        // Destroy session if used
        if (session_id()) {
            session_destroy();
            $_SESSION = array(); // Clear session variables
        }

        wp_redirect(home_url()); // Redirect to home page
        exit;
    }
});
