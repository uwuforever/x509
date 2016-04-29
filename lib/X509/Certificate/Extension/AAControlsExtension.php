<?php

namespace X509\Certificate\Extension;

use ASN1\Element;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\Boolean;
use ASN1\Type\Primitive\Integer;
use ASN1\Type\Primitive\ObjectIdentifier;
use ASN1\Type\Tagged\ImplicitlyTaggedType;


/**
 * Implements 'AA Controls' certificate extension.
 *
 * @link https://tools.ietf.org/html/rfc5755#section-7.4
 */
class AAControlsExtension extends Extension
{
	/**
	 * Path length contraint.
	 *
	 * @var int|null $_pathLenConstraint
	 */
	protected $_pathLenConstraint;
	
	/**
	 * Permitted attributes.
	 *
	 * Array of OID's.
	 *
	 * @var string[]|null $_permittedAttrs
	 */
	protected $_permittedAttrs;
	
	/**
	 * Excluded attributes.
	 *
	 * Array of OID's.
	 *
	 * @var string[]|null $_excludedAttrs
	 */
	protected $_excludedAttrs;
	
	/**
	 * Whether to permit unspecified attributes.
	 *
	 * @var bool $_permitUnSpecified
	 */
	protected $_permitUnSpecified;
	
	/**
	 * Constructor
	 *
	 * @param bool $critical
	 * @param int|null $path_len
	 * @param string[]|null $permitted
	 * @param string[]|null $excluded
	 * @param bool $permit_unspecified
	 */
	public function __construct($critical, $path_len = null, array $permitted = null, 
			array $excluded = null, $permit_unspecified = true) {
		parent::__construct(self::OID_AA_CONTROLS, $critical);
		$this->_pathLenConstraint = $path_len;
		$this->_permittedAttrs = $permitted;
		$this->_excludedAttrs = $excluded;
		$this->_permitUnSpecified = $permit_unspecified;
	}
	
	protected static function _fromDER($data, $critical) {
		$seq = Sequence::fromDER($data);
		$path_len = null;
		$permitted = null;
		$excluded = null;
		$permit_unspecified = true;
		$idx = 0;
		if ($seq->has($idx, Element::TYPE_INTEGER)) {
			$path_len = $seq->at($idx++)->number();
		}
		if ($seq->hasTagged(0)) {
			$attr_seq = $seq->getTagged(0)->implicit(Element::TYPE_SEQUENCE);
			$permitted = array_map(
				function (Element $el) {
					return $el->expectType(Element::TYPE_OBJECT_IDENTIFIER)->oid();
				}, $attr_seq->elements());
			$idx++;
		}
		if ($seq->hasTagged(1)) {
			$attr_seq = $seq->getTagged(1)->implicit(Element::TYPE_SEQUENCE);
			$excluded = array_map(
				function (Element $el) {
					return $el->expectType(Element::TYPE_OBJECT_IDENTIFIER)->oid();
				}, $attr_seq->elements());
			$idx++;
		}
		if ($seq->has($idx, Element::TYPE_BOOLEAN)) {
			$permit_unspecified = $seq->at($idx++)->value();
		}
		return new self($critical, $path_len, $permitted, $excluded, 
			$permit_unspecified);
	}
	
	public function hasPathLen() {
		return isset($this->_pathLenConstraint);
	}
	
	public function pathLen() {
		if (!$this->hasPathLen()) {
			throw new \LogicException("pathLen not set");
		}
		return $this->_pathLenConstraint;
	}
	
	public function hasPermittedAttrs() {
		return isset($this->_permittedAttrs);
	}
	
	public function permittedAttrs() {
		if (!$this->hasPermittedAttrs()) {
			throw new \LogicException("permittedAttrs not set");
		}
		return $this->_permittedAttrs;
	}
	
	public function hasExcludedAttrs() {
		return isset($this->_excludedAttrs);
	}
	
	public function excludedAttrs() {
		if (!$this->hasExcludedAttrs()) {
			throw new \LogicException("excludedAttrs not set");
		}
		return $this->_excludedAttrs;
	}
	
	public function permitUnspecified() {
		return $this->_permitUnSpecified;
	}
	
	protected function _valueASN1() {
		$elements = array();
		if (isset($this->_pathLenConstraint)) {
			$elements[] = new Integer($this->_pathLenConstraint);
		}
		if (isset($this->_permittedAttrs)) {
			$oids = array_map(
				function ($oid) {
					return new ObjectIdentifier($oid);
				}, $this->_permittedAttrs);
			$elements[] = new ImplicitlyTaggedType(0, new Sequence(...$oids));
		}
		if (isset($this->_excludedAttrs)) {
			$oids = array_map(
				function ($oid) {
					return new ObjectIdentifier($oid);
				}, $this->_excludedAttrs);
			$elements[] = new ImplicitlyTaggedType(1, new Sequence(...$oids));
		}
		if ($this->_permitUnSpecified !== true) {
			$elements[] = new Boolean(false);
		}
		return new Sequence(...$elements);
	}
}