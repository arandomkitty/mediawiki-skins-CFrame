<?php
namespace MediaWiki\Skins\CFrame\Components;

/**
 * Component interface for managing CFrame-modified components
 *
 * @internal
 */
interface CFrameComponent {
	/**
	 * @return array of Mustache compatible data
	 */
	public function getTemplateData(): array;
}
