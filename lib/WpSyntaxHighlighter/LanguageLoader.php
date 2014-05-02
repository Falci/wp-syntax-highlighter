<?php

namespace WpSyntaxHighlighter;

class LanguageLoader {

  public $scriptLoader;
  public $pluginVersion;

  protected $didCore = false;
  protected $scriptOptions = null;
  protected $languages = array();

  function needs() {
    return array('scriptLoader', 'pluginVersion');
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
    $options = $this->getScriptOptions();

    $this->scriptLoader->schedule($slug, $options);
    $this->scriptLoader->dependency($slug, 'highlight');
  }

  function load() {
    $this->scriptLoader->schedule('highlight-run');
    $this->scriptLoader->dependency('highlight-run', 'highlight');
    $this->scriptLoader->localize(
      'highlight-run', array($this, 'getHighlightOptions')
    );

    $this->scriptLoader->load();
  }

  function loadCore() {
    $this->scriptLoader->schedule('highlight');
    $this->didCore = true;
  }

  function slugFor($language) {
    return "languages/$language";
  }

  function getScriptOptions() {
    if (is_null($this->scriptOptions)) {
      $this->scriptOptions = array(
        'version' => $this->pluginVersion,
        'in_footer' => true
      );
    }

    return $this->scriptOptions;
  }

  function getHighlightOptions($script) {
    $options = array();
    $options['languages'] = $this->languages;

    return $options;
  }

  function getLanguages() {
    return $this->languages;
  }

  function hasLanguage($language) {
    return in_array($language, $this->languages);
  }

}
