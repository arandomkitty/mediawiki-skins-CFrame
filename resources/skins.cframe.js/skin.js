/* eslint-disable no-jquery/no-jquery-constructor */
/** @interface MediaWikiPageReadyModule */
const
	collapsibleTabs = require( './collapsibleTabs.js' ),
	/** @type {MediaWikiPageReadyModule} */
	pageReady = require( /** @type {string} */( 'mediawiki.page.ready' ) ),
	portlets = require( './portlets.js' ),
	cframe = require( './cframe.js' ),
	teleportTarget = /** @type {HTMLElement} */require( /** @type {string} */ ( 'mediawiki.page.ready' ) ).teleportTarget;

function main() {
	collapsibleTabs.init();
	$( cframe.init );
	portlets.main();
	pageReady.loadSearchModule( 'mediawiki.searchSuggest' );
	teleportTarget.classList.add( 'cframe-body' );

	document.documentElement.classList.add('skin-theme-clientpref-night');

	document.querySelector('#mobileDropdown').addEventListener("click", (event) => event.stopPropagation());

	const collapse = document.querySelector("#panelCollapse");
	let sidebar = document.querySelector("#mw-panel");
	const date = new Date();
	date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000);

	const expires = date.toUTCString();
	sidebar.before(collapse);
	sidebar = document.getElementById("mw-panel");
	console.log(document.cookie);
	let state;
	for (let index = 0; index < document.cookie.split(";").length; index++) {
		if (document.cookie.split(";")[index].startsWith(" sideNavCollapse")) {
			state = JSON.parse(
				document.cookie.split(";")[index].replace(" sideNavCollapse=", "")
			);
		} else if (
			document.cookie.split(";")[index].startsWith("sideNavCollapse")
		) {
			state = JSON.parse(
				document.cookie.split(";")[index].replace("sideNavCollapse=", "")
			);
		}
	}

	if (screen.width <= 980 && state == null) {
		document.cookie = "sideNavCollapse=true; expires=" + expires + ";path=/";
		state = true;
	} else if (state == null) {
		document.cookie = "sideNavCollapse=false; expires=" + expires + ";path=/";
		state = false;
	}

	if (state == true) {
		sidebar.setAttribute("class", "cframe-sidebar collapsed");
		document.body.classList.add("defaultCollapsed");
	} else if (screen.width <= 980) {
		sidebar.setAttribute("class", "cframe-sidebar collapsed");
		document.body.classList.add("defaultCollapsed");
		state = true;
	} else if (state == false) {
		sidebar.setAttribute("class", "cframe-sidebar expanded");
		document.body.classList.add("defaultExpanded");
	}

	collapse.addEventListener("click", () => {
		if (screen.width > 980) {
			document.cookie = "sideNavCollapse=" + !state + "; expires=" + expires + ";path=/";
		}
		state = !state;
		if (state == true) {
			document.querySelector("#mw-panel")
			.setAttribute("class", "cframe-sidebar collapsed");
		} else if (state == false) {
			document.querySelector("#mw-panel")
			.setAttribute("class", "cframe-sidebar expanded");
		}
	});

	let mobileState = false;

	document.querySelector('#panelCollapseMobile').addEventListener("click", () => {
		mobileState = !mobileState;
		if (mobileState == false) {
			mobileDropdown.setAttribute("class", "collapsed");
		} else if (mobileState == true) {
			mobileDropdown.setAttribute("class", "expanded");
		}
	});

	const remove = document.querySelectorAll('#mobileDropdown a[accesskey]');

	for (let elementToBeRemoved = 0; elementToBeRemoved < remove.length; elementToBeRemoved++) {
		remove[elementToBeRemoved].removeAttribute('accesskey');
		remove[elementToBeRemoved].setAttribute('title', remove[elementToBeRemoved].getAttribute('title').replace(/ \[.*?\]/g, ''));
	}
}

main();
