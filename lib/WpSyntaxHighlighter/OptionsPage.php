<?php

namespace WpSyntaxHighlighter;

class OptionsPage extends \Arrow\OptionsManager\OptionsPage {

  function getTemplateContext() {
    $context = array(
      'themes' => $this->pluginMeta->getThemes(),
      'theme' => $this->getOption('theme'),
      'highlightSyntaxHighlighter' => $this->getOption('highlightSyntaxHighlighter'),
      'highlightGeshi' => $this->getOption('highlightGeshi')
    );

    return $context;
  }

}
