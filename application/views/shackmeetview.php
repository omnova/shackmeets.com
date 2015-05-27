<script type="text/javascript"
    src="http://maps.googleapis.com/maps/api/js?sensor=false">
</script>

<script type="text/javascript" id="js">

$(document).ready(function() 
{
  initAttendanceModal();
  initCancelMeetModal();
  
  var descriptionText = $('#shackmeet-description').html();
  $('#shackmeet-description').html(Linkify(descriptionText));
  
  <?php if (strlen($locations[0]['latitude']) > 0): ?>
  
  var latlng = new google.maps.LatLng(<?php echo $locations[0]['latitude'] . ',' . $locations[0]['longitude']; ?>);
  var myOptions = {
    zoom: 15,
    center: latlng,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  
  var map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
      
  var marker = new google.maps.Marker({
    position: latlng, 
    map: map, 
    title: '<?php echo str_replace("'", "\'", $locations[0]['name']); ?>',
    clickable:true
  });   
  
  var contentString = '<div id="content" class="map-container-bubble"><strong><?php echo str_replace("'", "\'", htmlentities($locations[0]['name'])); ?></strong><br/><?php echo str_replace("'", "\'", Cleaner::strip_and_replace($locations[0]['address'])); ?><br/><br/><a href="<?php echo geocode::build_maps_url($locations[0]['address']); ?>" target="_blank">Click to view in Google Maps</a></div>';

  var infowindow = new google.maps.InfoWindow({
    content: contentString
  });

  google.maps.event.addListener(marker, 'click', function() {
    infowindow.open(map, marker);
  });

  // Post Reminder

  var meet_id = <?php echo $shackmeet->meet_id; ?>;
  var location_id = <?php echo $locations[0]['location_id']; ?>

  $('#post_reminder').click(function (e) {
    $.post( siteUrl('ajax_shackmeet/post_reminder'), { meet_id: meet_id, location_id: location_id  }, postReminderComplete, "json");
  });
        
  <?php endif; ?>
});

function postReminderComplete(data, textStatus, jqXHR)
{
  if (data.success)
  {
    alert("A reminder will be posted shortly.");
  }
  else
  {
    alert(data.errors[0].message);
  }
}

// http://stackoverflow.com/questions/37684/how-to-replace-plain-urls-with-links
function Linkify(inputText) {
  //URLs starting with http://, https://, or ftp://
  var replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
  var replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

  //URLs starting with www. (without // before it, or it'd re-link the ones done above)
  var replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
  var replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

  //Change email addresses to mailto:: links
  var replacePattern3 = /(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
  var replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

  return replacedText
}

</script>

<div class="body-title-container page-section">
  <div class="body-title">
    <div class="body-title-left">
      <h2><?php echo htmlentities($shackmeet->title); ?></h2>
      <h3>suggested by <a href="http://chattyprofil.es/p/<?php echo urlencode($shackmeet->organizer); ?>" target="_blank"><?php echo htmlentities($shackmeet->organizer); ?></a><?php
      
        if (strlen($locations[0]['start_date']) > 0) 
          echo ' for ' . date('F j, Y', strtotime($locations[0]['start_date'])); 
        else
          echo ', no date set';
    ?></h3>
    </div>
    <div class="body-title-right">      
      <?php if ($shackmeet->status_id != 0): ?>
        <h3>This shackmeet has been canceled.</h3>     
      <?php elseif (date("Y-m-d") > date("Y-m-d", strtotime($locations[0]['start_date']))): ?>   
        <h3>This shackmeet has already happened.</h3>       
      <?php elseif ($current_user == null):?>
        <h3>You must log in to RSVP!</h3>
      <?php else: ?>
        <label>RSVP</label>
          
        <?php if ($current_attendee == null || $current_attendee->attendance_option_id == 0): ?>
          <a id="attendance" class="button"> Not Attending <img style="padding-bottom: 1px; margin-left: 3px;" src="<?php echo Url::site('images/button_arrow.png'); ?>"/></a>
        <?php elseif ($current_attendee->attendance_option_id == 1): ?>
          <a id="attendance" class="button"> Possibly Attending<?php if ($current_attendee->extra_attendees > 0) echo ' +' . $current_attendee->extra_attendees; ?> <img style="padding-bottom: 1px; margin-left: 3px;" src="<?php echo Url::site('images/button_arrow.png'); ?>"/></a>
        <?php elseif ($current_attendee->attendance_option_id == 2): ?>      
          <a id="attendance" class="button"> Definitely Attending<?php if ($current_attendee->extra_attendees > 0) echo ' +' . $current_attendee->extra_attendees; ?> <img style="padding-bottom: 1px; margin-left: 3px;" src="<?php echo Url::site('images/button_arrow.png'); ?>"/></a>
        <?php endif; ?>
        
        <?php if ($current_user != null && $current_user->username == $shackmeet->organizer): ?>
        <a id="post_reminder" class="button-dark" title="This will post a reminder to the chatty.  You can do this once every 18 hours.">Post Reminder</a>
        <a id="edit_shackmeet" href="<?php echo Url::site('shackmeet/edit/' . $shackmeet->meet_id); ?>" class="button-dark">Edit</a>
        <a id="cancel_shackmeet" class="button-dark">Cancel</a>
        <?php endif; ?>
      <?php endif; ?>    
    </div>
  </div>
</div>	

<div class="body-container page-section">
  <div class="body">	
    <div class="body-content">
      <div class="body-content-cols col2">
        <div class="body-content-col first" style="text-align: left; ">    
          <p id="shackmeet-description"><?php echo Cleaner::strip_with_shacktags($shackmeet->description); ?></p>
 
          <h5 style="margin-top: 20px;">Venue<?php if (count($locations) > 1) echo 's'; ?></h5>
 
          <div id="map-container" style="">
            <div id="map-canvas">
              <div id="map-canvas-empty">No address to plot!</div>
            </div>
                        
            <div id="map-location-container">
              <?php foreach ($locations as $location): ?>
              <div class="<?php echo (count($locations) < 3) ? 'map-location1' : 'map-location2'; ?>">
                <div class="map-location-number" style="display: none;"><?php echo $location['order_id']; ?></div>
                <div class="map-location-name"><a href="<?php echo geocode::build_maps_url($location['address']); ?>" target="_blank"><?php echo $location['name']; ?></a></div>
                <?php if (strlen($location['address']) > 0): ?>
                <div class="map-location-address"><?php echo $location['address']; ?></div>
                <?php else: ?>
                <div class="map-location-none">No address provided</div>
                <?php endif; ?>
                <?php if (strlen($location['start_date']) > 0): ?>
                <div class="map-location-date">Starts <?php echo date('m/d/Y', strtotime($location['start_date'])) ?></div>
                <?php else: ?>
                <div class="map-location-none">No time set</div>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="body-content-col last">
          <div class="body-content-box">
            <h5>Definitely Attending<span class="right"><?php echo $definite_count; ?></span></h5>
            <ul class="list-attendees">
              <?php if (count($attendees_definite) > 0): ?>      
                <?php foreach ($attendees_definite as $attendee): ?>
                <li><?php                
                
                if ($current_user != null && $current_user->username == $attendee['username'])
                  echo '<strong>';
                  
                ?><a href="http://chattyprofil.es/p/<?php echo urlencode($attendee['username']); ?>" target="_blank"><?php echo htmlentities($attendee['username']); ?></a><?php 

                if (strtolower($attendee['username']) == 'hirez')
                  echo ' (idiot)';

                if ($attendee['extra_attendees'] > 0)
                {
                  echo ' +' . $attendee['extra_attendees'];

                  if (strtolower($attendee['username']) == 'hirez')
                    echo ' stupid hobos';
                }
                
                if ($current_user != null && $current_user->username == $attendee['username'])
                  echo '</strong>';
                  
                ?></li>          
                <?php endforeach; ?>
              <?php else: ?>  
                <li class="noattendees">None</li>
              <?php endif; ?>
            </ul>
            
            <h5>Possibly Attending<span class="right"><?php echo $maybe_count; ?></span></h5>
            <ul class="list-attendees">
              <?php if (count($attendees_maybe) > 0): ?>      
                <?php foreach ($attendees_maybe as $attendee): ?>
                <li><?php                
                
                if ($current_user != null && $current_user->username == $attendee['username'])
                  echo '<strong>';
                  
                ?><a href="http://chattyprofil.es/p/<?php echo urlencode($attendee['username']); ?>" target="_blank"><?php echo htmlentities($attendee['username']); ?></a><?php

                if (strtolower($attendee['username']) == 'hirez')
                  echo ' (idiot)';

                if ($attendee['extra_attendees'] > 0)
                {
                  echo ' +' . $attendee['extra_attendees'];

                  if (strtolower($attendee['username']) == 'hirez')
                    echo ' stupid hobos';
                }

                if ($current_user != null && $current_user->username == $attendee['username'])
                  echo '</strong>';
                  
                ?></li>              
                <?php endforeach; ?>
              <?php else: ?>  
                <li class="noattendees">None</li>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal-container">
  <div id="attendance_modal" class="standard-modal">
    <div class="modal-header">
      <h1>Change RSVP</h1>
    </div>
    <div class="modal-body">		
      <form id="form_attendance_modal" class="page-form" name="form_attendance_modal" action="<?php echo Url::site('ajax_shackmeet/set_attendance', null, false); ?>" method="post">          
        <div id="attendance_modal_error_div" style="display: none;">
          <div class="error-section">
            <p>The following errors prevented submission:</p>
            <ul id="attendance_modal_error_list"></ul>
          </div>		
        </div>
        
        <input id="attendance_meet_id" name="attendance_meet_id" type="hidden" value="<?php echo $shackmeet->meet_id; ?>"/>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label for="attendance_option">RSVP</label></td>
            <td class="field">
              <select id="attendance_option" name="attendance_option" style="width: 140px;">
                <?php echo $attendance_options; ?>
              </select>
            </td>
          </tr>
          <tr>
            <td class="label"><label for="attendance_extra_attendees">Additional Guests</label></td>
            <td class="field"><input id="attendance_extra_attendees" class="textbox" style="width: 25px;" type="text" name="attendance_extra_attendees" maxlength="2" value="<?php echo ($current_attendee->extra_attendees != null) ? $current_attendee->extra_attendees : 0; ?>" /></td>
          </tr>
        </table>

        <div class="button-section" style="height: 26px;">
          <button id="attendance_modal_submit" type="submit" class="dark" value="Save" name="attendance_modal_submit">Save</button>
          <button id="attendance_modal_cancel" type="button" value="Cancel" name="attendance_modal_cancel">Cancel</button>
        </div>          
      </form>	
    </div>
  </div>
  
  <?php if ($current_user != null && $current_user->username == $shackmeet->organizer): ?>       
  <div id="cancel_modal" class="standard-modal">
    <div class="modal-header">
      <h1>Cancel Shackmeet</h1>
    </div>
    <div class="modal-body">		
      <form id="form_cancel_modal" class="page-form" name="form_cancel_modal" action="<?php echo Url::site('ajax_shackmeet/cancel', null, false); ?>" method="post">        
        <input id="cancel_meet_id" name="cancel_meet_id" type="hidden" value="<?php echo $shackmeet->meet_id; ?>"/>
        <input id="cancel_location_id" name="cancel_location_id" type="hidden" value="<?php echo $locations[0]['location_id']; ?>"/>
        
        <p>Are you sure you want to cancel this shackmeet?</p>

        <div class="button-section" style="height: 26px;">
          <button id="cancel_modal_submit" type="submit" class="dark" value="Save" name="cancel_modal_submit">Yes</button>
          <button id="cancel_modal_cancel" type="button" value="Cancel" name="cancel_modal_cancel">No</button>
        </div>          
      </form>	
    </div>
  </div> 
  <?php endif; ?>
</div>