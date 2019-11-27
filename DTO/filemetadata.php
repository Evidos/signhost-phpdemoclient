<?php
class FileMetadata implements JsonSerializable {
	/** @var string */
	public $DisplayName;

	/** @var int */
	public $DisplayOrder;

	/** @var string */
	public $Description;

	/** @var FormSets[string] */
	public $Signers;

	/** @var FormSetField[string][string] */
	public $FormSets;

	/**
	 * @param string                       $displayName
	 * @param int                          $displayOrder
	 * @param string                       $description
	 * @param FormSets[string]             $signers
	 * @param FormSetField[string][string] $formSets
	 */
	function __construct(
		$displayName  = null,
		$displayOrder = null,
		$description  = null,
		$signers      = null,
		$formSets     = null
	) {
		$this->DisplayName  = $displayName;
		$this->DisplayOrder = $displayOrder;
		$this->Description  = $description;
		$this->Signers      = $signers;
		$this->FormSets     = $formSets;
	}

	function jsonSerialize() {
		return array_filter(array(
			"DisplayName"  => $this->DisplayName,
			"DisplayOrder" => $this->DisplayOrder,
			"Description"  => $this->Description,
			"Signers"      => $this->Signers,
			"FormSets"     => $this->FormSets,
		));
	}
}
