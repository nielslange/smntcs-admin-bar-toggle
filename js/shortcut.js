/**
 * Shortcut helpers shared by the frontend handler and the profile recorder.
 *
 * Shortcuts are stored as "Mod+Shift+KeyB": optional modifiers in the fixed order
 * Mod, Ctrl, Meta, Alt, Shift, followed by a KeyboardEvent.code value.
 * "Mod" means Cmd on macOS and Ctrl everywhere else.
 *
 * @package SMNTCS_Admin_Bar_Toggle
 */

/* global smntcsAdminBarToggleConfig */
window.smntcsAdminBarToggle =
	window.smntcsAdminBarToggle ||
	( function () {
		var platform =
			( navigator.userAgentData && navigator.userAgentData.platform ) ||
			navigator.platform ||
			'';
		var isMac = /mac|iphone|ipad/i.test( platform );
		var pattern = new RegExp( smntcsAdminBarToggleConfig.pattern );
		var symbols = {
			Backquote: '`',
			Minus: '-',
			Equal: '=',
			BracketLeft: '[',
			BracketRight: ']',
			Backslash: '\\',
			Semicolon: ';',
			Quote: "'",
			Comma: ',',
			Period: '.',
			Slash: '/'
		};

		function parse( shortcut ) {
			var parts = shortcut.split( '+' );
			return { code: parts.pop(), mods: parts };
		}

		function isValid( shortcut ) {
			return pattern.test( shortcut );
		}

		function matches( e, shortcut ) {
			var p = parse( shortcut );
			var x = { ctrl: false, meta: false, alt: false, shift: false };
			p.mods.forEach( function ( m ) {
				if ( 'Mod' === m ) {
					x[ isMac ? 'meta' : 'ctrl' ] = true;
				}
				if ( 'Ctrl' === m ) {
					x.ctrl = true;
				}
				if ( 'Meta' === m ) {
					x.meta = true;
				}
				if ( 'Alt' === m ) {
					x.alt = true;
				}
				if ( 'Shift' === m ) {
					x.shift = true;
				}
			} );
			return (
				e.code === p.code &&
				e.ctrlKey === x.ctrl &&
				e.metaKey === x.meta &&
				e.altKey === x.alt &&
				e.shiftKey === x.shift
			);
		}

		function fromEvent( e ) {
			var mods = [];
			var shortcut;

			if ( /^(Shift|Control|Alt|Meta|OS)(Left|Right)?$/.test( e.code ) ) {
				return null;
			}
			if ( isMac ) {
				if ( e.metaKey ) {
					mods.push( 'Mod' );
				}
				if ( e.ctrlKey ) {
					mods.push( 'Ctrl' );
				}
			} else {
				if ( e.ctrlKey ) {
					mods.push( 'Mod' );
				}
				if ( e.metaKey ) {
					mods.push( 'Meta' );
				}
			}
			if ( e.altKey ) {
				mods.push( 'Alt' );
			}
			if ( e.shiftKey ) {
				mods.push( 'Shift' );
			}
			shortcut = mods.concat( e.code ).join( '+' );
			return isValid( shortcut ) ? shortcut : null;
		}

		function label( shortcut ) {
			var p = parse( shortcut );
			var names = isMac
				? { Mod: '⌘', Ctrl: '⌃', Meta: '⌘', Alt: '⌥', Shift: '⇧' }
				: {
						Mod: 'Ctrl',
						Ctrl: 'Ctrl',
						Meta: 'Win',
						Alt: 'Alt',
						Shift: 'Shift'
				  };
			var key = symbols[ p.code ] || p.code.replace( /^(Key|Digit)/, '' );
			var mods = p.mods.map( function ( m ) {
				return names[ m ];
			} );
			return isMac ? mods.join( '' ) + key : mods.concat( key ).join( '+' );
		}

		return {
			isValid: isValid,
			matches: matches,
			fromEvent: fromEvent,
			label: label
		};
	} )();
