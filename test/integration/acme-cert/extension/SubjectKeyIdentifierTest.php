<?php

use CryptoUtil\ASN1\RSA\RSAPrivateKey;
use CryptoUtil\PEM\PEM;
use X509\Certificate\Extension\Extension;
use X509\Certificate\Extension\SubjectKeyIdentifierExtension;
use X509\Certificate\Extensions;

require_once __DIR__ . "/RefExtTestHelper.php";


/**
 * @group certificate
 * @group extension
 * @group decode
 */
class RefSubjectKeyIdentifierTest extends RefExtTestHelper
{
	/**
	 *
	 * @param Extensions $extensions
	 * @return SubjectKeyIdentifierExtension
	 */
	public function testSubjectKeyIdentifier() {
		$ext = self::$_extensions->get(Extension::OID_SUBJECT_KEY_IDENTIFIER);
		$this->assertInstanceOf(SubjectKeyIdentifierExtension::class, $ext);
		return $ext;
	}
	
	/**
	 * @depends testSubjectKeyIdentifier
	 *
	 * @param SubjectKeyIdentifierExtension $ski
	 */
	public function testSubjectKeyIdentifierKey(
			SubjectKeyIdentifierExtension $ski) {
		$pem = PEM::fromFile(TEST_ASSETS_DIR . "/certs/keys/acme-rsa.pem");
		$keyid = RSAPrivateKey::fromPEM($pem)->publicKey()
			->publicKeyInfo()
			->keyIdentifier();
		$this->assertEquals($keyid, $ski->keyIdentifier());
	}
}