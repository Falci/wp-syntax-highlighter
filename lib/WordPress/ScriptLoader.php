<?php

namespace WordPress;

use WordPress\AssetLoader;

class ScriptLoader extends AssetLoader {

  public function assetType() {
    return 'script';
  }

  function enqueueAction() {
    return 'wp_enqueue_scripts';
  }

}
