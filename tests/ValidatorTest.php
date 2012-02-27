<?php

require_once(__DIR__ . '/../Validator.php');

class ValidatorTest extends PHPUnit_Framework_TestCase {
  
  private $val;
  
  public function setUp() {
    
    $this->val = new Formula\Validator();
    
    parent::setUp();
  }
  
  public function tearDown() {
    parent::tearDown();
  }
      
  public function testIsRequiredFailsForEmptyStrings() {
    
   $this->assertFalse($this->val->is_required('')); 
   $this->assertFalse($this->val->is_required('  '));
   $this->assertFalse($this->val->is_required(NULL));
  }
  
  public function testIsRequiredSucceedsForNonEmptyStrings() {
    
    $this->assertTrue($this->val->is_required("HEYA"));
    $this->assertTrue($this->val->is_required(array(1, 2, 3)));
    $this->assertTrue($this->val->is_required(0));
  }
  
  public function testIsNumericSucceedsForAnyNumber() {
    
    $this->assertTrue($this->val->is_numeric('123'));
    $this->assertTrue($this->val->is_numeric('-123'));
    $this->assertTrue($this->val->is_numeric('233.23'));
    $this->assertTrue($this->val->is_numeric('04.2'));
  }

  public function testIsNumericFailsForNonNumbers() {
    
    $this->assertFalse($this->val->is_numeric('abc123'));
    $this->assertFalse($this->val->is_numeric('234-24'));
    $this->assertFalse($this->val->is_numeric('barf'));
    $this->assertFalse($this->val->is_numeric('sl20.2340as')); 
  }
  
  public function testIsIntegerSucceedsForIntegers() {
    
     $this->assertTrue($this->val->is_integer('123'));   
     $this->assertTrue($this->val->is_integer('-123'));
     $this->assertTrue($this->val->is_integer('0'));
  }
  
  public function testIsIntegerFailsForNonIntegers() {
    
     $this->assertFalse($this->val->is_integer('123.45'));   
     $this->assertFalse($this->val->is_integer('barf'));
  }
  
  public function testIsEmailAddressSucceedsForValidAddresses() {
    
    $this->assertTrue($this->val->is_email_address('abc@example.com'));
    $this->assertTrue($this->val->is_email_address('abc@baz.example.com'));
  }
  
  public function testIsEmailAddressFailsForNonAddresses() {
    
    $this->assertFalse($this->val->is_email_address('not_an_email'));
    $this->assertFalse($this->val->is_email_address('abc@baz.examp##leom'));
  }

  public function testIsOneOfSucceedsForValidSets() {
    
    $this->assertTrue($this->val->is_one_of('abc', array('abc', 'def')));
    $this->assertTrue($this->val->is_one_of('abc', array('abc')));
    $this->assertTrue($this->val->is_one_of('abc', 'abc'));
    $this->assertTrue($this->val->is_one_of('abc', 'abc;def'));
  }

  public function testIsOneOfFailsForInvalidSets() {

    $this->assertFalse($this->val->is_one_of('abc', array('def', 'ghi')));
    $this->assertFalse($this->val->is_one_of('abc', array('def')));
    $this->assertFalse($this->val->is_one_of('abc', 'def'));
    $this->assertFalse($this->val->is_one_of('abc', 'def;ghi'));
    
  }

  public function testIsUrlSucceedsForValidUrls() {    
    $this->assertTrue($this->val->is_url('http://www.example.com'));
    $this->assertTrue($this->val->is_url('https://www.example.com?some=complicated&query=string&123=here'));    
    $this->assertTrue($this->val->is_url('https://10.10.5.5'));    
  }
  
  public function testIsUrlFailsForInvalidUrls() {
    $this->assertFalse($this->val->is_url('http:/www.example.com'));
    $this->assertFalse($this->val->is_url('example.com'));    
    $this->assertFalse($this->val->is_url('10.10.5.5'));
  }

  public function testIsValidIPSucceedsForIPAddresses() {

    $this->assertTrue($this->val->is_valid_ip('10.5.5.5'));
    $this->assertTrue($this->val->is_valid_ip('fe80:0:0:0:200:f8ff:fe21:67cf'));
    
  }
  
  public function testIsValidIPFailsForNonIPAddress() {
    
    $this->assertFalse($this->val->is_valid_ip('10.5.5.5.5'));
    $this->assertFalse($this->val->is_valid_ip('1234567'));        
    $this->assertFalse($this->val->is_valid_ip('some.dns.name.com'));
  }
  
  public function testIsValidHostnameSucceedsForValidHostname() {
    
    $this->assertTrue($this->val->is_valid_hostname('10.5.5.5'));
    $this->assertTrue($this->val->is_valid_hostname('fe80:0:0:0:200:f8ff:fe21:67cf'));
    $this->assertTrue($this->val->is_valid_hostname('some.dns.name.com'));
  }
  
  public function testIsValidHostnameFailsForInvalidHostname() {
    
    $this->assertFalse($this->val->is_valid_hostname('10.5.5.5.5'));
    $this->assertFalse($this->val->is_valid_hostname('1234567'));
    $this->assertFalse($this->val->is_valid_hostname('blargh#3048#)*(@_.com'));    
  }
  
  public function testIsValidIPv4SucceedsForIPv4Address() {
    
    $this->assertTrue($this->val->is_valid_ipv4('10.5.5.5'));
    $this->assertTrue($this->val->is_valid_ipv4('128.186.72.1'));
    
  }
  
  public function testIsValidIPv4SucceedsForNonIPv4Address() {
    
    $this->assertFalse($this->val->is_valid_ipv4('fe80:0:0:0:200:f8ff:fe21:67cf'));
    $this->assertFalse($this->val->is_valid_ipv4('10.5.5.5.5'));
    $this->assertFalse($this->val->is_valid_ipv4('some.dns.name.com'));   
  }
  
  public function testIsPublicIPSucceedsForPublicIPs() {
    
    $this->assertTrue($this->val->is_public_ip('128.186.72.1'));
    $this->assertTrue($this->val->is_public_ip('5.5.5.5'));
    $this->assertTrue($this->val->is_public_ip('e80:0:0:0:200:f8ff:fe21:67cf'));    
  }
  
  public function testIsPublicIPFailsForPrivateIPs() {
    $this->assertFalse($this->val->is_public_ip('10.5.5.5'));
    $this->assertFalse($this->val->is_public_ip('192.168.1.5'));
    $this->assertFalse($this->val->is_public_ip('FDC8:BF8B:E62C:ABCD:1111:2222:3333:4444'));
    
  }
  
  public function testIsValidIPv6SucceedsForIPv6Address() {
    $this->assertTrue($this->val->is_valid_ipv6('FDC8:BF8B:E62C:ABCD:1111:2222:3333:4444'));    
    $this->assertTrue($this->val->is_valid_ipv6('e80:0:0:0:200:f8ff:fe21:67cf'));     
  }
  
  public function testIsValidIPv6FailsForNonIPv6Address() {
    $this->assertFalse($this->val->is_valid_ipv6('128.186.72.1'));
    $this->assertFalse($this->val->is_valid_ipv6('barf'));
    $this->assertFalse($this->val->is_valid_ipv6('10.5.5.5'));
  }
  
  public function testIsLessThanReturnsTrueForValidNumbers() {
    
    $this->assertTrue($this->val->is_less_than('12', 15));
    $this->assertTrue($this->val->is_less_than(12, '15'));
    $this->assertTrue($this->val->is_less_than(120.53, 120.531));    
    $this->assertTrue($this->val->is_less_than(-2, -1));    
  }
  
  public function testIsLessTahReturnsFalseForInvalidNumbers() {
    $this->assertFalse($this->val->is_less_than('16', 15));
    $this->assertFalse($this->val->is_less_than(18, '15'));
    $this->assertFalse($this->val->is_less_than(120.53, 120.529));    
    $this->assertFalse($this->val->is_less_than(-1, -2));     
  }
  
}

/* EOF: ValidatorTest.php */