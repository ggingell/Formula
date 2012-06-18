<?php

require_once(__DIR__ . '/../Formula/Validator.php');

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

   $this->assertFalse($this->val->isRequired(''));
   $this->assertFalse($this->val->isRequired('  '));
   $this->assertFalse($this->val->isRequired(NULL));
  }

  public function testIsRequiredSucceedsForNonEmptyStrings() {

    $this->assertTrue($this->val->isRequired("HEYA"));
    $this->assertTrue($this->val->isRequired(array(1, 2, 3)));
    $this->assertTrue($this->val->isRequired(0));
  }

  public function testIsNumericSucceedsForAnyNumber() {

    $this->assertTrue($this->val->isNumeric('123'));
    $this->assertTrue($this->val->isNumeric('-123'));
    $this->assertTrue($this->val->isNumeric('233.23'));
    $this->assertTrue($this->val->isNumeric('04.2'));
  }

  public function testIsNumericFailsForNonNumbers() {

    $this->assertFalse($this->val->isNumeric('abc123'));
    $this->assertFalse($this->val->isNumeric('234-24'));
    $this->assertFalse($this->val->isNumeric('barf'));
    $this->assertFalse($this->val->isNumeric('sl20.2340as'));
  }

  public function testIsIntegerSucceedsForIntegers() {

     $this->assertTrue($this->val->isInteger('123'));
     $this->assertTrue($this->val->isInteger('-123'));
     $this->assertTrue($this->val->isInteger('0'));
  }

  public function testIsIntegerFailsForNonIntegers() {

     $this->assertFalse($this->val->isInteger('123.45'));
     $this->assertFalse($this->val->isInteger('barf'));
  }

  public function testIsEmailAddressSucceedsForValidAddresses() {

    $this->assertTrue($this->val->isEmailAddress('abc@example.com'));
    $this->assertTrue($this->val->isEmailAddress('abc@baz.example.com'));
  }

  public function testIsEmailAddressFailsForNonAddresses() {

    $this->assertFalse($this->val->isEmailAddress('not_an_email'));
    $this->assertFalse($this->val->isEmailAddress('abc@baz.examp##leom'));
  }

  public function testIsOneOfSucceedsForValidSets() {

    $this->assertTrue($this->val->isOneOf('abc', array('abc', 'def')));
    $this->assertTrue($this->val->isOneOf('abc', array('abc')));
    $this->assertTrue($this->val->isOneOf('abc', 'abc'));
    $this->assertTrue($this->val->isOneOf('abc', 'abc;def'));
  }

  public function testIsOneOfFailsForInvalidSets() {

    $this->assertFalse($this->val->isOneOf('abc', array('def', 'ghi')));
    $this->assertFalse($this->val->isOneOf('abc', array('def')));
    $this->assertFalse($this->val->isOneOf('abc', 'def'));
    $this->assertFalse($this->val->isOneOf('abc', 'def;ghi'));

  }

  public function testIsUrlSucceedsForValidUrls() {
    $this->assertTrue($this->val->isUrl('http://www.example.com'));
    $this->assertTrue($this->val->isUrl('https://www.example.com?some=complicated&query=string&123=here'));
    $this->assertTrue($this->val->isUrl('https://10.10.5.5'));
  }

  public function testIsUrlFailsForInvalidUrls() {
    $this->assertFalse($this->val->isUrl('http:/www.example.com'));
    $this->assertFalse($this->val->isUrl('example.com'));
    $this->assertFalse($this->val->isUrl('10.10.5.5'));
  }

  public function testIsValidIPSucceedsForIPAddresses() {

    $this->assertTrue($this->val->isValidIP('10.5.5.5'));
    $this->assertTrue($this->val->isValidIP('fe80:0:0:0:200:f8ff:fe21:67cf'));

  }

  public function testIsValidIPFailsForNonIPAddress() {

    $this->assertFalse($this->val->isValidIP('10.5.5.5.5'));
    $this->assertFalse($this->val->isValidIP('1234567'));
    $this->assertFalse($this->val->isValidIP('some.dns.name.com'));
  }

  public function testIsValidHostnameSucceedsForValidHostname() {

    $this->assertTrue($this->val->isValidHostname('10.5.5.5'));
    $this->assertTrue($this->val->isValidHostname('fe80:0:0:0:200:f8ff:fe21:67cf'));
    $this->assertTrue($this->val->isValidHostname('some.dns.name.com'));
  }

  public function testIsValidHostnameFailsForInvalidHostname() {

    $this->assertFalse($this->val->isValidHostname('10.5.5.5.5'));
    $this->assertFalse($this->val->isValidHostname('1234567'));
    $this->assertFalse($this->val->isValidHostname('blargh#3048#)*(@_.com'));
  }

  public function testIsValidIPv4SucceedsForIPv4Address() {

    $this->assertTrue($this->val->isValidIPv4('10.5.5.5'));
    $this->assertTrue($this->val->isValidIPv4('128.186.72.1'));

  }

  public function testIsValidIPv4SucceedsForNonIPv4Address() {

    $this->assertFalse($this->val->isValidIPv4('fe80:0:0:0:200:f8ff:fe21:67cf'));
    $this->assertFalse($this->val->isValidIPv4('10.5.5.5.5'));
    $this->assertFalse($this->val->isValidIPv4('some.dns.name.com'));
  }

  public function testIsPublicIPSucceedsForPublicIPs() {

    $this->assertTrue($this->val->isPublicIP('128.186.72.1'));
    $this->assertTrue($this->val->isPublicIP('5.5.5.5'));
    $this->assertTrue($this->val->isPublicIP('e80:0:0:0:200:f8ff:fe21:67cf'));
  }

  public function testIsPublicIPFailsForPrivateIPs() {
    $this->assertFalse($this->val->isPublicIP('10.5.5.5'));
    $this->assertFalse($this->val->isPublicIP('192.168.1.5'));
    $this->assertFalse($this->val->isPublicIP('FDC8:BF8B:E62C:ABCD:1111:2222:3333:4444'));

  }

  public function testIsValidIPv6SucceedsForIPv6Address() {
    $this->assertTrue($this->val->isValidIPv6('FDC8:BF8B:E62C:ABCD:1111:2222:3333:4444'));
    $this->assertTrue($this->val->isValidIPv6('e80:0:0:0:200:f8ff:fe21:67cf'));
  }

  public function testIsValidIPv6FailsForNonIPv6Address() {
    $this->assertFalse($this->val->isValidIPv6('128.186.72.1'));
    $this->assertFalse($this->val->isValidIPv6('barf'));
    $this->assertFalse($this->val->isValidIPv6('10.5.5.5'));
  }

  public function testIsLessThanReturnsTrueForValidNumbers() {

    $this->assertTrue($this->val->isLessThan('12', 15));
    $this->assertTrue($this->val->isLessThan(12, '15'));
    $this->assertTrue($this->val->isLessThan(120.53, 120.531));
    $this->assertTrue($this->val->isLessThan(-2, -1));
  }

  public function testIsLessThanReturnsFalseForInvalidNumbers() {
    $this->assertFalse($this->val->isLessThan('16', 15));
    $this->assertFalse($this->val->isLessThan(18, '15'));
    $this->assertFalse($this->val->isLessThan(120.53, 120.529));
    $this->assertFalse($this->val->isLessThan(-1, -2));
  }

  public function testIsGreaterThanReturnsTrueForValidNumbers() {
    $this->assertTrue($this->val->isGreaterThan('16', 15));
    $this->assertTrue($this->val->isGreaterThan(18, '15'));
    $this->assertTrue($this->val->isGreaterThan(120.53, 120.529));
    $this->assertTrue($this->val->isGreaterThan(-1, -2));

  }

  public function testIsGreaterThanReturnsFalseForValidNumbers() {

    $this->assertFalse($this->val->isGreaterThan('12', 15));
    $this->assertFalse($this->val->isGreaterThan(12, '15'));
    $this->assertFalse($this->val->isGreaterThan(120.53, 120.531));
    $this->assertFalse($this->val->isGreaterThan(-2, -1));

  }

  public function testIsExactlySucceedsForExactMatches() {

    $this->assertTrue($this->val->isExactly(12, '12'));
    $this->assertTrue($this->val->isExactly(14.05, 14.05));
    $this->assertTrue($this->val->isExactly('abc 123', 'abc 123'));
    $this->assertTrue($this->val->isExactly(array(12, 34), array(12, 34)));
    $this->assertTrue($this->val->isExactly(TRUE, TRUE));

  }

  public function testIsExactlyFailsForNonExactMatches() {

    $this->assertFalse($this->val->isExactly(12, '12.1'));
    $this->assertFalse($this->val->isExactly(14.05, 14.04));
    $this->assertFalse($this->val->isExactly('abc 123', 'ABC 123'));
    $this->assertFalse($this->val->isExactly(array(12, 34), array(12, 35)));
    $this->assertFalse($this->val->isExactly(TRUE, FALSE));
  }

  public function testIsExactlyCaseInsensitiveSucceedsForExactMatches() {

    $this->assertTrue($this->val->isExactlyCaseInsensitive(12, '12'));
    $this->assertTrue($this->val->isExactlyCaseInsensitive(14.05, 14.05));
    $this->assertTrue($this->val->isExactlyCaseInsensitive('abc 123', 'ABC 123'));
    $this->assertTrue($this->val->isExactlyCaseInsensitive(array(12, 34), array(12, 34)));
    $this->assertTrue($this->val->isExactlyCaseInsensitive(TRUE, TRUE));
  }

  public function testIsExactlyCaseInsensitiveFailsForNonExactMatches() {

    $this->assertFalse($this->val->isExactlyCaseInsensitive(12, '12.1'));
    $this->assertFalse($this->val->isExactlyCaseInsensitive(14.05, 14.04));
    $this->assertFalse($this->val->isExactlyCaseInsensitive('abc 123', 'abd 123'));
    $this->assertFalse($this->val->isExactlyCaseInsensitive(array(12, 34), array(12, 35)));
    $this->assertFalse($this->val->isExactlyCaseInsensitive(TRUE, FALSE));

  }

  public function testIsContainingSucceedsForItemsThatContainValidValues() {

    $this->assertTrue($this->val->isContaining(123, 2));
    $this->assertTrue($this->val->isContaining('abc', 'c'));
    $this->assertTrue($this->val->isContaining(array('a', 'b', 'c'), 'b'));
  }

  public function testIsContainingFailsForItemsThatContainValidValues() {

    $this->assertFalse($this->val->isContaining(123, 4));
    $this->assertFalse($this->val->isContaining('abc', 'd'));
    $this->assertFalse($this->val->isContaining(array('a', 'b', 'c'), 'd'));
  }

  public function testIsAlphanumericSucceedsForAlphaNumItems() {

    $this->assertTrue($this->val->isAlphaNumeric('abc123'));
    $this->assertTrue($this->val->isAlphaNumeric('abc123ZEF'));
    $this->assertTrue($this->val->isAlphaNumeric('BARFONLYIZABOO1234'));
  }

  public function testisAlphanumericFailsForNonAlphaNumItems() {

    $this->assertFalse($this->val->isAlphaNumeric('abc 123'));
    $this->assertFalse($this->val->isAlphaNumeric('abc-123-ZEF'));
    $this->assertFalse($this->val->isAlphaNumeric('BARF_ONLYIZ##ABOO1234'));

  }

  public function testsIsAlphaNumDashSucceedsForAlphaNumDashItems() {

    $this->assertTrue($this->val->isAlphaNumDash('abc123'));
    $this->assertTrue($this->val->isAlphaNumDash('abc123ZEF'));
    $this->assertTrue($this->val->isAlphaNumDash('BARFONLYIZABOO1234'));
  }

  public function testsIsAlphaNumDashFailsForNonAlphaNumDashItems() {

  }

  public function testsIsAlphaNumDashSpaceSucceedsForAlphaNumDashSpaceItems() {

    $this->assertTrue($this->val->isAlphaNumDashSpace('abc-123'));
    $this->assertTrue($this->val->isAlphaNumDashSpace('abc-123 ZEF'));
    $this->assertTrue($this->val->isAlphaNumDashSpace('BARF-ONLY_IZA BOO 1234'));
  }

  public function testsIsAlphaNumDashSpaceFailsForNonAlphaNumDashSpaceItems() {

    $this->assertFalse($this->val->isAlphaNumDashSpace('abc#123'));
    $this->assertFalse($this->val->isAlphaNumDashSpace("abc 123\tZEF"));
    $this->assertFalse($this->val->isAlphaNumDashSpace('BARF%ONLY#IZA$BOO@1234'));
  }

  /*
   * Public Functionality Tests
   */
  public function testValidateReturnsFailForAtleastOneFailure() {

    $this->assertFalse($this->val->validate('test', 'abc123', "Test Field", array('isRequired', 'isNumeric')));
    $this->assertFalse($this->val->validate('test', '', 'Test Field', array('isRequired')));
  }

  public function testValidateReturnsTrueForAllSuccesses() {

    $this->assertTrue($this->val->validate('test', 'abc', 'Test Field', array('isRequired')));
    $this->assertTrue($this->val->validate('test', '123', 'Test Field', array('isRequired', 'isNumeric')));

  }

  public function testSetRulesAndRunReturnsTrueForValidData() {

    $this->val->setRules('data1', 'Hello World!', 'Data 1', "isRequired");
    $this->val->setRules('data2', '1234567890', 'Data 2', "isRequired|isNumeric");
    $this->val->setRules('data3', '1234567890', 'Data 3', "isRequired|isNumeric|isExactly[1234567890]");

    $this->assertTrue($this->val->run());
  }

  public function testSetRulesAndRunReturnsFalseForInvalidData() {

    $this->val->setRules('data1', 'Hello World!', 'Data 1', "isRequired|isAlphaNumeric");
    $this->val->setRules('data2', '1234567890', 'Data 2', "isRequired|isNumeric");
    $this->val->setRules('data3', '1234567890', 'Data 3', "isRequired|isNumeric|isExactly[1234567890]");

    $this->assertFalse($this->val->run());
  }

  public function testSetPostRulesAndRunReturnsTrueForValidData() {

    $_POST['data1'] = 'Hello World!';
    $_POST['data2'] = '1234567890';
    $_POST['data3'] = '1234567890';

    $this->val->setInputRules('data1', 'Data 1', "isRequired");
    $this->val->setInputRules('data2', 'Data 2', "isRequired|isNumeric");
    $this->val->setInputRules('data3', 'Data 3', "isRequired|isNumeric|isExactly[1234567890]");

    $this->assertTrue($this->val->run());

    $_POST = array();
  }

  public function testSetPostRulesAndRunReturnsFalseForInvalidData() {

    $_POST['data1'] = 'Hello World!';
    $_POST['data2'] = '1234567890';
    $_POST['data3'] = '1234567890';

    $this->val->setInputRules('data1', 'Data 1', "isRequired|isAlphaNumeric");
    $this->val->setInputRules('data2', 'Data 2', "isRequired|isNumeric");
    $this->val->setInputRules('data3', 'Data 3', "isRequired|isNumeric|isExactly[1234567890]");

    $this->assertFalse($this->val->run());

    $_POST = array();
  }

  public function testFailedValidationGeneratesErrorMessages() {

    $this->val->setRules('data1', 'Hello World!', 'Data 1', "isRequired|isAlphaNumeric");
    $this->val->setRules('data2', '123456abc7890', 'Data 2', "isRequired|isNumeric");
    $this->val->setRules('data3', '123456abc7890', 'Data 3', "isRequired|isNumeric|isExactly[1234567890]");
    $this->val->run();

    $this->assertGreaterThan(0, count($this->val->getErrorMessagesForField('data1')));
    $this->assertGreaterThan(0, count($this->val->getErrorMessagesForField('data2')));
    $this->assertGreaterThan(0, count($this->val->getErrorMessagesForField('data3')));
    $this->assertArrayHasKey('data1', $this->val->getErrorMessages());
    $this->assertArrayHasKey('data2', $this->val->getErrorMessages());
    $this->assertArrayHasKey('data3', $this->val->getErrorMessages());
  }
}

/* EOF: ValidatorTest.php */
