/**
 * An object containing the data to help create a portlet.
 *
 * @typedef {Object} Hint
 * @property {string} type
 */

/**
 * Creates default portlet.
 *
 * @param {Element} portlet
 * @return {Element}
 */
function addDefaultPortlet( portlet ) {
	portlet.classList.add( 'cframe-menu' );
	const ul = portlet.querySelector( 'ul' );
	if ( !ul ) {
		return portlet;
	}
	ul.classList.add( 'cframe-menu-content-list' );
	const label = portlet.querySelector( 'label' );
	const labelId = `${ portlet.id }-label`;
	if ( label ) {
		const labelDiv = document.createElement( 'div' );
		labelDiv.id = labelId;
		labelDiv.classList.add( 'cframe-menu-heading' );
		labelDiv.textContent = label.textContent || '';
		portlet.insertBefore( labelDiv, label );
		label.remove();
		portlet.setAttribute( 'aria-labelledby', labelId );
		portlet.setAttribute( 'role', 'navigation' );
	}
	let wrapper = portlet.querySelector( 'div:last-child' );
	if ( wrapper ) {
		ul.remove();
		wrapper.appendChild( ul );
		wrapper.classList.add( 'cframe-menu-content' );
	} else {
		wrapper = document.createElement( 'div' );
		wrapper.classList.add( 'cframe-menu-content' );
		ul.remove();
		wrapper.appendChild( ul );
		portlet.appendChild( wrapper );
	}
	// menus inside the legacy sidebar should have cframe-menu-portal class.
	if ( portlet.closest( '.cframe-sidebar' ) ) {
		portlet.classList.add( 'cframe-menu-portal' );
	}

	return portlet;
}

/**
 * A hook handler for util.addPortlet hook.
 * It creates a portlet based on the hint, and adabt it to cframe skin.
 *
 * @param {Element} portlet
 * @return {Element}
 */
function addPortletHandler( portlet ) {
	const parent = /** @type {HTMLElement} */( portlet.parentNode );
	// Note if there is a parent, it's been appended to the DOM so we can determine
	// its type.
	if ( parent && parent.id === 'right-navigation' ) {
		const id = portlet.id;
		portlet.classList.add( 'cframe-menu-dropdown' );
		const checkbox = document.createElement( 'input' );
		checkbox.type = 'checkbox';
		checkbox.id = `${ id }-checkbox`;
		checkbox.setAttribute( 'role', 'button' );
		checkbox.setAttribute( 'aria-haspopup', 'true' );
		checkbox.setAttribute( 'data-event-name', `ui.dropdown-${ id }` );
		checkbox.setAttribute( 'class', 'cframe-menu-checkbox' );
		checkbox.setAttribute( 'aria-labelledby', `${ id }-label` );
		portlet.prepend( checkbox );
	}
	portlet.classList.remove( 'mw-portlet-js' );
	return addDefaultPortlet( portlet );
}

/**
 * @return {{addPortletHandler: (function(Element): Element)}}
 */
function main() {
	mw.hook( 'util.addPortlet' ).add( addPortletHandler );
	// Update any portlets that were created prior to the hook being registered.
	document.querySelectorAll( '.mw-portlet-js' ).forEach( addPortletHandler );
	return {
		addPortletHandler
	};
}

module.exports = {
	main, addPortletHandler
};
