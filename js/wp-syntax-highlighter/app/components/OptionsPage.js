/** @jsx React.DOM */
var React       = require('react');
var Notice      = require('./Notice');
var OptionsForm = require('./OptionsForm');

var OptionsPage = React.createClass({
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
      <div>
        <h2>WP-Spoiler-Alert Settings</h2>
        <div id="wp-spoiler-alert">
          <Notice notice={this.state.notice} />
          <OptionsForm
            options={this.props.options} noticeChange={this.onNotice}
            themes={this.props.themes}
          />
        </div>
      </div>
    );
  }

});

module.exports = OptionsPage;
