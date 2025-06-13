=== Site Protection Pro ===
Contributors: mdazimuddin
Tags: password protect, site protection, maintenance mode, private site, content restriction
Requires at least: 5.6
Tested up to: 6.8
Stable tag: 2.2.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Protect your entire WordPress site with a single, easy-to-manage password. Features a modern, responsive UI.

== Description ==

**Site Protection Pro** is a simple yet powerful plugin designed to secure your entire WordPress website behind a single password. Perfect for development sites, coming soon pages, or private content, this plugin ensures that only authorized users can access your site's content.

With a clean, modern, and responsive password entry screen, your visitors will have a seamless experience while your content remains protected. The plugin is lightweight, easy to configure, and focuses on essential protection without unnecessary bloat.

**Key Features:**

* **Full Site Protection:** Secure every page and post on your WordPress site.
* **Single Password Entry:** Manage access with one universal password.
* **Modern & Responsive UI:** A sleek, user-friendly password input screen that looks great on any device.
* **Customizable Login Message:** Personalize the message displayed on the password protection page.
* **Session-Based Access:** Once the correct password is entered, access is granted for a set duration (60 minutes by default), preventing repeated password prompts during a single session.
* **Login Attempt Logging:** Basic logging of successful and failed password attempts to `wp-content/spp_login.log` for security monitoring.
* **Easy Logout:** Users can clear their access session by appending `?spp_logout=true` to any URL.
* **Admin Area Bypass:** The WordPress admin dashboard (`wp-admin`), login page (`wp-login.php`), and AJAX requests are automatically excluded from protection for seamless management.

**Why Choose Site Protection Pro?**

* **Simplicity:** No complex roles or user management – just one password for your entire site.
* **Security Focused:** Uses strong password hashing (`password_hash`) and automatically logs access attempts.
* **Clean Experience:** Provides a professional and non-intrusive barrier to your content.
* **Lightweight:** Designed to be efficient and not slow down your website.

Whether you're developing a new site, showcasing a private portfolio, or need a quick way to put your site in maintenance mode, Site Protection Pro offers a straightforward and effective solution.

== Installation ==

1.  **Upload the plugin files** to the `/wp-content/plugins/site-protection-pro` directory, or install the plugin through the WordPress plugins screen directly.
2.  **Activate the plugin** through the 'Plugins' screen in your WordPress admin area.
3.  Navigate to **Settings > Site Protection** to configure your password and enable the protection.
4.  Set a strong password and save your settings.
5.  **Enable the "Enable Protection"** checkbox to activate site-wide password protection.
6.  (Optional) Customize the login message displayed to visitors.

== Frequently Asked Questions ==

**Q: Where do I set the password?**
A: You can set the password in your WordPress admin area under **Settings > Site Protection**.

**Q: Can I protect only certain pages or posts?**
A: No, this plugin is designed for site-wide protection, meaning it protects all public-facing content. If you need to protect specific content, other plugins offer post/page-level restrictions.

**Q: What happens if I forget the password?**
A: If you forget the password, you will need to disable the plugin's protection via the WordPress admin area. If you are locked out of the admin area, you would need to temporarily disable the plugin by renaming its folder via FTP/SFTP or your hosting file manager.

**Q: Are WordPress admin users protected?**
A: No, the admin area (`wp-admin`), the login page (`wp-login.php`), and AJAX requests are automatically bypassed so you can manage your site without interruption.

**Q: How long does the password access last?**
A: Once a user enters the correct password, their session will remain active for 60 minutes. After this time, they will be prompted for the password again.

**Q: Where can I find the login attempt logs?**
A: Basic login attempt logs are stored in a file named `spp_login.log` within your `wp-content` directory.

== Screenshots ==

1.  **Plugin Settings Page:** Shows the straightforward options for enabling protection, setting the password, and customizing the login message.
2.  **Password Protection Screen (Frontend):** A preview of the modern, responsive UI visitors will see before gaining access.

== Changelog ==

**2.2.0**
* Removed Session Timeout, Use Cookies, Whitelist IPs, and Dark Mode options from settings.
* Streamlined protection logic to solely use PHP sessions for access.
* Fixed session timeout to 60 minutes for consistent security.
* Enhanced code for WordPress Plugin Directory submission readiness.

**2.1.0**
* Improved security with `httponly` and `samesite` attributes for cookies.
* Added IP whitelisting feature.
* Introduced a Dark Mode option for the password page.
* Enhanced security logging.

**2.0.0**
* Major UI redesign for the password protection page.
* Added session timeout option.
* Implemented cookie-based session persistence as an alternative.
* Improved security with `password_hash` for storing passwords.

**1.0.0**
* Initial release of the plugin with basic site-wide password protection.

== Upgrade Notice ==

**2.2.0**
This update removes several advanced options (Session Timeout, Use Cookies, Whitelist IPs, Dark Mode) to simplify the plugin. If you relied on these features, please be aware of this change. The core site-wide protection remains fully functional.