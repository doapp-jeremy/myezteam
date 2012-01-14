  $.post = function(url, data, callback, dataType){
    
    $("#loadingDialog").dialog("open");
    $.ajax({
      type: 'POST',
      url: url,
      data: data,
      dataType: dataType,
      success: function(response, textStatus, request)
      {
        $("#loadingDialog").dialog("close");
        callback(response, textStatus, request);
      },
      error: function (request, textStatus, errorThrown)
      {
        $("#loadingDialog").dialog("close");
        callback({status:'error', 'message':errorThrown}, textStatus, request);
      }
    });
  };

  
  $(document).ready(function(){
    $("#loadingDialog").dialog({
      autoOpen: false,
      height: 100,
      width: 200,
      modal: true,
      buttons: {
      },
      close: function() {
        // clear table
      }
    });
  });