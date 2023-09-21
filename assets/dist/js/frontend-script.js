/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
// Define your custom Backbone view for the checkbox// Extend bp.Views.FormSubmit
bp.Views.FormSubmit = bp.Views.FormSubmit.extend({
  initialize: function() {
      // Call the original initialize function
      bp.Views.FormSubmit.__super__.initialize.apply(this, arguments);

      // Create and append the checkbox
      this.checkbox = new bp.Views.ActivityInput({
          type: 'checkbox',
          id: 'anonymously-post',
          className: 'anonymously-post',
          name: 'anonymously-post',
          value: '1'
      });

      this.views.set( [ this.submit, this.reset, this.discard, this.checkbox ] );
  }
});
/******/ })()
;