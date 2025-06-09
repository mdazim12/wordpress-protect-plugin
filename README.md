Site-Wide Password Protection – WordPress Plugin
Site-Wide Password Protection is a lightweight and easy-to-use WordPress plugin that restricts access to your entire website behind a single password. Ideal for development sites, private portfolios, or temporarily hiding content from the public, this plugin ensures only authorized users can view your site.

🔐 Features
Full Site Protection: Displays a password gate before any front-end content loads.

Simple Configuration: Uses a single hardcoded password (easily customizable in the plugin file).

Admin Bypass: Logged-in administrators bypass the password screen automatically.

Customizable Password Prompt: Includes a clean, minimal password form that can be styled as needed.

Session-Based Access: Grants access for the session after password entry — no need to re-enter on every page.

POST Data Cleansing: Redirects after successful login to prevent form resubmission alerts.

Manual Logout: Add ?site_logout=true to the URL to clear the session and trigger the password prompt again.

🛠 Installation
Create the Plugin File:

Open a text editor (e.g., VS Code, Notepad).

Copy the contents of the provided site-password-protect.php file.

Find the line:

php
Copy
Edit
define('SITE_PASSWORD_PROTECT', 'spindel');
Replace 'spindel' with a strong, unique password of your choice.

Save the file as site-password-protect.php.

Upload to Your Site:

Connect to your site via FTP/SFTP or use your hosting provider’s File Manager.

Navigate to wp-content/plugins/.

Create a new folder named site-password-protect.

Upload site-password-protect.php into this folder.

Activate the Plugin:

Log into your WordPress admin dashboard.

Go to Plugins → Installed Plugins.

Find Site-Wide Password Protection and click Activate.

🚀 Usage
Once activated, your entire website (except the admin panel and login screen) will require a password to access.

Visitors must enter the correct password to proceed.

Administrators logged into WordPress bypass the password prompt automatically.

To logout and re-trigger the password form, add ?site_logout=true to any page URL.

ruby
Copy
Edit
Example: https://yourdomain.com/?site_logout=true
⚠️ Important Notes & Security Considerations
Security Notice: The plugin uses a hardcoded password. For basic protection this is fine, but not recommended for high-security environments.

For improved security:

Use password_hash() and password_verify() instead of plain text passwords.

Consider adding rate-limiting to prevent brute-force attempts.

For advanced use cases, explore well-established plugins like Password Protected by WPExperts.

Search Engine Visibility: Search engines will not index protected content, as they cannot pass the password gate.

Media File Access: Direct links to files (images, PDFs, etc.) may still be accessible if URLs are known. This plugin does not restrict file access at the server level.

Theme Compatibility: The password screen uses custom HTML and does not load your WordPress theme or templates. You may style it directly within the plugin file.

🎨 Customization & Development
Styling: Modify the <style> block inside the plugin file to adjust the appearance of the password screen.

Feature Extensions: For advanced features (e.g., user-based passwords, partial protection, role access), consider a membership or content restriction plugin instead of modifying this one.

📄 License
This plugin is released under the MIT License — you are free to use, modify, and distribute it.