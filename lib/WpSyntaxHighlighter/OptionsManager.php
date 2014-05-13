<?php

namespace WpSyntaxHighlighter;

class OptionsManager extends \Arrow\OptionsManager\OptionsManager {

  function __construct($container) {
    parent::__construct($container);

    $container
      ->singleton('optionsPage', 'WpSyntaxHighlighter\OptionsPage')
      ->singleton('optionsValidator', 'WpSyntaxHighlighter\OptionsValidator');
  }

}
