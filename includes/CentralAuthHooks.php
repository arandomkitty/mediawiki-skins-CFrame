<?php

namespace MediaWiki\Skins\CFrame;

use MediaWiki\Extension\CentralAuth\Hooks\CentralAuthIsUIReloadRecommendedHook;
use MediaWiki\User\Options\UserOptionsLookup;
use MediaWiki\User\User;

/**
 * @package CFrame
 * @internal
 */
class CentralAuthHooks implements CentralAuthIsUIReloadRecommendedHook {

	private UserOptionsLookup $userOptionsLookup;

	public function __construct( UserOptionsLookup $userOptionsLookup ) {
		$this->userOptionsLookup = $userOptionsLookup;
	}
}
