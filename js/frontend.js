/**
 * Toggle the admin bar on the frontend with the configured shortcut.
 *
 * Loaded in the head so a stored "hidden" state is applied before the page renders.
 *
 * @package SMNTCS_Admin_Bar_Toggle
 */

/* global smntcsAdminBarToggleConfig */
( function () {
	var root = document.documentElement;
	var key = 'smntcs-admin-bar-hidden';
	var cls = 'smntcs-admin-bar-hidden';
	var shortcut = smntcsAdminBarToggleConfig.shortcut;

	function syncBodyClass() {
		if ( document.body ) {
			document.body.classList.toggle(
				'admin-bar',
				! root.classList.contains( cls )
			);
		}
	}

	try {
		if ( window.localStorage.getItem( key ) === '1' ) {
			root.classList.add( cls );
		}
	} catch ( err ) {}

	// Fallback for themes that don't call wp_body_open().
	document.addEventListener( 'DOMContentLoaded', syncBodyClass );

	document.addEventListener( 'keydown', function ( e ) {
		var t = e.target;
		var hidden;

		if ( ! window.smntcsAdminBarToggle.matches( e, shortcut ) ) {
			return;
		}

		// Keep the shortcut's native meaning (e.g. bold) in form fields and editable areas.
		if (
			t &&
			( t.isContentEditable ||
				/^(INPUT|TEXTAREA|SELECT)$/.test( t.tagName ) )
		) {
			return;
		}

		e.preventDefault();

		hidden = root.classList.toggle( cls );
		syncBodyClass();

		try {
			window.localStorage.setItem( key, hidden ? '1' : '0' );
		} catch ( err ) {}
	} );
} )();
