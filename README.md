=== wp-syntax-highlighter ===
Contributors: dsawardekar
Donate link: http://pressing-matters.io/
Tags: syntax highlighter, code highlighter, source highlighter, sourcecode highlighter
Requires at least: 3.5.0
Tested up to: 3.9.2
Stable tag: 0.5.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Syntax Highlighter for WordPress using Highlight.js.

== Description ==

WP-Syntax-Highlighter is a WordPress plugin that brings the [Highlight.js](http://highlightjs.org) based source code syntax
highlighting to WordPress. This plugin is compatible with any other syntax highlighter
plugins that use [Syntax Highlighter](http://alexgorbatchev.com/SyntaxHighlighter/), [GeSHi](http://qbnz.com/highlighter/)
or [Prismjs](http://prismjs.com).

== Installation ==

1. Click Plugins > Add New in the WordPress admin panel.
1. Search for "wp-syntax-highlighter" and install.

###Customization###

The plugin comes bundled with all the themes provided by Highlight.js.
Use the Theme dropdown to change the selected theme.

Additionally the plugin checks for a `custom.css` in your current
theme's directory at *{current_theme}/wp-syntax-highlighter/custom.css*.

If this CSS file is present it will be added to the page automatically.
You can override any builtin theme's styles in this manner.

To use a completely custom theme use the *Custom* option in Theme
dropdown. Now only your current theme's custom.css will be used to style
the code blocks.

###Shortcodes###

Each language gets it's own shortcode. You can use this to add code
blocks for that language into your posts.

For instance to add some PHP code into your post use,

    `
    [php]
    foreach ($requirements as $requirement) {
      $result = array(
        'satisfied' => $requirement->check(),
        'requirement' => $requirement
      );

      array_push($results, $result);

      if (!$result['satisfied']) {
        $success = false;
      }
    }
    [/php]
    `

### Supported Languages ###

The following languages are currently supported by highlight.js.

1. 1c
1. Actionscript
1. Apache
1. Applescript
1. Asciidoc
1. Autohotkey
1. Avrasm
1. Axapta
1. Bash
1. Brainfuck
1. Clojure
1. Cmake
1. Coffeescript
1. Cpp
1. CS
1. CSS
1. D
1. Delphi
1. Diff
1. Django
1. Dos
1. Erlang
1. Erlang-repl
1. Fix
1. Fsharp
1. Glsl
1. Go
1. Haml
1. Handlebars
1. Haskell
1. Http
1. Ini
1. Java
1. Javascript
1. JSON
1. Lasso
1. Lisp
1. Livecodeserver
1. Lua
1. Makefile
1. Markdown
1. Mathematica
1. Matlab
1. Mel
1. Mizar
1. Nginx
1. ObjectiveC
1. Ocaml
1. Oxygene
1. Parser3
1. Perl
1. Php
1. Profile
1. Python
1. R
1. Rib
1. Rsl
1. Ruby
1. Ruleslanguage
1. Rust
1. Scala
1. Scilab
1. SCSS
1. Smalltalk
1. SQL
1. Tex
1. Vala
1. Vbnet
1. Vbscript
1. Vhdl
1. XML

Note: Since WordPress shortcodes must be at least 2 characters long, for
language names shorter than 2 characters use the `lang` suffix.

Eg:- [dlang][/dlang]

### Compatibility with other Syntax Highlighters ###

The plugin is completely compatible with other syntax highlighters.
It does not change the content of your existing posts. You can safely go
back to use your previous syntax highlighter after trying out
wp-syntax-highlighter.

Both [Syntax Highlighter](http://alexgorbatchev.com/SyntaxHighlighter/) and [GeSHi](http://qbnz.com/highlighter/)
and supported by default. [Prism](http://prismjs.com/) based syntax highlighters will also
work out of the box as they utilize the same markup as Highlight.js.

== Screenshots ==

1. Screenshot 1
2. Screenshot 2

== Credits ==

* Thanks to Ivan Sagalaev and the [highlight.js](http://highlightjs.org/) team for this excellent library.

== Upgrade Notice ==

* WP-Syntax-Highlighter requires PHP 5.3.2+

== Frequently Asked Questions ==

* Can I go back to my previous syntax highlighter plugin after using
  this?

Yes. The code block scanning is done clientside by highlight.js. The
contents of your posts are only scanned to pick any embedded languages.

== Changelog ==

= 0.5.1 =

* Upgrades to Arrow 1.8.0.

= 0.5.0 =

* Upgrades to Arrow 1.6.0.
* Updates Highlight.js to 8.1.
* Refreshes themes from Highlight.js repository.
* Cleaner build without development assets.

= 0.4.0 =

* Upgrades to Arrow 0.7.0

= 0.3.0 =

* Upgrades to Arrow 0.3.0.

= 0.2.2 =

* Fixes typos.

= 0.2.1 =

* Upgrades Arrow to 0.4.1.

= 0.2.0 =

* Switches to Arrow 0.4.0.

= 0.1.2 =

* First release on wordpress.org

= 0.1.0 =

* Initial Release
