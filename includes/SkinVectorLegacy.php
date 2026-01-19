<?php

namespace MediaWiki\Skins\Vector;

use MediaWiki\Languages\LanguageConverterFactory;
use MediaWiki\Skin\SkinMustache;
use MediaWiki\Skin\SkinTemplate;
use MediaWiki\Skins\Vector\Components\VectorComponentSearchBox;
use MediaWiki\Skins\Vector\Components\VectorComponentVariants;

/**
 * @ingroup Skins
 * @package Vector
 * @internal
 */
class SkinVectorLegacy extends SkinMustache {
	/** @var int */
	private const MENU_TYPE_DEFAULT = 0;
	/** @var int */
	private const MENU_TYPE_TABS = 1;
	/** @var int */
	private const MENU_TYPE_DROPDOWN = 2;
	private const MENU_TYPE_PORTAL = 3;

	private LanguageConverterFactory $languageConverterFactory;

	public function __construct(
		LanguageConverterFactory $languageConverterFactory,
		array $options
	) {
		parent::__construct( $options );
		$this->languageConverterFactory = $languageConverterFactory;
	}

	/**
	 * @inheritDoc
	 */
	protected function runOnSkinTemplateNavigationHooks( SkinTemplate $skin, &$content_navigation ) {
		parent::runOnSkinTemplateNavigationHooks( $skin, $content_navigation );
		Hooks::onSkinTemplateNavigation( $skin, $content_navigation );
	}

	/**
	 * Performs updates to all portlets.
	 *
	 * @param array $data
	 * @return array
	 */
	private function decoratePortletsData( array $data ) {
		foreach ( $data['data-portlets'] as $key => $pData ) {
			$data['data-portlets'][$key] = $this->decoratePortletData(
				$key,
				$pData
			);
		}
		$mainMenuData = $data['data-portlets-sidebar'];
		$mainMenuData['data-portlets-first'] = $this->decoratePortletData(
			'navigation', $mainMenuData['data-portlets-first']
		);
		$rest = $mainMenuData['array-portlets-rest'];
		foreach ( $rest as $key => $pData ) {
			$rest[$key] = $this->decoratePortletData(
				$pData['id'], $pData
			);
		}
		$mainMenuData['array-portlets-rest'] = $rest;
		$data['data-portlets-main-menu'] = $mainMenuData;
		return $data;
	}

	/**
	 * Performs the following updates to portlet data:
	 * - Adds concept of menu types
	 * - Marks the selected variant in the variant portlet
	 * - modifies tooltips of personal and user-menu portlets
	 * @param string $key
	 * @param array $portletData
	 * @return array
	 */
	private function decoratePortletData(
		string $key,
		array $portletData
	): array {
		$isIconDropdown = false;
		switch ( $key ) {
			case 'data-user-menu':
				$type = self::MENU_TYPE_DROPDOWN;
				$isIconDropdown = true;
				break;
			case 'data-actions':
			case 'data-variants':
			case 'data-sticky-header-toc':
				$type = self::MENU_TYPE_DROPDOWN;
				break;
			case 'data-views':
			case 'data-associated-pages':
			case 'data-namespaces':
				$type = self::MENU_TYPE_TABS;
				break;
			case 'data-notifications':
			case 'data-personal':
			case 'data-user-page':
			case 'data-vector-user-menu-overflow':
				$type = self::MENU_TYPE_DEFAULT;
				break;
			default:
				$type = self::MENU_TYPE_PORTAL;
				break;
		}

		if ( $key === 'data-personal' ) {
			// Set tooltip to empty string for the personal menu for both logged-in and logged-out users
			// to avoid showing the tooltip for legacy version.
			$portletData['html-items'] = preg_replace('/<li id="pt-notif.*?<\/li>/', '', $portletData['html-items']);
			$portletData['html-items'] = preg_replace('/(pt-userpage.*?<span>).*?(?=<\/span>)/', '$1User Page', $portletData['html-items']);
			array_splice($portletData['array-items'], 1, 2);
			$portletData['html-tooltip'] = '';
			$portletData['class'] .= ' vector-user-menu-legacy';
		}

		if ( $key === 'data-notifications') {
			$portletData['array-items'] = array_reverse($portletData['array-items']);
			preg_match_all('/<li id="pt-notif.*?<\/li>/', $portletData['html-items'], $matches, PREG_SET_ORDER);
			$portletData['html-items'] = $matches[1][0] . $matches[0][0];
		}

		// Special casing for Variant to change label to selected.
		// Hopefully we can revisit and possibly remove this code when the language switcher is moved.
		if ( $key === 'data-variants' ) {
			$variant = new VectorComponentVariants(
				$this->languageConverterFactory,
				$portletData,
				$this->getTitle()->getPageLanguage(),
				$this->msg( 'vector-language-variant-switcher-label' )
			);
			$portletData[ 'label' ] = $variant->getTemplateData()[ 'data-variants-dropdown' ][ 'label' ];
		}

		$portletData = $this->updatePortletClasses(
			$portletData,
			$type
		);

		return $portletData + [
			'is-dropdown' => $type === self::MENU_TYPE_DROPDOWN,
			'is-portal' => $type === self::MENU_TYPE_PORTAL,
		];
	}

	/**
	 * Helper for applying Vector menu classes to portlets
	 *
	 * @param array $portletData returned by SkinMustache to decorate
	 * @param int $type representing one of the menu types (see MENU_TYPE_* constants)
	 * @return array modified version of portletData input
	 */
	private function updatePortletClasses(
		array $portletData,
		int $type = self::MENU_TYPE_DEFAULT
	) {
		$extraClasses = [
			self::MENU_TYPE_DROPDOWN => 'vector-menu-dropdown',
			self::MENU_TYPE_TABS => 'vector-menu-tabs vector-menu-tabs-legacy',
			self::MENU_TYPE_PORTAL => 'vector-menu-portal portal',
			self::MENU_TYPE_DEFAULT => '',
		];
		$portletData['class'] .= ' ' . $extraClasses[$type];

		if ( !isset( $portletData['heading-class'] ) ) {
			$portletData['heading-class'] = '';
		}

		$portletData['class'] = trim( $portletData['class'] );
		$portletData['heading-class'] = trim( $portletData['heading-class'] );
		return $portletData;
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplateData(): array {
		$parentData = $this->decoratePortletsData( parent::getTemplateData() );

		$parentData['data-portlets']['data-logout'] = end($parentData['data-portlets']['data-user-menu']['array-items']);
		$parentData['data-portlets']['data-logout']['html-item'] = preg_replace('/((?<=<)li(?= id))|((?<=<\/)li(?=>))/', 'div', $parentData['data-portlets']['data-logout']['html-item']);

		$parentData['data-portlets']['data-ca-logout'] = end($parentData['data-portlets']['data-user-menu']['array-items']);
		$parentData['data-portlets']['data-ca-logout']['html-item'] = preg_replace('/Log out/', 'Logout', $parentData['data-portlets']['data-ca-logout']['html-item']);
		$parentData['data-portlets']['data-ca-logout']['html-item'] = preg_replace('/pt-logout/', 'ca-logout', $parentData['data-portlets']['data-ca-logout']['html-item']);

		$parentData['data-portlets']['data-login'] = $parentData['data-portlets']['data-user-menu']['array-items'][4];
		$parentData['data-portlets']['data-login']['html-item'] = preg_replace('/((?<=<)li(?= id))|((?<=<\/)li(?=>))/', 'span', $parentData['data-portlets']['data-login']['html-item']);

		$parentData['data-portlets']['data-ca-login'] = $parentData['data-portlets']['data-user-menu']['array-items'][4];
		$parentData['data-portlets']['data-ca-login']['html-item'] = preg_replace('/Log in/', 'Login', $parentData['data-portlets']['data-ca-login']['html-item']);
		$parentData['data-portlets']['data-ca-login']['html-item'] = preg_replace('/pt-login/', 'ca-login', $parentData['data-portlets']['data-ca-login']['html-item']);

		$parentData['data-portlets']['data-create'] = $parentData['data-portlets']['data-user-menu']['array-items'][3];
		$parentData['data-portlets']['data-create']['html-item'] = preg_replace('/((?<=<)li(?= id))|((?<=<\/)li(?=>))/', 'span', $parentData['data-portlets']['data-create']['html-item']);

		$parentData['data-portlets']['data-namespaces']['html-items'] = preg_replace('/Main Page/', 'Page', $parentData['data-portlets']['data-namespaces']['html-items']);

		$parentData['data-portlets']['data-actions']['html-items'] = $parentData['data-portlets']['data-actions']['html-items'] . preg_replace('/pt-logout/', 'ca-logout', $parentData['data-portlets']['data-logout']['html-item']);

		$parentData['data-portlets']['data-mobile-personal'] = $parentData['data-portlets']['data-personal'];
		$parentData['data-portlets']['data-mobile-personal'] = preg_replace('/<li id="pt-logout".*?<\/li>/', '', $parentData['data-portlets']['data-mobile-personal']);

		/*Create MobileNavActions*/
		$parentData['data-portlets']['data-mobile-actions'] = $parentData['data-portlets']['data-actions'];
		$parentData['data-portlets']['data-mobile-actions']['html-items'] = preg_replace('/((?<=<)div(?= id))|((?<=<\/)div(?=>))/', 'li', $parentData['data-portlets']['data-mobile-actions']['html-items']);
		$parentData['data-portlets']['data-mobile-actions']['html-items'] = preg_replace('/Log out/', 'Logout', $parentData['data-portlets']['data-mobile-actions']['html-items']);
		$parentData['data-portlets']['data-mobile-actions']['html-items'] = preg_replace('/Log in/', 'Login', $parentData['data-portlets']['data-mobile-actions']['html-items']);

		/*Create MobileNavCreate*/

		$parentData['data-portlets']['data-mobile-create']['html-item'] = preg_replace('/<span id="pt-create/', '<li id="pt-create', $parentData['data-portlets']['data-create']['html-item']);
		$parentData['data-portlets']['data-mobile-create']['html-item'] = preg_replace('/<\/span>$/', '</li>', $parentData['data-portlets']['data-mobile-create']['html-item']);

		$parentData['data-portlets']['data-mobile-tb'] = $parentData['data-portlets-sidebar']['array-portlets-rest'][array_search('p-tb', array_column($parentData['data-portlets-sidebar']['array-portlets-rest'], 'id'))];

		$parentData['data-toplinks'] = [];

		$this->addToSidebar($parentData['data-toplinks'], 'toplinks');
		foreach ($parentData['data-toplinks']['topnav'] as $x => $y) {
			$parentData['data-toplinks']['topnav'][$x]['id'] = preg_replace('/^n-/', 'topnav-', $y['id']);
		}
		$parentData['data-toplinks']['id'] = 'p-toplinks';
		$parentData['data-toplinks']['class'] = "mw-portlet mw-portlet-personal vector-user-menu-legacy";

		$components = [
			'data-search-box' => new VectorComponentSearchBox(
				$parentData['data-search-box'],
				false,
				// is primary mode of search
				true,
				'searchform',
				//json_encode($parentData),
				//json_encode($parentData),
				true,
				$this->getConfig(),
				Constants::SEARCH_BOX_INPUT_LOCATION_DEFAULT,
				$this->getContext()
			),
		];
		foreach ( $components as $key => $component ) {
			$parentData[$key] = $component->getTemplateData();
		}

		// SkinVector sometimes serves new Vector as part of removing the
		// skin version user preference. To avoid T302461 we need to unset it here.
		// This shouldn't be run on SkinVector22.
		unset( $parentData['data-toc'] );
		return $parentData;
	}
}
