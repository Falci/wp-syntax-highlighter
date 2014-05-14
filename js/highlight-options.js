(function($) {

  var LanguageFinder = function() {

  };

  LanguageFinder.prototype.find = function() {
    var names = this.getNames();
    var languages = [];
    var n = names.length;
    var name;
    var langName;

    for (var i = 0; i < n; i++) {
      name = names[i];
      langName = this.getLanguageName(name);
      rules = this.findRules(langName);

      languages.push({ name: name, rules: rules});
    }

    return languages;
  };

  LanguageFinder.prototype.getNames = function() {
    return highlight_options.languages;
  };

  LanguageFinder.prototype.getLanguageName = function(name) {
    return 'highlightjslang' + name;
  };

  LanguageFinder.prototype.findRules = function(langName) {
    return window[langName];
  };

  var SyntaxHighlighter = function() {
    this.hljs = new highlightjslib();
    this.configured = false;
  };

  SyntaxHighlighter.prototype.configure = function() {
    var finder = new LanguageFinder();
    var languages = finder.find();
    var n = languages.length;
    var language;

    for (var i = 0; i < n; i++) {
      language = languages[i];
      this.hljs.registerLanguage(language.name, language.rules);
    }

    this.hljs.configure({
      useBR: false
    });

    this.configured = true;
  };

  SyntaxHighlighter.prototype.highlight = function(blocks) {
    if (!this.configured) {
      this.configure();
    }

    var n = blocks.length;
    var block;

    for (var i = 0; i < n; i++) {
      block = blocks[i];
      this.highlightBlock(block);
    }
  };

  SyntaxHighlighter.prototype.highlightBlock = function(block) {
    this.hljs.highlightBlock(block);
  };

  var CodeBlockFinder = function() {

  };

  CodeBlockFinder.prototype.find = function() {
    var blocks = [];

    this.append(blocks, this.findPreCode());

    if (this.isDetectable('SyntaxHighlighter')) {
      this.append(blocks, this.findPreClass());
    }

    if (this.isDetectable('Geshi')) {
      this.append(blocks, this.findPreLang());
    }

    return blocks;
  };

  CodeBlockFinder.prototype.append = function(blocks, found) {
    blocks.push.apply(blocks, found);
  };

  CodeBlockFinder.prototype.findPreCode = function() {
    return $('pre code');
  };

  CodeBlockFinder.prototype.findPreClass = function() {
    return $("pre[class*=brush]");
  };

  CodeBlockFinder.prototype.findPreLang = function() {
    return $("pre[lang*=brush]");
  };

  CodeBlockFinder.prototype.isDetectable = function(name) {
    var prop = 'highlight' + name;
    if (highlight_options.hasOwnProperty(prop)) {
      return highlight_options[prop];
    } else {
      return false;
    }
  };

  $(document).ready(function() {
    var highlighter = new SyntaxHighlighter();
    var finder      = new CodeBlockFinder();
    var blocks      = finder.find();

    highlighter.highlight(blocks);
  });

}(jQuery));
