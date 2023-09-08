// Define your custom Backbone view for the checkbox
var CustomCheckboxView = Backbone.View.extend({
  tagName: 'input',
  attributes: {
    type: 'checkbox',
    name: 'anonymously-post',
    value: '1'
  },

  initialize: function(options) {
    // Additional initialization logic if needed
  }
});

// Assuming you have a view for the activity post form
var ActivityPostFormView = Backbone.View.extend({
  initialize: function(options) {
    alert( "Tewst" );
  },
  render: function() {
    // Render the checkbox within the activity post form
    var customCheckboxView = new CustomCheckboxView();
    this.$el.append(customCheckboxView.el);
    return this;
  }
});

// Create an instance of the ActivityPostFormView and render it
var activityPostFormView = new ActivityPostFormView({
  el: '#whats-new-form' // Replace with the appropriate selector
});
activityPostFormView.render();
