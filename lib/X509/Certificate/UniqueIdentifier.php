<?php

namespace X509\Certificate;

use ASN1\Type\Primitive\BitString;


/**
 * Implements <i>UniqueIdentifier</i> ASN.1 type.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.1.2.8
 */
class UniqueIdentifier
{
	/**
	 * Identifier
	 *
	 * @var BitString $_uid
	 */
	protected $_uid;
	
	/**
	 * Constructor
	 *
	 * @param BitString $bs
	 */
	public function __construct(BitString $bs) {
		$this->_uid = $bs;
	}
	
	/**
	 * Initialize from ASN.1.
	 *
	 * @param BitString $bs
	 */
	public static function fromASN1(BitString $bs) {
		return new self($bs);
	}
	
	/**
	 * Initialize from string.
	 *
	 * @param string $str
	 * @return self
	 */
	public static function fromString($str) {
		return new self(new BitString($str));
	}
	
	/**
	 * Get ASN.1 element.
	 *
	 * @return BitString
	 */
	public function toASN1() {
		return $this->_uid;
	}
}