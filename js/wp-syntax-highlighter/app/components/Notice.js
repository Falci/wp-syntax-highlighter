/** @jsx React.DOM */

var React = require('react');

var Notice = React.createClass({

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
        <div id="message" className={this.getClassList()}>
          <MessageList messages={this.getMessages()} />
        </div>
      );
    } else {
      return (<div />);
    }
  }
});

var MessageList = React.createClass({

  renderMessage: function(message, index) {
    return (
      <p key={index}>
        <strong>{ message }</strong>
      </p>
    );
  },

  render: function() {
    return (
      <div>
        {this.props.messages.map(this.renderMessage)}
      </div>
    );
  },

});

module.exports = Notice;
