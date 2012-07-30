<?php

require_once(__DIR__ . '/../Formula/Fields/Abstracts/Field.php');
require_once(__DIR__ . '/../Formula/Fields/Abstracts/Input.php');
require_once(__DIR__ . '/../Formula/Fields/Abstracts/MultipleChoiceInput.php');
require_once(__DIR__ . '/../Formula/Fields/Dropdown.php');

class FieldDropdownTest extends PHPUnit_Framework_TestCase {
  
  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }  
  
  /*
   * Tests
   */
  
  public function testCreateDropdownFieldInNewObject() {

    $dropdownField = new \Formula\Fields\Dropdown('test', NULL, 'formy');
    $this->assertInstanceOf('Formula\fields\Dropdown', $dropdownField);
  }
  
  public function testRenderProducesExpectedHTMLSegments() {
    
    $dropdownField = new \Formula\Fields\Dropdown('test', NULL, 'formy');
    $dropdownField = $dropdownField->asHtml();
    

    $this->assertContains("<select", $dropdownField);
    $this->assertContains("<div", $dropdownField);
    
  }

  // @TODO Test creation of a Dropdown allowOther set to TRUE.

  // public function testCreateDropdownFieldWithOtherInNewObject() {

  //   $dropdownField = new \Formula\Fields\Dropdown('test', NULL, 'formy');
  //   $dropdownField = $dropdownField->asHtml();

  //   //$dropdownField->allowOther = TRUE;
  //   $this->assertContains("has_other", $dropdownField);
  // }
  
}
