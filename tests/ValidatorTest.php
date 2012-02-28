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

  /*
   * Rule Tests
   */

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

  public function testIsLessThanReturnsFalseForInvalidNumbers() {
    $this->assertFalse($this->val->is_less_than('16', 15));
    $this->assertFalse($this->val->is_less_than(18, '15'));
    $this->assertFalse($this->val->is_less_than(120.53, 120.529));
    $this->assertFalse($this->val->is_less_than(-1, -2));
  }

  public function testIsGreaterThanReturnsTrueForValidNumbers() {
    $this->assertTrue($this->val->is_greater_than('16', 15));
    $this->assertTrue($this->val->is_greater_than(18, '15'));
    $this->assertTrue($this->val->is_greater_than(120.53, 120.529));
    $this->assertTrue($this->val->is_greater_than(-1, -2));

  }

  public function testIsGreaterThanReturnsFalseForValidNumbers() {

    $this->assertFalse($this->val->is_greater_than('12', 15));
    $this->assertFalse($this->val->is_greater_than(12, '15'));
    $this->assertFalse($this->val->is_greater_than(120.53, 120.531));
    $this->assertFalse($this->val->is_greater_than(-2, -1));

  }

  public function testIsExactlySucceedsForExactMatches() {

    $this->assertTrue($this->val->is_exactly(12, '12'));
    $this->assertTrue($this->val->is_exactly(14.05, 14.05));
    $this->assertTrue($this->val->is_exactly('abc 123', 'abc 123'));
    $this->assertTrue($this->val->is_exactly(array(12, 34), array(12, 34)));
    $this->assertTrue($this->val->is_exactly(TRUE, TRUE));

  }

  public function testIsExactlyFailsForNonExactMatches() {

    $this->assertFalse($this->val->is_exactly(12, '12.1'));
    $this->assertFalse($this->val->is_exactly(14.05, 14.04));
    $this->assertFalse($this->val->is_exactly('abc 123', 'ABC 123'));
    $this->assertFalse($this->val->is_exactly(array(12, 34), array(12, 35)));
    $this->assertFalse($this->val->is_exactly(TRUE, FALSE));
  }

  public function testIsExactlyCaseInsensitiveSucceedsForExactMatches() {

    $this->assertTrue($this->val->is_exactly_case_insensitive(12, '12'));
    $this->assertTrue($this->val->is_exactly_case_insensitive(14.05, 14.05));
    $this->assertTrue($this->val->is_exactly_case_insensitive('abc 123', 'ABC 123'));
    $this->assertTrue($this->val->is_exactly_case_insensitive(array(12, 34), array(12, 34)));
    $this->assertTrue($this->val->is_exactly_case_insensitive(TRUE, TRUE));
  }

  public function testIsExactlyCaseInsensitiveFailsForNonExactMatches() {

    $this->assertFalse($this->val->is_exactly_case_insensitive(12, '12.1'));
    $this->assertFalse($this->val->is_exactly_case_insensitive(14.05, 14.04));
    $this->assertFalse($this->val->is_exactly_case_insensitive('abc 123', 'abd 123'));
    $this->assertFalse($this->val->is_exactly_case_insensitive(array(12, 34), array(12, 35)));
    $this->assertFalse($this->val->is_exactly_case_insensitive(TRUE, FALSE));

  }

  public function testIsContainingSucceedsForItemsThatContainValidValues() {

    $this->assertTrue($this->val->is_containing(123, 2));
    $this->assertTrue($this->val->is_containing('abc', 'c'));
    $this->assertTrue($this->val->is_containing(array('a', 'b', 'c'), 'b'));
  }

  public function testIsContainingFailsForItemsThatContainValidValues() {

    $this->assertFalse($this->val->is_containing(123, 4));
    $this->assertFalse($this->val->is_containing('abc', 'd'));
    $this->assertFalse($this->val->is_containing(array('a', 'b', 'c'), 'd'));
  }

  public function testIsAlphanumericSucceedsForAlphaNumItems() {
    
    $this->assertTrue($this->val->is_alphanumeric('abc123'));
    $this->assertTrue($this->val->is_alphanumeric('abc123ZEF'));
    $this->assertTrue($this->val->is_alphanumeric('BARFONLYIZABOO1234'));
  }
  
  public function testisAlphanumericFailsForNonAlphaNumItems() {
    
    $this->assertFalse($this->val->is_alphanumeric('abc 123'));
    $this->assertFalse($this->val->is_alphanumeric('abc-123-ZEF'));
    $this->assertFalse($this->val->is_alphanumeric('BARF_ONLYIZ##ABOO1234'));
    
  }
  
  public function testsIsAlphaNumDashSucceedsForAlphaNumDashItems() {
    
    $this->assertTrue($this->val->is_alphanumeric('abc123'));
    $this->assertTrue($this->val->is_alphanumeric('abc123ZEF'));
    $this->assertTrue($this->val->is_alphanumeric('BARFONLYIZABOO1234'));  
  }
  
  public function testsIsAlphaNumDashFailsForNonAlphaNumDashItems() {
    
  }
  
  public function testsIsAlphaNumDashSpaceSucceedsForAlphaNumDashSpaceItems() {
    
    $this->assertTrue($this->val->is_alphanumdashspace('abc-123'));
    $this->assertTrue($this->val->is_alphanumdashspace('abc-123 ZEF'));
    $this->assertTrue($this->val->is_alphanumdashspace('BARF-ONLY_IZA BOO 1234'));
  }
  
  public function testsIsAlphaNumDashSpaceFailsForNonAlphaNumDashSpaceItems() {
     
    $this->assertFalse($this->val->is_alphanumdashspace('abc#123'));
    $this->assertFalse($this->val->is_alphanumdashspace("abc 123\tZEF"));
    $this->assertFalse($this->val->is_alphanumdashspace('BARF%ONLY#IZA$BOO@1234'));   
  }   
  
  /*
   * Public Functionality Tests
   */
  public function testValidateReturnsFailForAtleastOneFailure() {

    $this->assertFalse($this->val->validate('test', 'abc123', "Test Field", array('is_required', 'is_numeric')));
    $this->assertFalse($this->val->validate('test', '', 'Test Field', array('is_required')));
  }
  
  public function testValidateReturnsTrueForAllSuccesses() {
    
    $this->assertTrue($this->val->validate('test', 'abc', 'Test Field', array('is_required')));
    $this->assertTrue($this->val->validate('test', '123', 'Test Field', array('is_required', 'is_numeric')));
    
  }
   
  public function testSetRulesAndRunReturnsTrueForValidData() {
    
    $this->val->set_rules('data1', 'Hello World!', 'Data 1', "required");
    $this->val->set_rules('data2', '1234567890', 'Data 2', "required|is_numeric");
    $this->val->set_rules('data3', '1234567890', 'Data 3', "required|is_numeric|is_exactly[1234567890]");
    
    $this->assertTrue($this->val->run());
  }
  
  public function testSetRulesAndRunReturnsFalseForInvalidData() {
    
    $this->val->set_rules('data1', 'Hello World!', 'Data 1', "required|is_alphanumeric");
    $this->val->set_rules('data2', '1234567890', 'Data 2', "required|is_numeric");
    $this->val->set_rules('data3', '1234567890', 'Data 3', "required|is_numeric|is_exactly[1234567890]");
    
    $this->assertFalse($this->val->run());    
  }
  
  public function testSetPostRulesAndRunReturnsTrueForValidData() {
    
    $_POST['data1'] = 'Hello World!';
    $_POST['data2'] = '1234567890';
    $_POST['data3'] = '1234567890';
    
    $this->val->set_post_rules('data1', 'Data 1', "required");
    $this->val->set_post_rules('data2', 'Data 2', "required|is_numeric");
    $this->val->set_post_rules('data3', 'Data 3', "required|is_numeric|is_exactly[1234567890]");   
    
    $this->assertTrue($this->val->run());
    
    $_POST = array();
  }
    
  public function testSetPostRulesAndRunReturnsFalseForInvalidData() {
     
    $_POST['data1'] = 'Hello World!';
    $_POST['data2'] = '1234567890';
    $_POST['data3'] = '1234567890';
    
    $this->val->set_post_rules('data1', 'Data 1', "required|is_alphanumeric");
    $this->val->set_post_rules('data2', 'Data 2', "required|is_numeric");
    $this->val->set_post_rules('data3', 'Data 3', "required|is_numeric|is_exactly[1234567890]");   
    
    $this->assertFalse($this->val->run());   
    
    $_POST = array();
  }
  
  public function testFailedValidationGeneratesErrorMessages() {
    
    $this->val->set_rules('data1', 'Hello World!', 'Data 1', "required|is_alphanumeric");
    $this->val->set_rules('data2', '123456abc7890', 'Data 2', "required|is_numeric");
    $this->val->set_rules('data3', '123456abc7890', 'Data 3', "required|is_numeric|is_exactly[1234567890]");  
    $this->val->run();
    
    $this->assertGreaterThan(0, count($this->val->get_error_messages_for_field('data1')));
    $this->assertGreaterThan(0, count($this->val->get_error_messages_for_field('data2')));
    $this->assertGreaterThan(0, count($this->val->get_error_messages_for_field('data3'))); 
    $this->assertArrayHasKey('data1', $this->val->get_error_messages());
    $this->assertArrayHasKey('data2', $this->val->get_error_messages());
    $this->assertArrayHasKey('data3', $this->val->get_error_messages());
  }
}

/* EOF: ValidatorTest.php */