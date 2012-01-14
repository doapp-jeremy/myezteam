$(document).ready(function()
{
  $( "#linkTeamForm" ).dialog({
    autoOpen: false,
    height: 500,
    width: 600,
    modal: true,
    buttons: {
      Cancel: function() {
        $( this ).dialog( "close" );
      },
      "Link": function(){
        var teamId = $("#TeamId").val();
        var fbId = $("#TeamFacebookGroup").val();
        if (!fbId)
        {
          alert("Please select a Facebook group first");
          return;
        }
        var data = $("#TeamFacebookForm").serialize();
        var teamName = $("#TeamName").val();
        var groupName = $("#FacebookGroupName").val();

        var question = "Are you sure you want to link player " + teamName + " to Facebook group " + groupName + "?";
        if (confirm(question))
        {
          $.post("/Teams/save/json",data,
              function(data, textStatus, request){
                if ((textStatus == 'success') && (data['Team']['id'] == teamId))
                {
                  var title = teamName;
                  var fbPicHtml = getFBPicHtml(fbId, "thumb", "50", "50");
                  $("#teamFBpic" + teamId).html(fbPicHtml);
//                  // have Facebook parse the newly add pic
//                  FB.XFBML.parse(document.getElementById("userPic" + userId));
                  var message = "Successfully linked team " + teamName + " to Facebook group " + groupName;
                  alert(message);
                  $("#linkTeamForm").dialog("close");
                }
                else
                {
                  alert("Could not link team");
                }
              },
              'json'
            );
        }
      }
    },
    close: function() {
      $("#TeamFacebookGroup").val("");
      $("#TeamId").val("");
      $("#TeamName").val("");
      $("#FacebookGroupName").val("");
    }
  });
  
});

//function getFBPicHtml(fbId, size, height, width)
//{
//  var html = "<img src='https://graph.facebook.com/" + fbId + "/picture' />";
//  return html;
//}

function linkTeamToFacebook(id, teamName)
{
  $("#TeamName").val(teamName);
  $("#TeamId").val(id);
  $("#linkTeamFormText").text("Select one of your Facebook groups to link team " + teamName + " to.");
  $("#linkTeamForm").dialog("open");
}

//function getFriendListHtml(matches)
//{
//  var html = "<p>Click on a friend's <strong>name</strong> to select them</p>";
//  html+= "<table>";
//  html+= '<tr>';
//  for (var i in matches)
//  {
//    if (((i % 3) == 0) && (i > 0))
//    {
//      html+= '</tr><tr>';
//    }
//    var friend = matches[i];
//    var title = friend['label'];
//    html+= "<td>";
//    var picDiv = "<div style='float:left; margin-right: 10px'>";
//    picDiv += "<div style='cursor: pointer;' onclick='selectFriend(" + friend['id'] + ", \"" + friend['label'] + "\"); return false;'>" + friend['label'] + "</div>";
//    picDiv += getFBPicHtml(friend['id'], "square", "100", "100");
//    picDiv += "</div>";
//    html+= picDiv;
//    html+= '</td>';
//  }
//  html+= '</tr>';
//  html+= '</table>';
//  return html;
//}

function selectGroup(fbId, groupName)
{
  $("#TeamFacebookGroup").val(fbId);
  $("#FacebookGroupName").val(groupName);
  // TODO: figure out how to initiate button click
  $("#linkTeamForm :parent").children('button').each(function(){
//    alert('hi');
  });
}
//
//function getFbUsersLike(firstName, lastName)
//{
//  var matches = [];
//  for (var i in friends)
//  {
//    var friend = friends[i];
//    if ((friend['first_name'].toLowerCase() == firstName.toLowerCase()) || (friend['last_name'].toLowerCase() == lastName.toLowerCase()))
//    {
//      matches.push(friend);
//    }
//  }
//  return matches;
//}
//
//function getFbUser(matches, firstName, lastName)
//{
//  for (var i in matches)
//  {
//    var friend = matches[i];
//    if ((friend['first_name'].toLowerCase() == firstName.toLowerCase()) && (friend['last_name'].toLowerCase() == lastName.toLowerCase()))
//    {
//      return friend;
//    }
//  }
//  return null;
//}

