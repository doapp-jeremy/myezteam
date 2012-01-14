$(document).ready(function()
{
  $("#" + rsvpFormDivId).dialog({
    autoOpen: false,
    height: 500,
    width: 550,
    modal: true,
    buttons: {
      Cancel: function() {
        $( this ).dialog( "close" );
      },
      "RSVP": function(){
        $.post("/Responses/add", $("#" + rsvpFormId).serialize(), function(response, textStatus, request){
          if ((textStatus == 'success') && (response.Response.id))
          {
            window.location.reload();
          }
          else
          {
            alert("Could not save response: " + response);
          }
        }, 'json');
      }
    },
    close: function() {
    }
  });  
});
