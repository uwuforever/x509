<?php

namespace X509\Certificate\Extension\DistributionPoint;

use ASN1\Type\Primitive\BitString;
use ASN1\Util\Flags;


/**
 * Implements <i>ReasonFlags</i> ASN.1 type used by
 * 'CRL Distribution Points' certificate extension.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.2.1.13
 */
class ReasonFlags
{
	// const UNUSED = 0x100;
	const KEY_COMPROMISE = 0x080;
	const CA_COMPROMISE = 0x040;
	const AFFILIATION_CHANGED = 0x020;
	const SUPERSEDED = 0x010;
	const CESSATION_OF_OPERATION = 0x008;
	const CERTIFICATE_HOLD = 0x004;
	const PRIVILEGE_WITHDRAWN = 0x002;
	const AA_COMPROMISE = 0x001;
	
	/**
	 * Flags.
	 *
	 * @var int $_flags
	 */
	protected $_flags;
	
	/**
	 * Constructor
	 *
	 * @param int $flags
	 */
	public function __construct($flags) {
		$this->_flags = (int) $flags;
	}
	
	/**
	 * Initialize from ASN.1.
	 *
	 * @param BitString $bs
	 * @return self
	 */
	public static function fromASN1(BitString $bs) {
		return new self(Flags::fromBitString($bs, 9)->number());
	}
	
	/**
	 * Check whether keyCompromise flag is set.
	 *
	 * @return bool
	 */
	public function isKeyCompromise() {
		return $this->_flagSet(self::KEY_COMPROMISE);
	}
	
	/**
	 * Check whether cACompromise flag is set.
	 *
	 * @return bool
	 */
	public function isCACompromise() {
		return $this->_flagSet(self::CA_COMPROMISE);
	}
	
	/**
	 * Check whether affiliationChanged flag is set.
	 *
	 * @return bool
	 */
	public function isAffiliationChanged() {
		return $this->_flagSet(self::AFFILIATION_CHANGED);
	}
	
	/**
	 * Check whether superseded flag is set.
	 *
	 * @return bool
	 */
	public function isSuperseded() {
		return $this->_flagSet(self::SUPERSEDED);
	}
	
	/**
	 * Check whether cessationOfOperation flag is set.
	 *
	 * @return bool
	 */
	public function isCessationOfOperation() {
		return $this->_flagSet(self::CESSATION_OF_OPERATION);
	}
	
	/**
	 * Check whether certificateHold flag is set.
	 *
	 * @return bool
	 */
	public function isCertificateHold() {
		return $this->_flagSet(self::CERTIFICATE_HOLD);
	}
	
	/**
	 * Check whether privilegeWithdrawn flag is set.
	 *
	 * @return bool
	 */
	public function isPrivilegeWithdrawn() {
		return $this->_flagSet(self::PRIVILEGE_WITHDRAWN);
	}
	
	/**
	 * Check whether aACompromise flag is set.
	 *
	 * @return bool
	 */
	public function isAACompromise() {
		return $this->_flagSet(self::AA_COMPROMISE);
	}
	
	/**
	 * Generate ASN.1 element.
	 *
	 * @return BitString
	 */
	public function toASN1() {
		$flags = new Flags($this->_flags, 9);
		return $flags->bitString()->withoutTrailingZeroes();
	}
	
	protected function _flagSet($flag) {
		return (bool) ($this->_flags & $flag);
	}
}