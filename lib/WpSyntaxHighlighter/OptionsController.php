<?php

namespace WpSyntaxHighlighter;

class OptionsController extends \Arrow\Options\Controller {

  function patch() {
    $validator = $this->getValidator()
      ->rule('required', 'highlightSyntaxHighlighter')
      ->rule('boolean', 'highlightSyntaxHighlighter')

      ->rule('required', 'highlightGeshi')
      ->rule('boolean', 'highlightGeshi')

      ->rule('required', 'theme')
      ->rule('in', 'theme', $this->pluginMeta->getThemes());

    if ($validator->validate()) {
      return parent::patch();
    } else {
      return $this->error($validator->errors());
    }
  }

}
