<?php

require_once(__DIR__ . '/../Formula/Fields/Abstracts/Field.php');
require_once(__DIR__ . '/../Formula/Fields/Abstracts/Input.php');
require_once(__DIR__ . '/../Formula/Fields/Abstracts/OpenEndedInput.php');
require_once(__DIR__ . '/../Formula/Fields/Text.php');

class FieldTextTest extends PHPUnit_Framework_TestCase {
  
  public function setUp() {
    parent::setUp();
  }

  public function tearDown() {
    parent::tearDown();
  }  
  
  /*
   * Tests
   */
  
  public function testCreateTextFieldResultsInNewObject() {

    $textfield = new \Formula\Fields\Text('test', NULL, 'formy');
    $this->assertInstanceOf('Formula\fields\Text', $textfield);
  }
  
  public function testRenderProducesExpectedHTMLSegments() {
    
    $textfield = new \Formula\Fields\Text('test', NULL, 'formy');
    $textfield = $textfield->asHtml();
    
    $this->assertContains("type='text'", $textfield);
    $this->assertContains("<input", $textfield);
    $this->assertContains("<div", $textfield);
    
  }
  
}
