<?php
class FormSetField implements JsonSerializable {
	/** @var string */
	public $Type;

	/** @var string */
	public $Value;

	/** @var Location */
	public $Location;

	/**
	 * @param string   $type
	 * @param string   $value
	 * @param Location $location
	 */
	function __construct($type, $location, $value = null) {
		$this->Type     = $type;
		$this->Location = $location;
		$this->Value    = $value;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Type"     => $this->Type,
			"Value"    => $this->Value,
			"Location" => $this->Location,
		));
	}
}
