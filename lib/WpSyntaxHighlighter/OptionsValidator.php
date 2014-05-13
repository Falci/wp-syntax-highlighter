<?php

namespace WpSyntaxHighlighter;

class OptionsValidator extends \Arrow\OptionsManager\OptionsValidator {

  function loadRules($validator) {
    $validator->rule('required', 'theme');
    $validator->rule('in', 'theme', $this->pluginMeta->getThemes());
  }

}
