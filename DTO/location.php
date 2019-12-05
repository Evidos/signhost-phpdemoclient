<?php
class Location implements JsonSerializable {
	/** @var string */
	public $Search;

	/** @var int */
	public $Occurence;

	/** @var int */
	public $Top;

	/** @var int */
	public $Right;

	/** @var int */
	public $Bottom;

	/** @var int */
	public $Left;

	/** @var int */
	public $Width;

	/** @var int */
	public $Height;

	/** @var int */
	public $PageNumber;

	/**
	 * @param string $search
	 * @param int    $occurence
	 * @param int    $top
	 * @param int    $right
	 * @param int    $bottom
	 * @param int    $left
	 * @param int    $width
	 * @param int    $height
	 * @param int    $pageNumber
	 */
	function __construct(
		$search     = null,
		$occurence  = null,
		$top        = null,
		$right      = null,
		$bottom     = null,
		$left       = null,
		$width      = null,
		$height     = null,
		$pageNumber = null
	) {
		$this->Search     = $search;
		$this->Occurence  = $occurence;
		$this->Top        = $top;
		$this->Right      = $right;
		$this->Bottom     = $bottom;
		$this->Left       = $left;
		$this->Width      = $width;
		$this->Height     = $height;
		$this->PageNumber = $pageNumber;
	}

	function jsonSerialize() {
		return array_filter(array(
			"Search"     => $this->Search,
			"Occurence"  => $this->Occurence,
			"Top"        => $this->Top,
			"Right"      => $this->Right,
			"Bottom"     => $this->Bottom,
			"Left"       => $this->Left,
			"Width"      => $this->Width,
			"Height"     => $this->Height,
			"PageNumber" => $this->PageNumber,
		));
	}
}
