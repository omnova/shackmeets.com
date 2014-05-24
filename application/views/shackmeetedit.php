<link rel="stylesheet" type="text/css" href="/css/datePicker.css" />
<script type="text/javascript" src="/scripts/date.js"></script>	
<script type="text/javascript" src="/scripts/jquery.datePicker.js"></script>	
<script type="text/javascript" id="js">

$(document).ready(function() 
{
  initEditMeet();
  
  $('#meet_title').focus();
  
  $('.date-pick').datePicker({clickInput:true, createButton:false})
});

</script>

<div class="body-title-container page-section">
  <div class="body-title">
      <div class="body-title-left">
        <h2>Modify Shackmeet</h2>
      </div>
      <div class="body-title-right">
      
      </div>
    </div>
  </div>
</div>

<div class="body-container page-section">
  <div class="body">	
    <div class="body-content">
      <form id="form_meet" class="page-form page-form-fixed" name="form_meet" action="<?php echo Url::site('ajax_shackmeet/edit', null, false); ?>" method="post">		
        <div id="meet_error_div" style="display: none;">
          <div class="error-section">
            <p>The following errors prevented submission:</p>
            <ul id="meet_error_list"></ul>
          </div>
        </div>
        
        <input type="hidden" id="meet_id" name="meet_id" value="<?php echo $meet->meet_id; ?>"/>
        <input type="hidden" id="location_id" name="location_id" value="<?php echo $locations[0]['location_id']; ?>"/>

        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label for="meet_title">Title</label></td>
            <td class="field"><input id="meet_title" class="textbox" type="text" name="meet_title" maxlength="50" value="<?php echo $meet->title; ?>"/></td>
          </tr>
          <tr>
            <td class="label"><label for="meet_description">Description</label></td>
            <td class="multiline"><textarea id="meet_description" name="meet_description" style="width: 500px; height: 250px;"><?php echo $meet->description; ?></textarea></td>
          </tr>
          <tr>
            <td class="label"><label for="meet_location_start_date">Event Date</label></td>
            <td class="field">
              <input id="meet_location_start_date" class="textbox date-pick" style="width: 70px;" type="text" name="meet_location_start_date" maxlength="10" value="<?php echo date('m/d/Y', strtotime($meet->start_date)) ?>"/>
              <label>MM/DD/YYYY</label>
            </td>
          </tr>		
        </table>
        
        <div class="divider"></div>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label for="meet_location_name">Venue Name</label></td>
            <td class="field">
              <input id="meet_location_name" class="textbox" type="text" name="meet_location_name" maxlength="50" value="<?php echo $locations[0]['name']; ?>"/>
            </td>
          </tr>		
          <tr>
            <td class="label"><label for="meet_location_address">Venue Address</label></td>
            <td class="field">
              <!--<input id="meet_location_address" class="textbox" type="text" name="meet_location_address" />-->
              <textarea id="meet_location_address" name="meet_location_address" style="height: 46px;"><?php echo $locations[0]['address']; ?></textarea>
              <p>This address will be used to determine which shackers will be notified of this shackmeet's creation based on their proximity to it.  Use a general location (city, state, country) if a specific location has not been set or you don't want to publicize your real address.</p>
            </td>
          </tr>
        </table>
        <!--
        <div class="divider"></div>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>			
            <td class="label"><label>Options</label></td>
            <td class="checkbox-label">
              
            </td>
          </tr>

        </table>
        -->
        <div class="button-section">
          <button id="meet_submit" type="submit" class="dark" value="Save" name="meet_submit">Save</button>
          <button id="meet_cancel" type="button" value="Cancel" name="meet_cancel">Cancel</button>
        </div>  
      </form>	
    </div>
  </div>
</div>