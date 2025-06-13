<?php
/**
 * Plugin Name: Site Protection Pro
 * Plugin URI:  https://wordpress.org/plugins/site-protection-pro/
 * Description: Protect your entire WordPress site with a single, easy-to-manage password.
 * Version:     2.2.0
 * Author:      Azim Uddin
 * Author URI:  https://github.com/mdazim12/
 * Text Domain: site-protection-pro
 * Domain Path: /languages
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.6
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Copyright 2024 Azim Uddin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load plugin textdomain early, but after ABSPATH check
add_action('plugins_loaded', 'spp_load_textdomain');
function spp_load_textdomain() {
    load_plugin_textdomain('site-protection-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Admin menu
add_action('admin_menu', 'spp_add_admin_menu');
function spp_add_admin_menu() {
    add_options_page(
        esc_html__('Site Protection', 'site-protection-pro'), 
        esc_html__('Site Protection', 'site-protection-pro'), 
        'manage_options',
        'spp-settings',
        'spp_settings_page'
    );
}

// Register settings and add fields
add_action('admin_init', 'spp_register_settings');
function spp_register_settings() {
    // Nonce verification for the entire settings form submission
    if (
        isset($_POST['_wpnonce']) &&
        !empty($_POST['_wpnonce']) &&
        isset($_POST['option_page']) && // Ensure it's a settings page submission
        $_POST['option_page'] === 'spp_settings_group'
    ) {
        $nonce_from_post = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
        if (!wp_verify_nonce($nonce_from_post, 'spp_settings_group-options')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'site-protection-pro'), esc_html__('Security Error', 'site-protection-pro'), ['response' => 403]); 
        }
    }

    register_setting('spp_settings_group', 'spp_password_hash', [
        'type' => 'string',
        'sanitize_callback' => 'spp_sanitize_password_hash',
        'default' => '',
    ]);
    register_setting('spp_settings_group', 'spp_enabled', ['type' => 'boolean', 'default' => 0]);
    register_setting('spp_settings_group', 'spp_login_message', ['type' => 'string', 'default' => esc_html__('This website is currently password protected. Please enter the password to gain access.', 'site-protection-pro')]); 
    register_setting('spp_settings_group', 'spp_allow_cookies', ['type' => 'boolean', 'default' => 0]);
    register_setting('spp_settings_group', 'spp_whitelist_ips', ['type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '']);
    register_setting('spp_settings_group', 'spp_dark_mode', ['type' => 'boolean', 'default' => 0]);

    add_settings_section(
        'spp_main_settings_section',
        esc_html__('General Settings', 'site-protection-pro'), 
        null, // No description callback needed for this section
        'spp_settings_group'
    );

    add_settings_field(
        'spp_enabled_field',
        esc_html__('Enable Protection', 'site-protection-pro'), 
        function () {
            ?>
            <input type="checkbox" name="spp_enabled" value="1" <?php checked(1, get_option('spp_enabled'), true); ?> />
            <?php
        },
        'spp_settings_group',
        'spp_main_settings_section'
    );

    add_settings_field(
        'spp_new_password_field',
        esc_html__('New Password', 'site-protection-pro'), 
        'spp_new_password_callback',
        'spp_settings_group',
        'spp_main_settings_section'
    );

    add_settings_field(
        'spp_login_message_field',
        esc_html__('Login Message', 'site-protection-pro'),
        function () {
            ?>
            <input type="text" name="spp_login_message" value="<?php echo esc_attr(get_option('spp_login_message', esc_html__('This website is currently password protected. Please enter the password to gain access.', 'site-protection-pro'))); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e('Message displayed on the password protection page.', 'site-protection-pro'); ?></p> 
            <?php
        },
        'spp_settings_group',
        'spp_main_settings_section'
    );

    add_settings_field(
        'spp_allow_cookies_field',
        esc_html__('Allow Cookies for Access', 'site-protection-pro'), 
        function () {
            ?>
            <input type="checkbox" name="spp_allow_cookies" value="1" <?php checked(1, get_option('spp_allow_cookies'), true); ?> />
            <p class="description"><?php esc_html_e('If unchecked, access is session-based (expires on browser close). If checked, access uses cookies for longer persistence.', 'site-protection-pro'); ?></p> 
            <?php
        },
        'spp_settings_group',
        'spp_main_settings_section'
    );

    add_settings_field(
        'spp_whitelist_ips_field',
        esc_html__('Whitelisted IPs', 'site-protection-pro'), 
        function () {
            ?>
            <input type="text" name="spp_whitelist_ips" value="<?php echo esc_attr(get_option('spp_whitelist_ips', '')); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e('Comma-separated list of IP addresses to bypass protection.', 'site-protection-pro'); ?></p> 
            <?php
        },
        'spp_settings_group',
        'spp_main_settings_section'
    );

    add_settings_field(
        'spp_dark_mode_field',
        esc_html__('Enable Dark Mode for Login Page', 'site-protection-pro'), 
        function () {
            ?>
            <input type="checkbox" name="spp_dark_mode" value="1" <?php checked(1, get_option('spp_dark_mode'), true); ?> />
            <p class="description"><?php esc_html_e('Applies dark mode styles to the password protection page.', 'site-protection-pro'); ?></p> 
            <?php
        },
        'spp_settings_group',
        'spp_main_settings_section'
    );
}

/**
 * Renders the new password input field in admin settings.
 */
function spp_new_password_callback() {
    ?>
    <input type="password" name="spp_new_password" value="" class="regular-text" autocomplete="new-password" />
    <p class="description"><?php esc_html_e('Leave blank to keep the current password. Enter a new password to change it.', 'site-protection-pro'); ?></p>
    <?php
}

/**
 * Sanitizes and hashes the new password before saving.
 * This is the sanitize_callback for 'spp_password_hash'.
 *
 * @param string $old_password_hash The existing password hash.
 * @return string The new (or old) password hash.
 */
function spp_sanitize_password_hash($old_password_hash) {
    // Add a nonce check here as a double-check for the password change.
    // The main settings form nonce check should have already happened.
    $nonce_from_post = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
    if (!wp_verify_nonce($nonce_from_post, 'spp_settings_group-options')) {
        // If nonce fails, return the old hash without changes.
        // This prevents unauthorized password changes even if the main form check was bypassed.
        return $old_password_hash;
    }

    if (isset($_POST['spp_new_password']) && !empty($_POST['spp_new_password'])) {
        $new_password = sanitize_text_field(wp_unslash($_POST['spp_new_password']));
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        return $hashed_password;
    }
    return $old_password_hash;
}

// Admin page function (handles form rendering)
function spp_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Site Protection Pro Settings', 'site-protection-pro'); ?></h1> 
        <form method="post" action="options.php">
            <?php
            // settings_fields() automatically generates the nonce for the settings group
            // and includes a hidden field named '_wpnonce'
            settings_fields('spp_settings_group');
            do_settings_sections('spp_settings_group');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Protection logic
add_action('template_redirect', 'spp_template_redirect_protection');
function spp_template_redirect_protection() {
    // Validate $_SERVER['REQUEST_URI'] and $_SERVER['REQUEST_METHOD']
    $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    $request_method = isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '';

    if (is_admin() || strpos($request_uri, 'wp-login.php') !== false || (defined('DOING_AJAX') && DOING_AJAX) || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    if (!get_option('spp_enabled')) {
        return;
    }

    $whitelisted_ips = array_map('trim', explode(',', get_option('spp_whitelist_ips', '')));
    $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    if (in_array($remote_addr, $whitelisted_ips, true)) {
        return;
    }

    $timeout = 60 * 60; // 60 minutes
    $now = time();
    $access_granted = false;
    $error = '';

    if (get_option('spp_allow_cookies')) {
        $spp_access_token = isset($_COOKIE['spp_access_token']) ? sanitize_text_field(wp_unslash($_COOKIE['spp_access_token'])) : '';
        $spp_access_time = isset($_COOKIE['spp_access_time']) ? (int) sanitize_text_field(wp_unslash($_COOKIE['spp_access_time'])) : 0;

        if (!empty($spp_access_token) && $spp_access_time > 0 && ($spp_access_time + $timeout) > $now) {
            setcookie('spp_access_time', $now, [
                'expires' => $now + $timeout,
                'path' => '/',
                'secure' => is_ssl(),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            $access_granted = true;
        }
    } else {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $spp_session_access_granted = isset($_SESSION['spp_access_granted']) ? (bool) $_SESSION['spp_access_granted'] : false;
        $spp_session_access_time = isset($_SESSION['spp_access_time']) ? (int) $_SESSION['spp_access_time'] : 0;

        if ($spp_session_access_granted === true && $spp_session_access_time > 0 && ($spp_session_access_time + $timeout) > $now) {
            $_SESSION['spp_access_time'] = $now;
            $access_granted = true;
        }
    }

    // Check for password submission and nonce verification
    if (!$access_granted && $request_method === 'POST' && isset($_POST['spp_password'])) {
        // Sanitize the nonce before verification
        $spp_nonce_submitted = isset($_POST['spp_nonce']) ? sanitize_text_field(wp_unslash($_POST['spp_nonce'])) : '';

        if (wp_verify_nonce($spp_nonce_submitted, 'spp_login_form_nonce')) {
            $hash = get_option('spp_password_hash');
            $submitted = sanitize_text_field(wp_unslash($_POST['spp_password']));
            $success = password_verify($submitted, $hash);

            spp_log_attempt($success);

            if ($success) {
                if (get_option('spp_allow_cookies')) {
                    setcookie('spp_access_token', md5($submitted), [
                        'expires' => $now + $timeout,
                        'path' => '/',
                        'secure' => is_ssl(),
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                    setcookie('spp_access_time', $now, [
                        'expires' => $now + $timeout,
                        'path' => '/',
                        'secure' => is_ssl(),
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                } else {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['spp_access_granted'] = true;
                    $_SESSION['spp_access_time'] = $now;
                }

                wp_safe_redirect($request_uri);
                exit;
            } else {
                $error = __('Incorrect password. Please try again.', 'site-protection-pro'); 
            }
        } else {
            // Nonce verification failed for the login form
            wp_die(esc_html__('Security check failed for login. Please try again.', 'site-protection-pro'), esc_html__('Security Error', 'site-protection-pro'), ['response' => 403]); 
        }
    }

    if (!$access_granted) {
        $message = get_option('spp_login_message', esc_html__('This website is currently password protected. Please enter the password to gain access.', 'site-protection-pro')); 
        $dark_mode = get_option('spp_dark_mode');
        $dark_mode_styles = '';
        if ($dark_mode) {
            $dark_mode_styles = '
            body { background: #2c3e50; color: #ecf0f1; }
            .login-box { background: #34495e; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); }
            .login-box p { color: #bdc3c7; }
            input[type="password"] { background: #233140; border-color: #4a667f; color: #ecf0f1; }
            input[type="password"]::placeholder { color: #95a5a6; }
            button { background-color: #3498db; }
            button:hover { background-color: #2980b9; }
            .error { color: #e74c3c; }
            ';
        }

        ?>
        <!DOCTYPE html>
        <html lang="en" class="<?php echo esc_attr($dark_mode ? 'dark-mode' : ''); ?>">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo esc_html__('Protected Site', 'site-protection-pro'); ?></title> 
            <style><?php echo esc_html($dark_mode_styles); ?></style>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; background: #f0f2f5; color: #333; }
                .login-box { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); text-align: center; width: 100%; max-width: 400px; }
                .login-box h2 { margin-top: 0; color: #222; font-size: 24px; margin-bottom: 20px; }
                .login-box p { font-size: 16px; line-height: 1.5; margin-bottom: 30px; color: #555; }
                .login-box form { display: flex; flex-direction: column; gap: 15px; }
                input[type="password"] { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; box-sizing: border-box; transition: border-color 0.3s; }
                input[type="password"]:focus { border-color: #0073aa; outline: none; box-shadow: 0 0 0 1px #0073aa; }
                button[type="submit"] { background-color: #0073aa; color: white; padding: 12px 20px; border: none; border-radius: 5px; font-size: 18px; cursor: pointer; transition: background-color 0.3s; }
                button[type="submit"]:hover { background-color: #005177; }
                .error { color: #d63638; margin-top: 15px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2><?php echo esc_html__('Website Under Protection', 'site-protection-pro'); ?></h2> 
                <p><?php echo esc_html($message); ?></p>
                <form method="post">
                    <?php wp_nonce_field('spp_login_form_nonce', 'spp_nonce'); ?>
                    <input type="password" name="spp_password" placeholder="<?php echo esc_attr__('Enter password', 'site-protection-pro'); ?>" required autocomplete="current-password"> 
                    <button type="submit"><?php echo esc_html__('Unlock Website', 'site-protection-pro'); ?></button> 
                </form>
                <?php
                if (!empty($error)) {
                    echo '<div class="error">' . esc_html($error) . '</div>';
                }
                ?>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Logging function
add_action('plugins_loaded', 'spp_register_logging_function');
function spp_register_logging_function() {
    if ( ! function_exists( 'spp_log_attempt' ) ) {
        function spp_log_attempt($success) {
            $log_file = WP_CONTENT_DIR . '/spp_login.log';
            $timestamp = current_time('mysql', true);
            $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'UNKNOWN_IP';
            $log_message = sprintf("[%s] IP: %s | Status: %s\n", $timestamp, $remote_addr, $success ? 'SUCCESS' : 'FAIL');
            file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX);
        }
    }
}


// Manual logout via URL
add_action('init', function () {
    $spp_logout_action = 'spp_logout_nonce';
    $spp_logout = isset($_GET['spp_logout']) ? sanitize_text_field(wp_unslash($_GET['spp_logout'])) : '';

    if ($spp_logout === 'true') {
        $nonce_from_get = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';

        // Add nonce verification for logout
        if (!wp_verify_nonce($nonce_from_get, $spp_logout_action)) {
            wp_die(esc_html__('Security check failed for logout. Please try again.', 'site-protection-pro'), esc_html__('Security Error', 'site-protection-pro'), ['response' => 403]); 
        }

        setcookie('spp_access_token', '', time() - 3600, '/', '', is_ssl(), true);
        setcookie('spp_access_time', '', time() - 3600, '/', '', is_ssl(), true);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (session_id()) {
            session_destroy();
            $_SESSION = array();
        }

        // Example of how to generate a logout link with a nonce:
        // echo '<a href="' . esc_url(wp_nonce_url(home_url('?spp_logout=true'), $spp_logout_action, '_wpnonce')) . '">' . esc_html__('Logout', 'site-protection-pro') . '</a>';
        // Note: The above is for demonstration. Implement your actual logout link where appropriate in your theme or another part of your plugin.

        wp_redirect(home_url());
        exit;
    }
});