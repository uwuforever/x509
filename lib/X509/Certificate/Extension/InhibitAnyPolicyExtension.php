<?php

namespace X509\Certificate\Extension;

use ASN1\Type\Primitive\Integer;


/**
 * Implements 'Inhibit anyPolicy' extension.
 *
 * @link https://tools.ietf.org/html/rfc5280#section-4.2.1.14
 */
class InhibitAnyPolicyExtension extends Extension
{
	/**
	 *
	 * @var int $_skipCerts
	 */
	protected $_skipCerts;
	
	/**
	 * Constructor
	 *
	 * @param bool $critical
	 * @param int $skip_certs
	 */
	public function __construct($critical, $skip_certs) {
		parent::__construct(self::OID_INHIBIT_ANY_POLICY, $critical);
		$this->_skipCerts = $skip_certs;
	}
	
	protected static function _fromDER($data, $critical) {
		return new self($critical, Integer::fromDER($data)->number());
	}
	
	/**
	 * Get value.
	 *
	 * @return int
	 */
	public function skipCerts() {
		return $this->_skipCerts;
	}
	
	protected function _valueASN1() {
		return new Integer($this->_skipCerts);
	}
}