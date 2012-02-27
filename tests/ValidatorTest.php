<?php

use Formula;

class ValidatorTest extends PHPUnitTestCase {
  
  private $val;
  
  public function setUp() {
    
    $this->val = new Validator();
    
    parent::setUp();
  }
  
  public function tearDown() {
    parent::tearDown();
  }
      
  public function testIsRequiredFailsForEmptyStrings() {
    
   $this->assertEquals(FALSE, $this->val->is_required('')); 
   $this->assertEquals(FALSE, $this->val->is_required('  '));
   $this->assertEquals(FALSE, $this->val->is_required(NULL));
  }
  
  public function testIsRequiredSucceedsForNonEmptyStrings() {
    
    $this->assertEquals(TRUE, $this->val->is_required("HEYA"));
    $this->assertEquals(TRUE, $this->val->is_required(array(1, 2, 3)));
    $this->assertEquals(TRUE, $this->val->is_required(0));
  }
}

/* EOF: ValidatorTest.php */