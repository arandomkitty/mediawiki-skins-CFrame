<?php
namespace MediaWiki\Skins\CFrame\Components;

/**
 * CFrameComponentDropdown component
 */
class CFrameComponentDropdown implements CFrameComponent {
	/** @var string */
	private $id;
	/** @var string */
	private $label;
	/** @var string */
	private $class;
	/** @var string */
	private $tooltip;
	/** @var string|null */
	private $icon;

	/**
	 * @param string $id
	 * @param string $label
	 * @param string $class
	 * @param string|null $icon
	 * @param string $tooltip
	 */
	public function __construct( string $id, string $label, string $class = '', $icon = null, string $tooltip = '' ) {
		$this->id = $id;
		$this->label = $label;
		$this->class = $class;
		$this->icon = $icon;
		$this->tooltip = $tooltip;
	}

	/**
	 * @inheritDoc
	 */
	public function getTemplateData(): array {
		// FIXME: Stop hardcoding button and icon styles, this assumes all dropdowns with icons are icon buttons
		// Not the case for the language dropdown, page tools, etc
		$icon = $this->icon;
		$buttonClass = 'cdx-button cdx-button--fake-button cdx-button--fake-button--enabled cdx-button--weight-quiet';
		$iconButtonClass = $icon ? ' cdx-button--icon-only ' : '';

		return [
			'id' => $this->id,
			'label' => $this->label,
			'label-class' => $buttonClass . $iconButtonClass,
			'icon' => $this->icon,
			'html-cframe-menu-label-attributes' => '',
			'html-cframe-menu-checkbox-attributes' => '',
			'class' => $this->class,
			'html-tooltip' => $this->tooltip,
			'checkbox-class' => '',
		];
	}
}
