<?php

use ASN1\Type\Constructed\Sequence;
use X501\ASN1\AttributeValue\AttributeValue;
use X509\Certificate\Extensions;
use X509\CertificationRequest\Attribute\ExtensionRequestValue;


/**
 * @group csr
 * @group attribute
 */
class ExtensionRequestTest extends PHPUnit_Framework_TestCase
{
	public function testCreate() {
		$value = new ExtensionRequestValue(new Extensions());
		$this->assertInstanceOf(ExtensionRequestValue::class, $value);
		return $value;
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeValue $value
	 */
	public function testEncode(AttributeValue $value) {
		$el = $value->toASN1();
		$this->assertInstanceOf(Sequence::class, $el);
		return $el->toDER();
	}
	
	/**
	 * @depends testEncode
	 *
	 * @param unknown $der
	 */
	public function testDecode($der) {
		$value = ExtensionRequestValue::fromASN1(Sequence::fromDER($der));
		$this->assertInstanceOf(ExtensionRequestValue::class, $value);
		return $value;
	}
	
	/**
	 * @depends testCreate
	 * @depends testDecode
	 *
	 * @param AttributeValue $ref
	 * @param AttributeValue $new
	 */
	public function testRecoded(AttributeValue $ref, AttributeValue $new) {
		$this->assertEquals($ref, $new);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param AttributeValue $value
	 */
	public function testOID(AttributeValue $value) {
		$this->assertEquals(ExtensionRequestValue::OID, $value->oid());
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param ExtensionRequestValue $value
	 */
	public function testExtensions(ExtensionRequestValue $value) {
		$this->assertInstanceOf(Extensions::class, $value->extensions());
	}
}