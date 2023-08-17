// A $( document ).ready() block.


window.wp = window.wp || {};
window.bp = window.bp || {};



jQuery( document ).ready(function($) {

    bp.Nouveau = bp.Nouveau || {};

    bp.Models.Activity.listenTo(Backbone, 'mediaprivacy',  function() {
        alert( "Test 15" );
    });

});