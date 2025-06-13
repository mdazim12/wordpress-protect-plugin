# Site Protection Pro

**Site Protection Pro** is a WordPress plugin that provides a simple, secure way to password-protect your entire site. You can manage settings via the admin panel, including the option to allow cookie-based access, whitelist IP addresses, and enable dark mode for the login page.

## Features

- **Password Protection:** Secure your WordPress site with a single, easy-to-manage password.
- **Admin Settings:**
  - Enable/Disable site protection.
  - Set a custom login message displayed on the password protection page.
  - Allow cookies for persistent access (or session-based access).
  - Whitelist specific IP addresses to bypass protection.
  - Enable Dark Mode for the login page.
- **Logging:** Tracks successful and failed login attempts in a log file for security auditing.
- **Manual Logout:** A URL (`?spp_logout=true`) can be used to manually log out the user and clear cookies and session data.

## Installation

1. Download the `Site Protection Pro` plugin.
2. Upload the plugin to your WordPress site:
   - Go to `Plugins > Add New` in the WordPress dashboard.
   - Click `Upload Plugin` and select the downloaded `.zip` file.
   - Click `Install Now`, and then activate the plugin.

3. **Configure Plugin Settings:**
   - After activation, go to `Settings > Site Protection` in the WordPress admin panel.
   - Set your password, enable/disable protection, set login messages, allow cookies, and whitelist IPs as needed.

## Admin Settings

1. **Enable Protection:** Toggle to activate or deactivate the password protection for the entire site.
2. **New Password:** Set a new password for your website.
3. **Login Message:** A custom message that will be shown on the login page.
4. **Allow Cookies for Access:** Choose whether to use cookies for persistent login or session-based access (expires when the browser is closed).
5. **Whitelisted IPs:** Add a comma-separated list of IP addresses that are allowed to access the site without a password.
6. **Enable Dark Mode for Login Page:** Enable dark mode for a sleek, modern design on the password protection page.

## Usage

- When site protection is enabled, visitors will be prompted to enter a password before accessing the website.
- If cookies are enabled, the password will be remembered for a set period of time.
- Whitelisted IPs will be able to bypass the password screen.
- You can manually log out by visiting the logout URL with the `spp_logout=true` parameter.

### Example of Logout Link:
```html
<a href="<?php echo esc_url(wp_nonce_url(home_url('?spp_logout=true'), 'spp_logout_nonce', '_wpnonce')); ?>"><?php esc_html_e('Logout', 'site-protection-pro'); ?></a>
