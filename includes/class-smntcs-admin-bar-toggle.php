<?php
/**
 * SMNTCS Admin Bar Toggle main class.
 *
 * @package SMNTCS_Admin_Bar_Toggle
 */

declare(strict_types=1);

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * SMNTCS_Admin_Bar_Toggle class
 *
 * Shortcuts are stored as "Mod+Shift+KeyB": optional modifiers in the fixed order
 * Mod, Ctrl, Meta, Alt, Shift, followed by a KeyboardEvent.code value.
 * "Mod" means Cmd on macOS and Ctrl everywhere else.
 *
 * @since 1.0
 */
class SMNTCS_Admin_Bar_Toggle {

	/**
	 * User meta key for the personal shortcut.
	 *
	 * @since 1.0
	 * @var string
	 */
	const META_KEY = 'smntcs_admin_bar_toggle_shortcut';

	/**
	 * Built-in default shortcut.
	 *
	 * @since 1.0
	 * @var string
	 */
	const DEFAULT_SHORTCUT = 'Mod+KeyB';

	/**
	 * Allowed shortcut format (shared by PHP and JS, so no delimiters).
	 *
	 * @since 1.0
	 * @var string
	 */
	const PATTERN = '^(?=.*(?:Mod|Ctrl|Meta|Alt)\+)(?:Mod\+)?(?:Ctrl\+)?(?:Meta\+)?(?:Alt\+)?(?:Shift\+)?(?:Key[A-Z]|Digit[0-9]|F[1-9]|F1[0-2]|Backquote|Minus|Equal|BracketLeft|BracketRight|Backslash|Semicolon|Quote|Comma|Period|Slash)$';

	/**
	 * Script and style handle prefix.
	 *
	 * @since 1.0
	 * @var string
	 */
	const HANDLE = 'smntcs-admin-bar-toggle';

	/**
	 * CSS class set on <html> while the admin bar is hidden.
	 *
	 * @since 1.0
	 * @var string
	 */
	const HIDDEN_CLASS = 'smntcs-admin-bar-hidden';

	/**
	 * Initialize the class and register hooks.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ], 10, 0 );
		add_action( 'wp_body_open', [ $this, 'print_body_script' ], 0, 0 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_profile_assets' ] );
		add_action( 'personal_options', [ $this, 'render_profile_field' ] );
		add_action( 'personal_options_update', [ $this, 'save_profile_field' ] );
		add_action( 'edit_user_profile_update', [ $this, 'save_profile_field' ] );
	}

	/**
	 * Whether the toggle should be active on the current request.
	 *
	 * @since 1.0
	 * @return bool
	 */
	private function is_enabled(): bool {
		return ! is_admin() && is_admin_bar_showing();
	}

	/**
	 * Check whether a shortcut string has the expected format.
	 *
	 * @since 1.0
	 * @param string $shortcut The shortcut, e.g. "Mod+Shift+KeyB".
	 * @return bool
	 */
	private function is_valid_shortcut( string $shortcut ): bool {
		return 1 === preg_match( '/' . self::PATTERN . '/', $shortcut );
	}

	/**
	 * Get the site-wide default shortcut.
	 *
	 * @since 1.0
	 * @return string
	 */
	private function get_default_shortcut(): string {
		/**
		 * Filters the default shortcut used when a user hasn't set their own.
		 *
		 * @since 1.0
		 * @param string $shortcut Default shortcut, e.g. "Mod+KeyB" or "Ctrl+Alt+KeyA".
		 */
		$shortcut = (string) apply_filters( 'smntcs_admin_bar_toggle_default_shortcut', self::DEFAULT_SHORTCUT );

		return $this->is_valid_shortcut( $shortcut ) ? $shortcut : self::DEFAULT_SHORTCUT;
	}

	/**
	 * Get the shortcut a user saved, or an empty string if none.
	 *
	 * @since 1.0
	 * @param int $user_id The user ID.
	 * @return string
	 */
	private function get_user_shortcut( int $user_id ): string {
		$shortcut = (string) get_user_meta( $user_id, self::META_KEY, true );

		return $this->is_valid_shortcut( $shortcut ) ? $shortcut : '';
	}

	/**
	 * Register a script from the plugin's js/ directory.
	 *
	 * The file's modification time is used as version, so browsers fetch a fresh
	 * copy after every update.
	 *
	 * @since 1.0
	 * @param string   $handle    Script handle.
	 * @param string   $file      File name inside js/.
	 * @param string[] $deps      Script dependencies.
	 * @param bool     $in_footer Whether to load the script in the footer.
	 * @return void
	 */
	private function register_script( string $handle, string $file, array $deps, bool $in_footer ): void {
		$path = plugin_dir_path( SMNTCS_ADMIN_BAR_TOGGLE_PLUGIN_FILE ) . 'js/' . $file;

		wp_register_script(
			$handle,
			plugins_url( 'js/' . $file, SMNTCS_ADMIN_BAR_TOGGLE_PLUGIN_FILE ),
			$deps,
			(string) filemtime( $path ),
			$in_footer
		);
	}

	/**
	 * Register the shared shortcut helpers and pass them their configuration.
	 *
	 * @since 1.0
	 * @param array<string, string> $config Values exposed as smntcsAdminBarToggleConfig.
	 * @param bool                  $in_footer Whether to load the script in the footer.
	 * @return string The shared script handle.
	 */
	private function register_shared_script( array $config, bool $in_footer ): string {
		$handle = self::HANDLE . '-shortcut';

		$this->register_script( $handle, 'shortcut.js', [], $in_footer );

		$config['pattern'] = self::PATTERN;

		wp_add_inline_script(
			$handle,
			'var smntcsAdminBarToggleConfig = ' . wp_json_encode( $config, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE ) . ';',
			'before'
		);

		return $handle;
	}

	/**
	 * Enqueue the CSS and the keyboard handler on the frontend.
	 *
	 * The script loads in the head (not deferred) so a stored "hidden" state is
	 * applied before the page renders.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function enqueue_frontend_assets(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		$shortcut = $this->get_user_shortcut( get_current_user_id() );
		$shortcut = '' !== $shortcut ? $shortcut : $this->get_default_shortcut();

		// Neutralize the margin bump, the admin bar height variable and the scroll padding.
		// Attached to core's admin bar stylesheet, which loads whenever the admin bar shows.
		wp_add_inline_style(
			'admin-bar',
			'html.' . self::HIDDEN_CLASS . '{margin-top:0!important;scroll-padding-top:0!important;--wp-admin--admin-bar--height:0px;}'
			. 'html.' . self::HIDDEN_CLASS . ' #wpadminbar{display:none!important;}'
		);

		$shared = $this->register_shared_script( [ 'shortcut' => $shortcut ], false );

		$this->register_script( self::HANDLE, 'frontend.js', [ $shared ], false );
		wp_enqueue_script( self::HANDLE );
	}

	/**
	 * Remove the admin-bar body class right after <body> opens to avoid a layout jump.
	 *
	 * Themes style `body.admin-bar`, so this has to run before the body content
	 * renders. Themes without wp_body_open() fall back to DOMContentLoaded.
	 *
	 * @since 1.0
	 * @return void
	 */
	public function print_body_script(): void {
		if ( ! $this->is_enabled() ) {
			return;
		}

		wp_print_inline_script_tag(
			"if(document.documentElement.classList.contains('" . self::HIDDEN_CLASS . "')){document.body.classList.remove('admin-bar');}",
			[ 'id' => self::HANDLE . '-body-js' ]
		);
	}

	/**
	 * Enqueue the shortcut recorder on the profile screens.
	 *
	 * @since 1.0
	 * @param string $hook_suffix The current admin page.
	 * @return void
	 */
	public function enqueue_profile_assets( $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, [ 'profile.php', 'user-edit.php' ], true ) ) {
			return;
		}

		$shared = $this->register_shared_script(
			[
				'defaultShortcut' => $this->get_default_shortcut(),
				'defaultSuffix'   => __( '(default)', 'smntcs-admin-bar-toggle' ),
			],
			true
		);

		$this->register_script( self::HANDLE . '-profile', 'profile.js', [ $shared ], true );
		wp_enqueue_script( self::HANDLE . '-profile' );
	}

	/**
	 * Render the shortcut recorder below the "Toolbar" option on the profile screen.
	 *
	 * @since 1.0
	 * @param WP_User $user The user being edited.
	 * @return void
	 */
	public function render_profile_field( WP_User $user ): void {
		$value = $this->get_user_shortcut( $user->ID );
		?>
		<tr class="smntcs-admin-bar-toggle-wrap">
			<th scope="row"><label for="smntcs-admin-bar-toggle-display"><?php esc_html_e( 'Toolbar shortcut', 'smntcs-admin-bar-toggle' ); ?></label></th>
			<td>
				<input type="text" id="smntcs-admin-bar-toggle-display" class="regular-text code" readonly autocomplete="off" style="max-width:12em" aria-describedby="smntcs-admin-bar-toggle-description" />
				<input type="hidden" name="smntcs_admin_bar_toggle_shortcut" id="smntcs-admin-bar-toggle-shortcut" value="<?php echo esc_attr( $value ); ?>" />
				<button type="button" class="button" id="smntcs-admin-bar-toggle-reset" hidden><?php esc_html_e( 'Reset to default', 'smntcs-admin-bar-toggle' ); ?></button>
				<p class="description" id="smntcs-admin-bar-toggle-description">
					<?php esc_html_e( 'Shortcut to show or hide the toolbar on the frontend. Click the field and press a key combination that includes Cmd/Ctrl or Alt. Backspace resets it to the default. Browser-reserved shortcuts such as Cmd/Ctrl+W, T or N will not work.', 'smntcs-admin-bar-toggle' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the shortcut from the profile screen.
	 *
	 * @since 1.0
	 * @param int|string $user_id The user ID.
	 * @return void
	 */
	public function save_profile_field( $user_id ): void {
		$user_id = (int) $user_id;

		check_admin_referer( 'update-user_' . $user_id );

		if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['smntcs_admin_bar_toggle_shortcut'] ) ) {
			return;
		}

		$shortcut = sanitize_text_field( wp_unslash( $_POST['smntcs_admin_bar_toggle_shortcut'] ) );

		if ( '' === $shortcut ) {
			delete_user_meta( $user_id, self::META_KEY );
		} elseif ( $this->is_valid_shortcut( $shortcut ) ) {
			update_user_meta( $user_id, self::META_KEY, $shortcut );
		}
	}

	/**
	 * Remove all stored shortcuts on uninstall.
	 *
	 * @since 1.0
	 * @return void
	 */
	public static function uninstall(): void {
		delete_metadata( 'user', 0, self::META_KEY, '', true );
	}
}
