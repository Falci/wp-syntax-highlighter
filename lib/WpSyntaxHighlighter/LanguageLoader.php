<?php

namespace WpSyntaxHighlighter;

use WordPress\Logger;

class LanguageLoader {

  public $scriptLoader;
  public $stylesheetLoader;
  public $pluginVersion;

  protected $didCore = false;
  protected $languages = array();

  function needs() {
    return array('scriptLoader', 'stylesheetLoader');
  }

  function add($language) {
    if (!$this->didCore) {
      $this->loadCore();
    }

    if ($this->hasLanguage($language)) {
      return;
    }

    array_push($this->languages, $language);

    $slug    = $this->slugFor($language);
    $this->scriptLoader->stream($slug, array(
      'dependencies' => 'highlight')
    );
  }

  function load($localizer = null) {
    $options = array();
    $options['dependencies'] = array('highlight');

    if (!is_null($localizer)) {
      $options['localizer'] = $localizer;
    }

    $this->scriptLoader->stream(
      'highlight-options', $options
    );
  }

  function loadCore() {
    $this->scriptLoader->stream('highlight');
    $this->didCore = true;
  }

  function slugFor($language) {
    return "languages/$language";
  }

  function getLanguages() {
    return $this->languages;
  }

  function hasLanguage($language) {
    return in_array($language, $this->languages);
  }

}
