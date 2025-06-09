# Site-Wide Password Protection – WordPress Plugin

A simple, lightweight WordPress plugin that protects your entire site with a single password. Ideal for development environments, private portfolios, or temporary privacy needs, this plugin ensures that only authorized users can access your website content.

---

## 🔐 Features

- **Full Site Protection**: Blocks access to all front-end pages until the correct password is entered.
- **Single Password Gate**: Uses a hardcoded password (modifiable in the plugin file).
- **Admin Bypass**: Logged-in administrators bypass the password screen automatically.
- **Custom Prompt UI**: Includes a clean password form that you can style directly in the plugin file.
- **Session-Based Access**: Access is granted for the entire session once the password is correctly entered.
- **POST Redirect Handling**: Redirects after login to avoid browser resubmission warnings.
- **Manual Logout**: Visit any URL with `?site_logout=true` to destroy the session and trigger the login screen again.

---

## 🛠 Installation

### Step 1: Create the Plugin File

1. Open a plain text editor (e.g., VS Code, Notepad).
2. Paste the plugin code into the file.
3. Locate the line:

   ```php
   define('SITE_PASSWORD_PROTECT', 'spindel');
