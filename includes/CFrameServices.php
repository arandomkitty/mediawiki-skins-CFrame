<?php

namespace MediaWiki\Skins\CFrame;

use MediaWiki\Skins\CFrame\Services\LanguageService;

/**
 * A service locator for services specific to CFrame.
 *
 * @package CFrame
 * @internal
 */
final class VectorServices {

	/**
	 * Gets the language service.
	 *
	 * @return LanguageService
	 */
	public static function getLanguageService(): LanguageService {
		return new LanguageService();
	}
}
