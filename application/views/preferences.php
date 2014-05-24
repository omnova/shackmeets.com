<script type="text/javascript" id="js">

$(document).ready(function() 
{
  initPreferences();
  
  $('#preferences_address').focus();
});

</script>

<div class="body-title-container page-section">
  <div class="body-title">
      <div class="body-title-left">
        <h2>User Preferences</h2>
      </div>
      <div class="body-title-right">
      
      </div>
    </div>
  </div>
</div>

<div class="body-container page-section">
  <div class="body">	
    <div class="body-content">
      <form id="form_preferences" class="page-form page-form-fixed" name="form_preferences" action="<?php echo Url::site('ajax_user/savepreferences', null, false); ?>" method="post">		
        <div id="preferences_error_div" style="display: none;">
          <div class="error-section">
            <p>The following errors prevented submission:</p>
            <ul id="preferences_error_list"></ul>
          </div>		
        </div>

        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label for="preferences_address">Address</label></td>
            <td class="field" style="width: 400px;">
              <input id="preferences_address" class="textbox" type="text" name="preferences_address" />
              <button id="preferences_address_convert" type="button" value="convert_address" name="preferences_address_convert">Convert to Geocode</button>
              <p>Enter an address and click the button to convert it into a geocode location.  This location will be used to determine which shackmeets you are alerted of based on your proximity to them.  Only the geocode will be stored and you may use any address that Google can figure out.</p>
            </td>
            <td class="info"></td>
          </tr>
          <tr>
            <td class="label"><label for="preferences_latitude">Latitude</label></td>
            <td class="field"><input id="preferences_latitude" class="textbox" style="width: 88px;" type="text" name="preferences_latitude" maxlength="20" value="<?php echo $current_user->latitude; ?>" /></td>
            <td class="info"></td>
          </tr>
          <tr>
            <td class="label"><label for="preferences_longitude">Longitude</label></td>
            <td class="field"><input id="preferences_longitude" class="textbox" style="width: 88px;" type="text" name="preferences_longitude" maxlength="20" value="<?php echo $current_user->longitude; ?>" /></td>
            <td class="info"></td>
          </tr>	
        </table>
        
        <div class="divider"></div>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td class="label"><label>Notify Me</label></td>
            <td class="field">
              <input id="preferences_notify_none" class="checkbox" type="radio" name="preferences_notify_option" value="0" <?php if ($current_user->notify_option == 0) echo 'checked="1"'; ?>/>
              <label for="preferences_notify_none">Never</label>
            </td>
            <td class="info"></td>
          </tr>
          <tr>
            <td class="label"></td>
            <td class="field">
              <input id="preferences_notify_distance" class="checkbox" style="margin-top: 5px;" type="radio" name="preferences_notify_option" value="1" <?php if ($current_user->notify_option == 1) echo 'checked="1"'; ?>/>
              <label for="preferences_notify_distance">Only for shackmeets within </label>
              <input id="preferences_notify_max_distance" class="textbox" style="width: 42px;" type="text" name="preferences_notify_max_distance" maxlength="5" value="<?php echo $current_user->notify_max_distance; ?>" />
              <label for="preferences_notify_distance">miles of the above geolocation</label>
            <td class="info"></td>
          </tr>
          <tr>
            <td class="label"></td>
            <td class="field">
              <input id="preferences_notify_all" class="checkbox" type="radio" name="preferences_notify_option" value="2" <?php if ($current_user->notify_option == 2) echo 'checked="1"'; ?>/>
              <label for="preferences_notify_all">For all shackmeets</label>
            <td class="info"></td>
          </tr>
        </table>
        
        <div class="divider"></div>
        
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>			
            <td class="label"><label>Notify By</label></td>
            <td class="checkbox-label"><input id="preferences_notify_shackmessage" class="checkbox" type="checkbox" name="preferences_notify_shackmessage" maxlength="20" value="1" <?php if ($current_user->notify_shackmessage == 1) echo 'checked="1"'; ?>/>
            <label for="preferences_notify_shackmessage">Shackmessage</label></td>
            <td class="info"></td>
          </tr>
          <tr>			
            <td class="label"></td>
            <td class="checkbox-label">
              <input id="preferences_notify_email" class="checkbox" type="checkbox" name="preferences_notify_email" maxlength="20" value="1" <?php if ($current_user->notify_email == 1) echo 'checked="1"'; ?>/>
              <label for="preferences_notify_email">Email:</label>
              <input id="preferences_email_address" class="textbox" type="text" name="preferences_email_address" maxlength="45" value="<?php echo $current_user->email_address; ?>" />
            </td>
            <td class="info"></td>
          </tr>					
        </table>

        <div class="button-section">
          <button id="preferences_submit" type="submit" class="dark" value="Save" name="preferences_submit">Save</button>
          <button id="preferences_cancel" type="button" value="Cancel" name="preferences_cancel">Cancel</button>
        </div>  
      </form>	
    </div>
  </div>
</div>