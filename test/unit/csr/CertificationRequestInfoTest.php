<?php

use ASN1\Type\Constructed\Sequence;
use CryptoUtil\ASN1\PrivateKeyInfo;
use CryptoUtil\PEM\PEM;
use X501\ASN1\Name;
use X509\Certificate\Extension\SubjectAlternativeNameExtension;
use X509\Certificate\Extensions;
use X509\CertificationRequest\Attribute\ExtensionRequestValue;
use X509\CertificationRequest\Attributes;
use X509\CertificationRequest\CertificationRequestInfo;
use X509\GeneralName\DirectoryName;
use X509\GeneralName\GeneralNames;


/**
 * @group csr
 */
class CertificationRequestInfoTest extends PHPUnit_Framework_TestCase
{
	const SAN_DN = "cn=Alt Name";
	
	private static $_subject;
	
	private static $_privateKeyInfo;
	
	private static $_attribs;
	
	public static function setUpBeforeClass() {
		self::$_subject = Name::fromString("cn=Subject");
		self::$_privateKeyInfo = PrivateKeyInfo::fromPEM(
			PEM::fromFile(TEST_ASSETS_DIR . "/rsa/private_key.pem"));
		$extensions = new Extensions(
			new SubjectAlternativeNameExtension(true, 
				new GeneralNames(DirectoryName::fromDNString(self::SAN_DN))));
		self::$_attribs = Attributes::fromAttributeValues(
			new ExtensionRequestValue($extensions));
	}
	
	public static function tearDownAfterClass() {
		self::$_subject = null;
		self::$_privateKeyInfo = null;
		self::$_attribs = null;
	}
	
	public function testCreate() {
		$pkinfo = self::$_privateKeyInfo->privateKey()
			->publicKey()
			->publicKeyInfo();
		$cri = new CertificationRequestInfo(self::$_subject, $pkinfo);
		$cri = $cri->withAttributes(self::$_attribs);
		$this->assertInstanceOf(CertificationRequestInfo::class, $cri);
		return $cri;
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param CertificationRequestInfo $cri
	 */
	public function testEncode(CertificationRequestInfo $cri) {
		$seq = $cri->toASN1();
		$this->assertInstanceOf(Sequence::class, $seq);
		return $seq->toDER();
	}
	
	/**
	 * @depends testEncode
	 *
	 * @param string $der
	 */
	public function testDecode($der) {
		$cert = CertificationRequestInfo::fromASN1(Sequence::fromDER($der));
		$this->assertInstanceOf(CertificationRequestInfo::class, $cert);
		return $cert;
	}
	
	/**
	 * @depends testCreate
	 * @depends testDecode
	 *
	 * @param CertificationRequestInfo $ref
	 * @param CertificationRequestInfo $new
	 */
	public function testRecoded(CertificationRequestInfo $ref, 
			CertificationRequestInfo $new) {
		$this->assertEquals($ref, $new);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param CertificationRequestInfo $cri
	 */
	public function testVersion(CertificationRequestInfo $cri) {
		$this->assertEquals(CertificationRequestInfo::VERSION_1, 
			$cri->version());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param CertificationRequestInfo $cri
	 */
	public function testSubject(CertificationRequestInfo $cri) {
		$this->assertEquals(self::$_subject, $cri->subject());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param CertificationRequestInfo $cri
	 */
	public function testSubjectPKI(CertificationRequestInfo $cri) {
		$pkinfo = self::$_privateKeyInfo->privateKey()
			->publicKey()
			->publicKeyInfo();
		$this->assertEquals($pkinfo, $cri->subjectPKInfo());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param CertificationRequestInfo $cri
	 */
	public function testAttribs(CertificationRequestInfo $cri) {
		$attribs = $cri->attributes();
		$this->assertInstanceOf(Attributes::class, $attribs);
		return $attribs;
	}
	
	/**
	 * @depends testAttribs
	 *
	 * @param Attributes $attribs
	 */
	public function testSAN(Attributes $attribs) {
		$dn = $attribs->extensionRequest()
			->extensions()
			->subjectAlternativeName()
			->names()
			->firstDN()
			->toString();
		$this->assertEquals(self::SAN_DN, $dn);
	}
}