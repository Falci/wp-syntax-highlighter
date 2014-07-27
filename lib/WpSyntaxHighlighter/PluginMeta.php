<?php

namespace WpSyntaxHighlighter;

class PluginMeta extends \Arrow\PluginMeta {

  function getVersion() {
    return Version::$version;
  }

  function getDefaultOptions() {
    return array(
      'theme'                      => 'default',
      'highlightSyntaxHighlighter' => true,
      'highlightGeshi'             => true
    );
  }

  function getLanguages() {
    return Languages::$names;
  }

  function getThemes() {
    $themes = Themes::$names;
    array_push($themes, 'custom');

    return $themes;
  }

  function getOptionsContext() {
    return $this->lookup('optionsStore')->getOptions();
  }

  function getLocalizedStrings() {
    $strings = array();
    $strings['themes'] = $this->getThemes();

    return $strings;
  }

}
