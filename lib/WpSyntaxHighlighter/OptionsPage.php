<?php

namespace WpSyntaxHighlighter;

class OptionPage {

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
