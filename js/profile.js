/**
 * Shortcut recorder on the profile screen.
 *
 * @package SMNTCS_Admin_Bar_Toggle
 */

/* global smntcsAdminBarToggleConfig */
( function () {
	var abt = window.smntcsAdminBarToggle;
	var display = document.getElementById( 'smntcs-admin-bar-toggle-display' );
	var field = document.getElementById( 'smntcs-admin-bar-toggle-shortcut' );
	var reset = document.getElementById( 'smntcs-admin-bar-toggle-reset' );
	var fallback = smntcsAdminBarToggleConfig.defaultShortcut;
	var suffix = smntcsAdminBarToggleConfig.defaultSuffix;

	if ( ! display || ! field || ! reset ) {
		return;
	}

	function render() {
		display.value =
			abt.label( field.value || fallback ) +
			( field.value ? '' : ' ' + suffix );
		reset.hidden = ! field.value;
	}

	display.addEventListener( 'keydown', function ( e ) {
		var shortcut;

		if ( 'Tab' === e.key && ! e.ctrlKey && ! e.metaKey && ! e.altKey ) {
			return;
		}
		e.preventDefault();
		if ( 'Escape' === e.key ) {
			display.blur();
			return;
		}
		if (
			( 'Backspace' === e.key || 'Delete' === e.key ) &&
			! e.ctrlKey &&
			! e.metaKey &&
			! e.altKey
		) {
			field.value = '';
			render();
			return;
		}
		shortcut = abt.fromEvent( e );
		if ( shortcut ) {
			field.value = shortcut;
			render();
		}
	} );

	reset.addEventListener( 'click', function () {
		field.value = '';
		render();
	} );

	render();
} )();
