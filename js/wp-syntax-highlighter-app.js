webpackJsonp([1],[
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	/** @jsx React.DOM */
	var $            = __webpack_require__(1);
	var React        = __webpack_require__(6);
	var OptionsPage  = __webpack_require__(3);
	var optionsStore = __webpack_require__(2).optionsStore;
	var config       = __webpack_require__(2).config;

	$(document).ready(function() {
	  $('.wrap-static').remove();

	  React.renderComponent(
	    OptionsPage({
	      options: optionsStore.getOptions(), 
	      themes: config.getThemes()}
	    ),
	    document.getElementById('wp-syntax-highlighter-app')
	  );
	});



/***/ },
/* 1 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = jQuery;

/***/ },
/* 2 */
/***/ function(module, exports, __webpack_require__) {

	/** @jsx React.DOM */

	__webpack_require__(16);

	var ArrowApi = __webpack_require__(9).ArrowApi;
	var Config   = __webpack_require__(7);
	var Options  = __webpack_require__(10);

	/* app initialization */
	var pluginSlug    = 'wp_syntax_highlighter';
	var config        = new Config(pluginSlug);
	var pluginOptions = config.getOptions();
	var api           = new ArrowApi(pluginOptions);
	var optionsStore  = new Options(pluginOptions, api);

	module.exports = {
	  pluginSlug   : pluginSlug,
	  api          : api,
	  config       : config,
	  optionsStore : optionsStore
	};


/***/ },
/* 3 */
/***/ function(module, exports, __webpack_require__) {

	/** @jsx React.DOM */
	var React       = __webpack_require__(6);
	var Notice      = __webpack_require__(11);
	var OptionsForm = __webpack_require__(12);

	var OptionsPage = React.createClass({displayName: 'OptionsPage',
	  getInitialState: function() {
	    return {
	      notice: { type: '', value: '' }
	    };
	  },

	  onNotice: function(type, value) {
	    var notice = { type: type, value: value };
	    this.setState({notice: notice});
	  },

	  render: function() {
	    return (
	      React.DOM.div(null, 
	        React.DOM.h2(null, "WP-Spoiler-Alert Settings"), 
	        React.DOM.div({id: "wp-spoiler-alert"}, 
	          Notice({notice: this.state.notice}), 
	          OptionsForm({
	            options: this.props.options, noticeChange: this.onNotice, 
	            themes: this.props.themes}
	          )
	        )
	      )
	    );
	  }

	});

	module.exports = OptionsPage;


/***/ },
/* 4 */,
/* 5 */,
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(15);


/***/ },
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	var Config = function(configKey) {
	  this.configKey = configKey;
	  this.load();
	};

	Config.prototype = {

	  load: function() {
	    this.configObj = window[this.configKey];
	  },

	  getOptions: function(name) {
	    return this.configObj.options;
	  },

	  getThemes: function() {
	    return this.configObj.themes;
	  },

	  translate: function(name) {
	    if (this.params.hasOwnProperty(name)) {
	      return this.params[name];
	    } else {
	      return name;
	    }
	  }

	};


	module.exports = Config;


/***/ },
/* 8 */,
/* 9 */
/***/ function(module, exports, __webpack_require__) {

	var $           = __webpack_require__(1);
	var Promise     = __webpack_require__(13).Promise;

	var PromiseHandler = function(ajaxPromise) {
	  ajaxPromise
	    .then(this.onAjaxSuccess.bind(this))
	    .fail(this.onAjaxFailure.bind(this));
	};

	PromiseHandler.prototype = {

	  handle: function(resolve, reject) {
	    this.resolve = resolve;
	    this.reject  = reject;
	  },

	  onAjaxSuccess: function(response) {
	    if (response === '0') {
	      this.reject('not_logged_in');
	    } else if (response.success) {
	      this.resolve(response.data);
	    } else {
	      this.reject(response.data.error);
	    }
	  },

	  onAjaxFailure: function(response) {
	    var error;
	    if (response.statusText === 'timeout') {
	      error = 'request_timeout';
	    } else if (response.responseJSON) {
	      error = response.responseJSON.data.error;
	    } else {
	      /* TODO: do some basic parsing of the HTML response */
	      error = 'unknown_response';
	    }

	    this.reject(error);
	  }

	};

	var ArrowApi = function(config) {
	  this.config = config;
	};

	ArrowApi.prototype = {

	  urlFor: function(controller, operation) {
	    var params        = {};
	    params.controller = controller;
	    params.operation  = operation;
	    params.nonce      = this.config.nonce;

	    var apiEndpoint = this.config.apiEndpoint;
	    var queryParams = $.param(params);

	    if (apiEndpoint.indexOf('?') === -1) {
	      return apiEndpoint + '?' + queryParams;
	    } else {
	      return apiEndpoint + '&' + queryParams;
	    }
	  },

	  paramsFor: function(controller, operation, params) {
	    if (typeof params !== 'object') {
	      params = {};
	    }

	    params.url = this.urlFor(controller, operation);

	    if (params.type === 'POST' && params.hasOwnProperty('data')) {
	      if (typeof params.data !== 'string') {
	        params.data = JSON.stringify(params.data);
	      }
	    }

	    return params;
	  },

	  request: function(controller, operation, params) {
	    params      = this.paramsFor(controller, operation, params);

	    var ajaxPromise = $.ajax(params);
	    var handler     = new PromiseHandler(ajaxPromise);
	    var handle      = handler.handle.bind(handler);
	    var promise     = new Promise(handle);

	    return promise;
	  },

	  all: function(resource) {
	    var params = { type: 'GET' };
	    return this.request(resource, 'all', params);
	  },

	  get: function(resource, data) {
	    var params = { type: 'GET', data: data };
	    return this.request(resource, 'get', params);
	  },

	  post: function(resource, data) {
	    var params = { type: 'POST', data: data };
	    return this.request(resource, 'post', params);
	  },

	  put: function(resource, data) {
	    var params = { type: 'POST', data: data };
	    return this.request(resource, 'put', params);
	  },

	  patch: function(resource, data) {
	    var params = { type: 'POST', data: data };
	    return this.request(resource, 'patch', params);
	  },

	  delete: function(resource, data) {
	    if (!data) {
	      data = {};
	    }
	    var params = { type: 'POST', data: data };
	    return this.request(resource, 'delete', params);
	  }

	};

	exports.ArrowApi       = ArrowApi;
	exports.PromiseHandler = PromiseHandler;


/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	var OptionsStore = function(options, api) {
	  this.options = options;
	  this.api     = api;
	};

	OptionsStore.prototype = {

	  getOptions: function() {
	    return this.options;
	  },

	  save: function(options) {
	    var promise = this.api.patch('options', options);
	    var self = this;

	    return promise.then(function(json) {
	      self.options = json;
	    });
	  },

	  reset: function() {
	    var promise = this.api.delete('options');
	    var self = this;

	    return promise.then(function(json) {
	      self.options = json;
	    });
	  }

	};

	module.exports = OptionsStore;


/***/ },
/* 11 */
/***/ function(module, exports, __webpack_require__) {

	/** @jsx React.DOM */

	var React = __webpack_require__(6);

	var Notice = React.createClass({displayName: 'Notice',

	  getDefaultProps: function() {
	    return {
	      notice: {
	        type: '',
	        value: ''
	      }
	    };
	  },

	  hasNotice: function() {
	    return !!this.props.notice.type;
	  },

	  getClassList: function() {
	    return 'updated ' + this.props.notice.type;
	  },

	  valueToMessages: function(value) {
	    var messages = [];
	    valueType = typeof(value);

	    if (valueType === 'string') {
	      /* plain text error */
	      messages.push(value);
	    } else if (valueType === 'object') {
	      /* list of errors, field => error */
	      messages.push.apply(messages, this.fieldsToMessages(value));
	    } else if (valueType === 'array') {
	      messages.push.apply(messages, value);
	    } else if (value instanceof Error) {
	      messages.push(value.toString());
	    } else {
	      messages.push('Unknown Error: ' + value);
	    }

	    return messages;
	  },

	  fieldsToMessages: function(fields) {
	    var messages = [];
	    for (var field in fields) {
	      if (fields.hasOwnProperty(field)) {
	        var errors = fields[field];
	        messages.push.apply(messages, errors);
	      }
	    }

	    return messages;
	  },

	  getMessages: function() {
	    if (this.hasNotice()) {
	      return this.valueToMessages(this.props.notice.value);
	    } else {
	      return [];
	    }
	  },

	  render: function() {
	    if (this.hasNotice()) {
	      return (
	        React.DOM.div({id: "message", className: this.getClassList()}, 
	          MessageList({messages: this.getMessages()})
	        )
	      );
	    } else {
	      return (React.DOM.div(null));
	    }
	  }
	});

	var MessageList = React.createClass({displayName: 'MessageList',

	  renderMessage: function(message, index) {
	    return (
	      React.DOM.p({key: index}, 
	        React.DOM.strong(null, message )
	      )
	    );
	  },

	  render: function() {
	    return (
	      React.DOM.div(null, 
	        this.props.messages.map(this.renderMessage)
	      )
	    );
	  },

	});

	module.exports = Notice;


/***/ },
/* 12 */
/***/ function(module, exports, __webpack_require__) {

	/** @jsx React.DOM */
	var React        = __webpack_require__(4);
	var optionsStore = __webpack_require__(2).optionsStore;
	var str          = __webpack_require__(8);

	var OptionsForm = React.createClass({displayName: 'OptionsForm',
	  mixins: [React.addons.LinkedStateMixin],

	  getInitialState: function() {
	    return this.props.options;
	  },

	  handleSubmit: function(event) {
	    event.preventDefault();
	    this.props.noticeChange('progress', 'Saving settings ...');

	    optionsStore.save(this.state)
	      .then(this.updateState)
	      .catch(this.showError);
	  },

	  handleReset: function(event) {
	    event.preventDefault();
	    var confirmed = confirm('Restore Defaults: Are you sure?');
	    if (!confirmed) return;

	    this.props.noticeChange('progress', 'Restoring defaults ...');

	    optionsStore.reset()
	      .then(this.updateState)
	      .catch(this.showError);
	  },

	  updateState: function() {
	    this.setState(optionsStore.getOptions());
	    this.props.noticeChange('success', 'Settings saved successfully.');
	  },

	  showError: function(error) {
	    this.props.noticeChange('error', error);
	  },

	  renderSelect: function(field, values, property) {
	    return (
	      React.DOM.select({id: field, name: field, valueLink: this.linkState(property)}, 
	        this.renderSelectOptions(values)
	      )
	    );
	  },

	  renderSelectOptions: function(values) {
	    values.sort();

	    var self = this;
	    return values.map(function(value, index) {
	      return (
	        React.DOM.option({
	          key: index, 
	          value: value}, self.formatThemeName(value))
	      );
	    });
	  },

	  formatThemeName: function(name) {
	    name = str.humanize(name);
	    name = name.replace('.', ' ');

	    return name;
	  },

	  render: function() {
	    return (
	      React.DOM.form({onSubmit: this.handleSubmit}, 
	        React.DOM.table({className: "form-table"}, 
	          React.DOM.tbody(null, 
	            React.DOM.tr(null, 
	              React.DOM.th({scope: "row"}, 
	                React.DOM.label({htmlFor: "theme"}, "Theme")
	              ), 
	              React.DOM.td(null, 
	                this.renderSelect('theme', this.props.themes, 'theme')
	              )
	            ), 

	            React.DOM.tr(null, 
	              React.DOM.th({scope: "row"}, 
	                React.DOM.label({htmlFor: "highlightSyntaxHighlighter"}, "Highlight Syntax Highlighter Code Blocks")
	              ), 
	              React.DOM.td(null, 
	                React.DOM.input({
	                  type: "checkbox", 
	                  id: "highlightSyntaxHighlighter", 
	                  name: "highlightSyntaxHighlighter", 
	                  checkedLink: this.linkState('highlightSyntaxHighlighter')})
	              )
	            ), 

	            React.DOM.tr(null, 
	              React.DOM.th({scope: "row"}, 
	                React.DOM.label({htmlFor: "highlightGeshi"}, "Highlight GeSHi Code Blocks")
	              ), 
	              React.DOM.td(null, 
	                React.DOM.input({
	                  type: "checkbox", 
	                  id: "highlightGeshi", 
	                  name: "highlightGeshi", 
	                  checkedLink: this.linkState('highlightGeshi')})
	              )
	            )

	          )
	        ), 
	        React.DOM.p({className: "submit"}, 
	          React.DOM.input({name: "submit", className: "button button-primary", value: "Save Changes", type: "submit"}), 
	          " ", 
	          React.DOM.input({name: "reset", className: "button", value: "Restore Defaults", type: "submit", onClick: this.handleReset})
	        )
	      )
	    );
	  }
	});

	module.exports = OptionsForm;


/***/ },
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	// removed by extract-text-webpack-plugin

/***/ }
]);