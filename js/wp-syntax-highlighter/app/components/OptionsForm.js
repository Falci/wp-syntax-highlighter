/** @jsx React.DOM */
var React        = require('react/addons');
var optionsStore = require('../app').optionsStore;
var str          = require('underscore.string');

var OptionsForm = React.createClass({
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
      <select id={field} name={field} valueLink={this.linkState(property)}>
        {this.renderSelectOptions(values)}
      </select>
    );
  },

  renderSelectOptions: function(values) {
    values.sort();

    var self = this;
    return values.map(function(value, index) {
      return (
        <option
          key={index}
          value={value} >{self.formatThemeName(value)}</option>
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
      <form onSubmit={this.handleSubmit}>
        <table className="form-table">
          <tbody>
            <tr>
              <th scope="row">
                <label htmlFor="theme">Theme</label>
              </th>
              <td>
                {this.renderSelect('theme', this.props.themes, 'theme')}
              </td>
            </tr>

            <tr>
              <th scope="row">
                <label htmlFor="highlightSyntaxHighlighter">Highlight Syntax Highlighter Code Blocks</label>
              </th>
              <td>
                <input
                  type="checkbox"
                  id="highlightSyntaxHighlighter"
                  name="highlightSyntaxHighlighter"
                  checkedLink={this.linkState('highlightSyntaxHighlighter')} />
              </td>
            </tr>

            <tr>
              <th scope="row">
                <label htmlFor="highlightGeshi">Highlight GeSHi Code Blocks</label>
              </th>
              <td>
                <input
                  type="checkbox"
                  id="highlightGeshi"
                  name="highlightGeshi"
                  checkedLink={this.linkState('highlightGeshi')} />
              </td>
            </tr>

          </tbody>
        </table>
        <p className="submit">
          <input name="submit" className="button button-primary" value="Save Changes" type="submit" />
          &nbsp;
          <input name="reset" className="button" value="Restore Defaults" type="submit" onClick={this.handleReset} />
        </p>
      </form>
    );
  }
});

module.exports = OptionsForm;
