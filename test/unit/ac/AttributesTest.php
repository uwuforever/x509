<?php

use ASN1\Type\Constructed\Sequence;
use X501\ASN1\Attribute;
use X501\ASN1\AttributeType;
use X509\AttributeCertificate\Attribute\AccessIdentityAttributeValue;
use X509\AttributeCertificate\Attribute\RoleAttributeValue;
use X509\AttributeCertificate\Attributes;
use X509\GeneralName\UniformResourceIdentifier;


/**
 * @group ac
 * @group attribute
 */
class AttributeCertificateAttributesTest extends PHPUnit_Framework_TestCase
{
	public function testCreate() {
		$attribs = Attributes::fromAttributeValues(
			new AccessIdentityAttributeValue(
				new UniformResourceIdentifier("urn:service"), 
				new UniformResourceIdentifier("urn:ident")), 
			new RoleAttributeValue(new UniformResourceIdentifier("urn:admin")));
		$this->assertInstanceOf(Attributes::class, $attribs);
		return $attribs;
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Attributes $attribs
	 */
	public function testEncode(Attributes $attribs) {
		$seq = $attribs->toASN1();
		$this->assertInstanceOf(Sequence::class, $seq);
		return $seq->toDER();
	}
	
	/**
	 * @depends testEncode
	 *
	 * @param string $der
	 */
	public function testDecode($der) {
		$tc = Attributes::fromASN1(Sequence::fromDER($der));
		$this->assertInstanceOf(Attributes::class, $tc);
		return $tc;
	}
	
	/**
	 * @depends testCreate
	 * @depends testDecode
	 *
	 * @param Attributes $ref
	 * @param Attributes $new
	 */
	public function testRecoded(Attributes $ref, Attributes $new) {
		$this->assertEquals($ref, $new);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Attributes $attribs
	 */
	public function testCount(Attributes $attribs) {
		$this->assertCount(2, $attribs);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Attributes $attribs
	 */
	public function testIterator(Attributes $attribs) {
		$values = array();
		foreach ($attribs as $attr) {
			$values[] = $attr;
		}
		$this->assertCount(2, $values);
		$this->assertContainsOnlyInstancesOf(Attribute::class, $values);
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Attributes $attribs
	 */
	public function testHas(Attributes $attribs) {
		$this->assertTrue($attribs->has(AttributeType::OID_ACCESS_IDENTITY));
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Attributes $attribs
	 */
	public function testFirstOf(Attributes $attribs) {
		$this->assertInstanceOf(Attribute::class, 
			$attribs->firstOf(AttributeType::OID_ACCESS_IDENTITY));
	}
	
	/**
	 * @depends testCreate
	 *
	 * @param Attributes $attribs
	 */
	public function testAllOf(Attributes $attribs) {
		$this->assertCount(1, 
			$attribs->allOf(AttributeType::OID_ACCESS_IDENTITY));
	}
}