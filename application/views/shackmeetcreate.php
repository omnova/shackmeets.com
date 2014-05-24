<link rel="stylesheet" type="text/css" href="/css/datePicker.css" />
<script type="text/javascript" src="/scripts/date.js"></script>	
<script type="text/javascript" src="/scripts/jquery.datePicker.js"></script>	
<script type="text/javascript" id="js">

$(document).ready(function() 
{
  initCreateMeet();
  
  $('#meet_title').focus();
  
  $('.date-pick').datePicker({clickInput:true, createButton:false})
});

</script>

<div class="body-title-container page-section">
  <div class="body-title">
      <div class="body-title-left">
        <h2>Create a New Shackmeet</h2>
      </div>
      <div class="body-title-right">
      
      </div>
    </div>
  </div>
</div>

<div class="body-container page-section">
  <div class="body">	
    <div class="body-content">
      <form id="form_meet" class="page-form page-form-fixed" name="form_meet" action="<?php echo Url::site('ajax_shackmeet/create', null, false); ?>" method="post">		
        <div id="meet_error_div" style="display: none;">
          <div class="error-section">
            <p>The following errors prevented submission:</p>
            <ul id="meet_error_list"></ul>
          </div>
        </div>

        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label for="meet_title">Title</label></td>
            <td class="field"><input id="meet_title" class="textbox" type="text" name="meet_title" maxlength="50" /></td>
          </tr>
          <tr>
            <td class="label"><label for="meet_description">Description</label></td>
            <td class="multiline"><textarea id="meet_description" name="meet_description" style="width: 500px; height: 250px;"></textarea></td>
          </tr>
          <tr>
            <td class="label"><label for="meet_location_start_date">Event Date</label></td>
            <td class="field">
              <input id="meet_location_start_date" class="textbox date-pick" style="width: 70px;" type="text" name="meet_location_start_date" maxlength="10"/>
              <label>MM/DD/YYYY</label>
            </td>
          </tr>		
        </table>
        
        <div class="divider"></div>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label for="meet_location_name">Venue Name</label></td>
            <td class="field">
              <input id="meet_location_name" class="textbox" type="text" name="meet_location_name" maxlength="50" />             
            </td>
          </tr>		
          <tr>
            <td class="label"><label for="meet_location_address">Venue Address</label></td>
            <td class="field">
              <!--<input id="meet_location_address" class="textbox" type="text" name="meet_location_address" />-->
              <textarea id="meet_location_address" name="meet_location_address" style="height: 46px;"></textarea>
              <p>This address will be used to determine which shackers will be notified of this shackmeet's creation based on their proximity to it.  Use a general location (city, state, country) if a specific location has not been set or you don't want to publicize your real address.</p>
            </td>
          </tr>
        </table>
        
        <div class="divider"></div>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>			
            <td class="label"><label>Options</label></td>
            <td class="checkbox-label">
              <input id="meet_rsvp_creator" class="checkbox" type="checkbox" name="meet_rsvp_creator" value="1" checked="checked" />
              <label for="meet_rsvp_creator">Mark Self As Attending</label>
            </td>
          </tr>
          <tr>
            <td></td>
            <td class="checkbox-label">
              <input id="meet_post_announcement" class="checkbox" type="checkbox" name="meet_post_announcement" value="1" checked="checked" />
              <label for="meet_post_announcement">Post Announcement To Chatty</label>
              <p>This will post a basic shackmeet announcement into the chatty so you don't have to.</p>
            </td>
          </tr>
          <?php if ($current_user != null && $current_user->username == 'omnova'): ?>
          <tr>
            <td></td>
            <td class="checkbox-label">
              <input id="meet_send_notifications" class="checkbox" type="checkbox" name="meet_send_notifications" value="1" checked="checked" />
              <label for="meet_send_notifications">Send Notifications</label>
              <p>This will send out notifications to shackers who are eligible to receive them for this shackmeet.</p>
            </td>
          </tr>
          <?php endif; ?>

        </table>

        <div class="button-section">
          <button id="meet_submit" type="submit" class="dark" value="Save" name="meet_submit">Save</button>
          <button id="meet_cancel" type="button" value="Cancel" name="meet_cancel">Cancel</button>
        </div>  
      </form>	
    </div>
  </div>
</div>