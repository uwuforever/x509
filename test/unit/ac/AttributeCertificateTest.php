<?php

use ASN1\Type\Constructed\Sequence;
use CryptoUtil\ASN1\AlgorithmIdentifier\Feature\SignatureAlgorithmIdentifier;
use CryptoUtil\ASN1\AlgorithmIdentifier\Signature\SHA256WithRSAEncryptionAlgorithmIdentifier;
use CryptoUtil\ASN1\PrivateKeyInfo;
use CryptoUtil\Crypto\Crypto;
use CryptoUtil\Crypto\Signature;
use CryptoUtil\PEM\PEM;
use X501\ASN1\Name;
use X509\AttributeCertificate\AttCertIssuer;
use X509\AttributeCertificate\AttCertValidityPeriod;
use X509\AttributeCertificate\Attribute\RoleAttributeValue;
use X509\AttributeCertificate\AttributeCertificate;
use X509\AttributeCertificate\AttributeCertificateInfo;
use X509\AttributeCertificate\Attributes;
use X509\AttributeCertificate\Holder;
use X509\AttributeCertificate\IssuerSerial;
use X509\GeneralName\DirectoryName;
use X509\GeneralName\GeneralNames;
use X509\GeneralName\UniformResourceIdentifier;


/**
 * @group ac
 */
class AttributeCertificateTest extends PHPUnit_Framework_TestCase
{
	private static $_privateKeyInfo;
	
	public static function setUpBeforeClass() {
		self::$_privateKeyInfo = PrivateKeyInfo::fromPEM(
			PEM::fromFile(TEST_ASSETS_DIR . "/rsa/private_key.pem"));
	}
	
	public static function tearDownAfterClass() {
		self::$_privateKeyInfo = null;
	}
	
	public function testCreate() {
		$holder = new Holder(
			new IssuerSerial(
				new GeneralNames(DirectoryName::fromDNString("cn=Issuer")), 42));
		$issuer = AttCertIssuer::fromName(Name::fromString("cn=Issuer"));
		$validity = AttCertValidityPeriod::fromStrings("2016-04-29 12:00:00", 
			"2016-04-29 13:00:00");
		$attribs = Attributes::fromAttributeValues(
			new RoleAttributeValue(new UniformResourceIdentifier("urn:admin")));
		$acinfo = new AttributeCertificateInfo($holder, $issuer, $validity, 
			$attribs);
		$algo = new SHA256WithRSAEncryptionAlgorithmIdentifier();
		$acinfo = $acinfo->withSignature($algo)->withSerial(1);
		$signature = Crypto::getDefault()->sign($acinfo->toASN1()
			->toDER(), self::$_privateKeyInfo, $algo);
		$ac = new AttributeCertificate($acinfo, $algo, $signature);
		$this->assertInstanceOf(AttributeCertificate::class, $ac);
		return $ac;
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeCertificate $ac
	 */
	public function testEncode(AttributeCertificate $ac) {
		$seq = $ac->toASN1();
		$this->assertInstanceOf(Sequence::class, $seq);
		return $seq->toDER();
	}
	
	/**
	 * @depends testEncode
	 *
	 * @param string $der
	 */
	public function testDecode($der) {
		$ac = AttributeCertificate::fromASN1(Sequence::fromDER($der));
		$this->assertInstanceOf(AttributeCertificate::class, $ac);
		return $ac;
	}
	
	/**
	 * @depends testCreate
	 * @depends testDecode
	 *
	 * @param AttributeCertificate $ref
	 * @param AttributeCertificate $new
	 */
	public function testRecoded(AttributeCertificate $ref, 
			AttributeCertificate $new) {
		$this->assertEquals($ref, $new);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeCertificate $ac
	 */
	public function testAttributeCertificateInfo(AttributeCertificate $ac) {
		$this->assertInstanceOf(AttributeCertificateInfo::class, $ac->acinfo());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeCertificate $ac
	 */
	public function testSignatureAlgo(AttributeCertificate $ac) {
		$this->assertInstanceOf(SignatureAlgorithmIdentifier::class, 
			$ac->signatureAlgorithm());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeCertificate $ac
	 */
	public function testSignatureValue(AttributeCertificate $ac) {
		$this->assertInstanceOf(Signature::class, $ac->signatureValue());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeCertificate $ac
	 */
	public function testVerify(AttributeCertificate $ac) {
		$pubkey_info = self::$_privateKeyInfo->privateKey()
			->publicKey()
			->publicKeyInfo();
		$this->assertTrue($ac->verify(Crypto::getDefault(), $pubkey_info));
	}
}