<?php

namespace Formula\Fields\Abstracts;

abstract class Field {
  
  /**
   * @var string
   */
  public $name;
  
  /**
   * @var string
   */
  public $label;
  
  /**
   * @var string
   */
  public $attrs;

  /**
   * @var string
   */
  public $classes;

  /**
   * @var string
   */
  public $before;
  
  /**
   * @var string
   */
  public $after;
  
  /**
   * @var string
   */
  public $description; 
 
  /**
   * Automatically identified
   * @var type 
   */
  public $type;
  
  /**
   * Automatically identified
   * @var string 
   */
  public $id;
  
  /**
   * Form ID
   * @var string 
   */
  public $form_id = 'form';
  
	// -----------------------------------------------------------
  
  /**
   * Data for rendering the field
   * @var array|string
   */
  protected $_data = NULL;
  
	// -----------------------------------------------------------
    
  /**
   * Constructor
   * 
   * @param array|string $data  Typically, existing 
   */
  public function __construct($name, $data = NULL, $form_id = 'form') {
    
    $this->type    = strtolower(__CLASS__);
    $this->name    = $name;
    $this->_data   = $data;
    $this->form_id = $form_id;
    
    $this->id = $form_id . '_' . $name;
  }
  
  // ------------------------------------------------------------
  
  //Render Method Returns a String of HTML
  abstract protected function render();
  
  // ------------------------------------------------------------
  
  /**
   * Render the field as HTML
   * 
   * @param string $html  Optionally, manually enter HTML to use
   * @return string
   */
  public function as_html() {
    
    $html = $this->render();
    
    $before = $this->before ? "<span class='before'>$this->before</span>" : NULL;
    $after = $this->after ? "<span class='after'>$this->after</span>" : NULL;
    
    $html = $before . $html . $after;
    $html = "<div class='{$this->form_id}_{$this->name}'>" . $html . "</div>";
    return $html;
  }
  
  // ------------------------------------------------------------

  /**
   * Render the field as JSON
   * 
   * @return string
   */
  public function as_json() {
    
    $obj = clone $this;
    unset($obj->_data);
    return $obj;
    
  }
  
  // ------------------------------------------------------------
  
  /**
   * Get Validation Rules
   * 
   * @throws RuntimeException 
   * @return array;
   */
  public function get_validation_rules() {
    
    if ( ! isset($this->validation_rules))
      throw new RuntimeException("Cannot get validation Rules for non-input field!");
    
    return array();
  }
  
  // ------------------------------------------------------------
 
  /**
   * Alias for 'as_json'
   * 
   * @return string
   */
  public function __toString() {
    
    return $this->as_json();
    
  }
 
  // ------------------------------------------------------------
 
  /**
   * Get the attributes as an array if they are not already
   * 
   * @return array
   */
  protected function get_attrs() {
    
    return ( ! is_array($this->attrs)) ? explode(" ", $this->attrs) : $this->attrs;
    
  }
  
  // ------------------------------------------------------------
 
  protected function render_attrs($attrs) {
    
    if (is_array($attrs)) {
      $attrs = array_filter($attrs);
      foreach($attrs as $k => &$v) {
        $v = "$k='$v'";
      }
      unset($v);
      
      $attrs = implode(' ', $attrs);
    }
    
    return $attrs;
  }
  
}

/* EOF: Field.php */
