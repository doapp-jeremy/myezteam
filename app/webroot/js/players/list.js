$(document).ready(function()
{
  $("#friendSelectionLinkUser").autocomplete({
    source: fbFriends,
    autoFill: true,
    formatItem: function (row, i, max) {
      return i + "/" + max + ": \"" + row.name + "\" [" + row.id + "]";
    },
    select: function(event, ui){
      //$("#friendSelection").val(ui.item.label);
      $(this).val(ui.item.label);
      $("#UserFacebookId").val(ui.item.id);
      return false;
    }
  });
  
  $("#friendSelectionAddPlayer").autocomplete({
    source: friends,
    autoFill: true,
    formatItem: function (row, i, max) {
      return i + "/" + max + ": \"" + row.name + "\" [" + row.id + "]";
    },
    select: function(event, ui){
      $(this).val(ui.item.label);
      $("#PlayerUserId").val(ui.item.id);
      $("#AddPlayerUserEmail").val(ui.item.email);
      $("#AddPlayerUserFirstName").val(ui.item.first_name);
      $("#AddPlayerUserLastName").val(ui.item.last_name);
      return false;
    }
  });
  
  $( "#linkUserForm" ).dialog({
    autoOpen: false,
    height: 400,
    width: 600,
    modal: true,
    buttons: {
      "Link": function(){
        var userId = $("#UserId").val();
        var fbId = $("#UserFacebookId").val();
        if (!fbId)
        {
          alert("Please select a Facebook friend first");
          return;
        }
        var data = $("#UserFacebookForm").serialize();
        var firstName = $("#UserFirstName").val();
        var lastName = $("#UserLastName").val();

        var question = "Are you sure you want to link player " + firstName + " " + lastName + " to Facebook friend " + $("#friendSelectionLinkUser").val() + "?";
        if (confirm(question))
        {
          $.post("/Users/save/json",data,
              function(data, textStatus, request){
                if ((textStatus == 'success') && (data['User']['id'] == userId))
                {
//                  var title = data['User']['first_name'] + " " + data['User']['last_name'];
                  var title = firstName + " " + lastName;
                  var fbPicHtml = getFBPicHtml(fbId, "thumb", "50", "50");
                  $("#userPic" + userId).html(fbPicHtml);
                  // have Facebook parse the newly add pic
                  FB.XFBML.parse(document.getElementById("userPic" + userId));
                  var message = "Successfully linked " + title + " to Facebook";
                  alert(message);
                  $("#linkUserForm").dialog("close");
                }
                else
                {
                  alert("Could not link user");
                }
              },
              'json'
            );
        }
      },
      Cancel: function() {
        $( this ).dialog( "close" );
      }
    },
    close: function() {
      $("#friendSelectionLinkUser").val("");
      $("#UserFacebookId").val("");
      $("#UserId").val("");
    }
  });  
  
});

function getFBPicHtml(fbId, size, height, width)
{
  var html = "<fb:profile-pic class='fb_profile_pic_rendered' size='" + size + "' height='" + height + "'" + "width='" + width + "' ";
  html+= "facebook-logo='1' uid='" + fbId + "' style='width: " + width + "px; height: " + height + "px;'>";
  html+= "<a class='fb_link' href='http://www.facebook.com/profile.php?id=" + fbId + "'>";
  html+= "</fb:profile-pic>";
  return html;
}

function linkUserToFacebook(id, firstName, lastName, email)
{
  var matches = getFbUsersLike(firstName, lastName);
  if (matches.length >  0)
  {
    var fbFriend = getFbUser(matches, firstName, lastName);
    if (fbFriend)
    {
      $("#friendSelectionLinkUser").val(fbFriend['label']);
      $("#UserFacebookId").val(fbFriend['id']);
    }
    
    var friendListHtml = getFriendListHtml(matches);
    $("#friendPics").html(friendListHtml);
    // have Facebook parse the newly add pic
    FB.XFBML.parse(document.getElementById("friendPics"));
  }
  
  $("#UserFirstName").val(firstName);
  $("#UserLastName").val(lastName);
  $("#UserId").val(id);
  $("#linkUserFormText").text("Select one of your Facebook friends to link player " + firstName + " " + lastName + " to.");
  $("#linkUserForm").dialog("open");
}

function getFriendListHtml(matches)
{
  var html = "<p>Click on a friend's <strong>name</strong> to select them</p>";
  html+= "<table>";
  html+= '<tr>';
  for (var i in matches)
  {
    if (((i % 3) == 0) && (i > 0))
    {
      html+= '</tr><tr>';
    }
    var friend = matches[i];
    var title = friend['label'];
    html+= "<td>";
    var picDiv = "<div style='float:left; margin-right: 10px'>";
    picDiv += "<div style='cursor: pointer;' onclick='selectFriend(" + friend['id'] + ", \"" + friend['label'] + "\"); return false;'>" + friend['label'] + "</div>";
    picDiv += getFBPicHtml(friend['id'], "normal", "100", "75");
    picDiv += "</div>";
    html+= picDiv;
    html+= '</td>';
  }
  html+= '</tr>';
  html+= '</table>';
  return html;
}

function selectFriend(fbId, name)
{
  $("#friendSelectionLinkUser").val(name);
  $("#UserFacebookId").val(fbId);
}

function getFbUsersLike(firstName, lastName)
{
  var matches = [];
  if (fbFriends)
  {
    for (var i in fbFriends)
    {
      var friend = fbFriends[i];
      if ((friend['first_name'].toLowerCase() == firstName.toLowerCase()) || (friend['last_name'].toLowerCase() == lastName.toLowerCase()))
      {
        matches.push(friend);
      }
    }
  }
  return matches;
}

function getFbUser(matches, firstName, lastName)
{
  for (var i in matches)
  {
    var friend = matches[i];
    if ((friend['first_name'].toLowerCase() == firstName.toLowerCase()) && (friend['last_name'].toLowerCase() == lastName.toLowerCase()))
    {
      return friend;
    }
  }
  return null;
}

