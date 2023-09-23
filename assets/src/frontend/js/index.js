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

      var checkboxWrapper = document.createElement('div');
      checkboxWrapper.classList.add('anonymously-post-wrap'); 
      
      checkboxWrapper.appendChild(this.checkbox.el);
      
      var checkboxknobs = document.createElement('div');
      checkboxknobs.classList.add('anonymously-post-knobs'); 
      checkboxWrapper.appendChild(checkboxknobs);

      var checkboxlayer = document.createElement('div');
      checkboxlayer.classList.add('anonymously-post-layer'); 
      checkboxWrapper.appendChild(checkboxlayer);


      this.checkbox.el = checkboxWrapper;

      this.views.set( [ this.submit, this.reset, this.discard, this.checkbox ] );
  }
});