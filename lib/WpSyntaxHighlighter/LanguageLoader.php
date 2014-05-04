<?php

namespace WpSyntaxHighlighter;

use WordPress\Logger;

class LanguageLoader {

  public $scriptLoader;
  public $pluginVersion;

  protected $didCore = false;
  protected $languages = array();
  protected $defaultScriptOptions;

  function needs() {
    return array('scriptLoader', 'stylesheetLoader', 'pluginVersion');
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
    $options = $this->getScriptOptions(array('highlight'));

    $this->scriptLoader->stream($slug, $options);
  }

  function load($localizer) {
    $options = $this->getScriptOptions(array('highlight'));
    $options['localizer'] = $localizer;
    Logger::log('LanguageLoader::load', $options);

    $this->scriptLoader->stream('highlight-run', $options);
  }

  function loadCore() {
    $this->scriptLoader->stream('highlight', $this->getDefaultScriptOptions());
    $this->didCore = true;
  }

  function slugFor($language) {
    return "languages/$language";
  }

  function getDefaultScriptOptions() {
    if (is_null($this->defaultScriptOptions)) {
      $this->defaultScriptOptions = array(
        'version' => $this->pluginVersion,
        'in_footer' => true
      );
    }

    return $this->defaultScriptOptions;
  }

  function getScriptOptions($dependencies = null) {
    $options = $this->getDefaultScriptOptions();
    if (!is_null($dependencies)) {
      $options['dependencies'] = $dependencies;
    }

    return $options;
  }

  function getLanguages() {
    return $this->languages;
  }

  function hasLanguage($language) {
    return in_array($language, $this->languages);
  }

}
