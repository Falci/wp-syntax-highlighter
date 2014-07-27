var $           = require('jquery');
var Promise     = require('es6-promise').Promise;

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
