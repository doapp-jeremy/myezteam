$(document).ready(function()
{
  $( "#addPlayerForm" ).dialog({
    autoOpen: false,
    height: 800,
    width: 800,
    modal: true,
    buttons: {
      Cancel: function() {
        $( this ).dialog( "close" );
      },
      "Add Player": function(){
        submitAddPlayerForm(function() { $(this).dialog('close'); });
      }
    },
    close: function() {
    }
  });  
});

function submitAddPlayerForm(successCallback)
{
  if (!$("#PlayerUserId").val() && !$("#AddPlayerUserEmail").val())
  {
    alert("Please enter an email.");
    return;
  }
  
  $.post('/Players/save', $('#AddPlayerForm').serialize(),
    function(response, textStatus, request){
      if ((textStatus == 'success') && (response.status == 'success') && response.Player.id)
      {
        alert('Player Added Successfully!');
        successCallback();
        return;
      }
      else if (response.message)
      {
        alert("Could not save player: " + response.message);
        return;
      }
      else
      {
        alert("Error: could not save player");
        return;
      }
    },
    'json'
  );
  
}


