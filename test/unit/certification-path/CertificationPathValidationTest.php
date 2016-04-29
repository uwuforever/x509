<?php

use CryptoUtil\Crypto\Crypto;
use CryptoUtil\PEM\PEM;
use X509\Certificate\Certificate;
use X509\CertificationPath\CertificationPath;
use X509\CertificationPath\PathValidation\PathValidationConfig;
use X509\CertificationPath\PathValidation\PathValidationResult;


/**
 * @group certification-path
 */
class CertificationPathValidationTest extends PHPUnit_Framework_TestCase
{
	private static $_path;
	
	public static function setUpBeforeClass() {
		$certs = array(
			Certificate::fromPEM(
				PEM::fromFile(TEST_ASSETS_DIR . "/certs/acme-ca.pem")), 
			Certificate::fromPEM(
				PEM::fromFile(TEST_ASSETS_DIR . "/certs/acme-interm-ecdsa.pem")), 
			Certificate::fromPEM(
				PEM::fromFile(TEST_ASSETS_DIR . "/certs/acme-ecdsa.pem")));
		self::$_path = new CertificationPath(...$certs);
	}
	
	public static function tearDownAfterClass() {
		self::$_path = null;
	}
	
	/**
	 *
	 * @return PathValidationResult
	 */
	public function testValidateDefault() {
		$result = self::$_path->validate(Crypto::getDefault(), 
			PathValidationConfig::defaultConfig());
		$this->assertInstanceOf(PathValidationResult::class, $result);
		return $result;
	}
	
	/**
	 * @depends testValidateDefault
	 *
	 * @param PathValidationResult $result
	 */
	public function testResult(PathValidationResult $result) {
		$expected_cert = Certificate::fromPEM(
			PEM::fromFile(TEST_ASSETS_DIR . "/certs/acme-ecdsa.pem"));
		$this->assertEquals($expected_cert, $result->certificate());
	}
	
	/* @formatter:off */
	/**
	 * @expectedException X509\CertificationPath\Exception\PathValidationException
	 */
	/* @formatter:on */
	public function testValidateExpired() {
		$config = PathValidationConfig::defaultConfig()->withDateTime(
			new DateTimeImmutable("2026-01-03"));
		self::$_path->validate(Crypto::getDefault(), $config);
	}
	
	/* @formatter:off */
	/**
	 * @expectedException X509\CertificationPath\Exception\PathValidationException
	 */
	/* @formatter:on */
	public function testValidateNotBeforeFail() {
		$config = PathValidationConfig::defaultConfig()->withDateTime(
			new DateTimeImmutable("2015-12-31"));
		self::$_path->validate(Crypto::getDefault(), $config);
	}
	
	/* @formatter:off */
	/**
	 * @expectedException X509\CertificationPath\Exception\PathValidationException
	 */
	/* @formatter:on */
	public function testValidatePathLengthFail() {
		$config = PathValidationConfig::defaultConfig()->withMaxLength(0);
		self::$_path->validate(Crypto::getDefault(), $config);
	}
}