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
