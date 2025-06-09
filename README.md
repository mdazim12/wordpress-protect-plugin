Site-Wide Password Protection WordPress Plugin
This is a custom WordPress plugin designed to protect your entire website with a single password, ensuring that only authorized individuals can access its content. It's a simple, lightweight solution for development sites, private portfolios, or temporary privacy needs.

Features
Entire Site Protection: Places a password gate in front of your whole WordPress website.

Simple Password: Uses a single, hardcoded password for access (configurable within the plugin file).

Admin Bypass: Automatically allows logged-in WordPress administrators to bypass the password screen for easy site management.

Customizable Prompt: Displays a basic, customizable password entry form to visitors.

Session-Based Access: Once the correct password is entered, access is granted for the duration of the user's session.

Clear POST Data: Redirects after successful login to prevent browser warnings on refresh.

Logout Functionality: Includes an optional URL parameter to manually log out (destroy session) for testing.

Installation
Create the Plugin File:

Open a plain text editor (e.g., Notepad, VS Code).

Copy the entire code from the site-password-protect.php file provided to you.

IMPORTANT: Locate the line define('SITE_PASSWORD_PROTECT', 'spindel'); and change 'spindel' to a strong, unique password of your choice. For a live site, it's highly recommended to use a more complex, hashed password system.

Save the file as site-password-protect.php.

Upload to Your WordPress Site:

Connect to your WordPress site via FTP/SFTP client (like FileZilla) or use your hosting provider's File Manager.

Navigate to the wp-content/plugins/ directory.

Inside plugins, create a new folder named site-password-protect (or any other descriptive name).

Upload the site-password-protect.php file into this new folder.

Activate the Plugin:

Log in to your WordPress admin dashboard (yourdomain.com/wp-admin).

Go to Plugins > Installed Plugins.

Locate "Site-Wide Password Protection" in the list.

Click the "Activate" link below its name.

Usage
Once activated, your entire website (excluding the wp-admin area and wp-login.php) will display a password entry form to visitors.

Entering the Password: Visitors must enter the password you defined in SITE_PASSWORD_PROTECT to access the site.

Administrator Access: If you are logged in as a WordPress administrator, you will automatically bypass the password screen.

Logging Out (for testing): To simulate a new visitor or test the password screen again, you can append ?site_logout=true to your site's URL (e.g., https://yourdomain.com/?site_logout=true). This will destroy the session and require the password again.

Important Considerations & Security Notes
Security: This plugin uses a hardcoded password. While convenient for quick setups, for highly sensitive sites or long-term solutions, storing passwords directly in code is not the most secure practice. For enhanced security, consider:

Using password_hash() and password_verify() for password storage and comparison.

Implementing rate limiting for password attempts.

Utilizing more robust, feature-rich password protection plugins from the WordPress plugin repository (e.g., "Password Protected" by WPExperts, which offers more advanced options like IP whitelisting, multiple passwords, and brute-force protection).

SEO: A password-protected site like this will generally not be indexed by search engines, as they cannot access the content.

Media Files: This plugin primarily protects WordPress pages and posts. Direct links to media files (images, documents) might still be accessible if their URLs are known.

No WordPress Header/Footer: When the password screen is active, the plugin explicitly outputs its own HTML, meaning your theme's header, footer, and other WordPress-generated content are not loaded or displayed.

Development
If you wish to modify or extend this plugin:

Styling: Adjust the CSS within the <style> tags in the site-password-protect.php file to change the appearance of the password form.

Features: For advanced features like different passwords for different users, or partial content protection, it's highly recommended to use a dedicated membership or content restriction plugin as outlined in previous discussions.