<?php

namespace X509\AttributeCertificate;

use ASN1\Element;
use ASN1\Type\Constructed\Sequence;
use ASN1\Type\Primitive\GeneralizedTime;


/**
 * Implements <i>AttCertValidityPeriod</i> ASN.1 type.
 *
 * @link https://tools.ietf.org/html/rfc5755#section-4.1
 */
class AttCertValidityPeriod
{
	/**
	 * Not before time.
	 *
	 * @var \DateTimeImmutable
	 */
	protected $_notBeforeTime;
	
	/**
	 * Not after time.
	 *
	 * @var \DateTimeImmutable
	 */
	protected $_notAfterTime;
	
	/**
	 * Constructor
	 *
	 * @param \DateTimeImmutable $nb
	 * @param \DateTimeImmutable $na
	 */
	public function __construct(\DateTimeImmutable $nb, \DateTimeImmutable $na) {
		$this->_notBeforeTime = $nb;
		$this->_notAfterTime = $na;
	}
	
	/**
	 * Initialize from ASN.1.
	 *
	 * @param Sequence $seq
	 * @return self
	 */
	public static function fromASN1(Sequence $seq) {
		$nb = $seq->at(0, Element::TYPE_GENERALIZED_TIME)->dateTime();
		$na = $seq->at(1, Element::TYPE_GENERALIZED_TIME)->dateTime();
		return new self($nb, $na);
	}
	
	/**
	 * Initialize from date strings.
	 *
	 * @param string|null $nb_date Not before date
	 * @param string|null $na_date Not after date
	 * @param string|null $tz Timezone string
	 * @return self
	 */
	public static function fromStrings($nb_date, $na_date, $tz = null) {
		$timezone = isset($tz) ? new \DateTimeZone($tz) : null;
		$nb = new \DateTimeImmutable($nb_date, $timezone);
		$na = new \DateTimeImmutable($na_date, $timezone);
		return new self($nb, $na);
	}
	
	/**
	 * Get not before time.
	 *
	 * @return \DateTimeImmutable
	 */
	public function notBeforeTime() {
		return $this->_notBeforeTime;
	}
	
	/**
	 * Get not after time.
	 *
	 * @return \DateTimeImmutable
	 */
	public function notAfterTime() {
		return $this->_notAfterTime;
	}
	
	/**
	 * Generate ASN.1 structure.
	 *
	 * @return Sequence
	 */
	public function toASN1() {
		return new Sequence(new GeneralizedTime($this->_notBeforeTime), 
			new GeneralizedTime($this->_notAfterTime));
	}
}