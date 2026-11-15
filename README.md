# SMNTCS Admin Bar Toggle

![Support Level](https://img.shields.io/badge/support-active-green.svg)
![Build Status](https://github.com/nielslange/smntcs-admin-bar-toggle/actions/workflows/test.yml/badge.svg)
![GPLv2 License](https://img.shields.io/github/license/nielslange/smntcs-admin-bar-toggle.svg)
![Compatible to WordPress version](https://img.shields.io/wordpress/plugin/tested/smntcs-admin-bar-toggle.svg)
![Compatible to PHP version](https://img.shields.io/wordpress/plugin/required-php/smntcs-admin-bar-toggle.svg)
![Downloads](https://img.shields.io/wordpress/plugin/dt/smntcs-admin-bar-toggle.svg)
![Plugin Version](https://img.shields.io/wordpress/plugin/v/smntcs-admin-bar-toggle.svg)
![Tag Version](https://img.shields.io/github/tag/nielslange/smntcs-admin-bar-toggle.svg)

Show or hide the admin bar on the frontend with a keyboard shortcut, without leaving a gap at the top of the page.

**SMNTCS Admin Bar Toggle** lets logged-in users hide the admin bar while they look at the frontend, so they see the site the way visitors do. Press **Cmd+B** on a Mac or **Ctrl+B** on Windows and Linux to hide the admin bar, and press it again to bring it back. The page moves up with it, so no empty gap is left behind.

- Hide and show the admin bar on the frontend with one key combination
- No gap: the page and the theme's admin bar offsets move up as well
- Remembers the state between page loads
- Personal shortcut per user, recorded under **Users → Profile → Toolbar shortcut**
- Change the site-wide default with the `smntcs_admin_bar_toggle_default_shortcut` filter

## Installation

1. Upload `smntcs-admin-bar-toggle` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Visit the frontend while logged in and press Cmd+B (Mac) or Ctrl+B (Windows, Linux).
4. Optionally, choose a different shortcut under `Users » Profile » Toolbar shortcut`.

## Changing the default shortcut

```php
add_filter( 'smntcs_admin_bar_toggle_default_shortcut', fn() => 'Mod+Shift+KeyA' );
```

Shortcuts are written as optional modifiers in the order `Mod`, `Ctrl`, `Meta`, `Alt`, `Shift`, followed by a [`KeyboardEvent.code`](https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/code) value. `Mod` means Cmd on macOS and Ctrl everywhere else. At least one of `Mod`, `Ctrl`, `Meta` or `Alt` is required.

## Plugin page

You can find the plugin on [WordPress.org](https://wordpress.org/plugins/smntcs-admin-bar-toggle/).

## Changelog

### 1.0 (2026.11.15)

- Initial release
