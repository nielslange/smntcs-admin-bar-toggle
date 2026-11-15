=== SMNTCS Admin Bar Toggle ===

Contributors:       nielslange
Tags:               admin bar, toolbar, keyboard shortcut, hide admin bar, frontend
Requires at least:  5.7
Tested up to:       7.1
Requires PHP:       7.4
Stable tag:         1.0
License:            GPL v2 or later
License URI:        https://www.gnu.org/licenses/gpl-2.0.html

Show or hide the admin bar on the frontend with a keyboard shortcut, without leaving a gap at the top of the page.

== Description ==

SMNTCS Admin Bar Toggle lets logged-in users hide the admin bar while they look at the frontend, so they see the site the way visitors do. Press Cmd+B on a Mac or Ctrl+B on Windows and Linux to hide the admin bar, and press it again to bring it back. The page moves up with it, so no empty gap is left behind.

The choice is remembered in the browser across page loads. Every user can pick a different shortcut under Users → Profile → Toolbar shortcut. The shortcut is ignored while you type in a form field, so Cmd/Ctrl+B still makes text bold there.

= Features =

* Hide and show the admin bar on the frontend with one key combination
* No gap: the page and the theme's admin bar offsets move up as well
* Remembers the state between page loads
* Personal shortcut per user, recorded on the profile screen
* Developers can change the site-wide default with the `smntcs_admin_bar_toggle_default_shortcut` filter

== Installation ==

1. Upload `smntcs-admin-bar-toggle` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Visit the frontend while logged in and press Cmd+B (Mac) or Ctrl+B (Windows, Linux).
4. Optionally, choose a different shortcut under `Users » Profile » Toolbar shortcut`.

== Frequently Asked Questions ==

= Which shortcuts can I use? =

Any letter, digit, F1–F12 or punctuation key combined with Cmd/Ctrl or Alt, optionally with Shift. Shortcuts the browser reserves for itself, such as Cmd/Ctrl+W, T or N, cannot be used.

= How do I change the default shortcut for all users? =

Use the `smntcs_admin_bar_toggle_default_shortcut` filter, for example `add_filter( 'smntcs_admin_bar_toggle_default_shortcut', fn() => 'Mod+Shift+KeyA' );`. `Mod` stands for Cmd on macOS and Ctrl elsewhere. Users who picked their own shortcut keep it.

= Does the plugin store any data? =

A personal shortcut is stored as user meta and removed when you delete the plugin. Whether the admin bar is hidden is stored in the browser's local storage only.

== Changelog ==

= 1.0 (2026.11.15) =

- Initial release
