<?php

/**
 * @file Markup Field Class
 * @package Formula
 * @author Casey McLaughlin
 */

// ---------------------------------------------------------------------------

namespace Formula\Fields;

class Markup extends Abstracts\Field {

  // -----------------------------------------------------------

  /**
   * @var string
   */
  public $markup = '';

  /**
   * @var boolean
   */
  public $wrapMarkup = FALSE;

  // -----------------------------------------------------------

  protected function render() {

    $outHtml  = ($this->label) ? sprintf("<label>%s</label>", $this->label) : '';
    $outHtml .= $this->markup;

    return $outHtml;
  }

  // -----------------------------------------------------------

  /**
   * Override the parent html renderer to not include the outside wrapper unless we want it
   *
   * @param array classes
   */
  public function asHtml($classes = NULL) {

    if ($this->wrapMarkup) {
      return parent::asHtml($classes);
    }
    else {

      $html = $this->render();

      //Before and after HTML
      $before      = $this->before ? sprintf(self::$beforeWrapperHtml, $this->before) : NULL;
      $after       = $this->after ? sprintf(self::$afterWrapperHtml, $this->after) : NULL;
      $description = $this->description ? sprintf(self::$descriptionWrapperHtml, $this->description) : NULL;
      return $before . $html . $after . $description;
    }

  }

}

/* EOF: Markup.php */