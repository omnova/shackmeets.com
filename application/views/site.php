<!DOCTYPE html>
<html lang="en" xml:lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ShackMeets</title>

    <link rel="stylesheet" type="text/css" href="/css/shackmeets.css" />	

		<script type="text/javascript" src="/scripts/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="/scripts/jquery.dataTables.min.js"></script>	
		<script type="text/javascript" src="/scripts/jquery.form.js"></script>
    <script type="text/javascript" src="/scripts/jquery.simplemodal-1.3.5.min.js"></script>
		<script type="text/javascript" src="/scripts/shackmeets.js"></script>			

		<script type="text/javascript" id="js">

		$(document).ready(function() 
		{
      <?php

      $nags = array("Hey look it's fixed", "It's fixed. Happy now?", "Look at me, I'm all fixed!", "Mmm mm mm mm mmmm fixed.", "Fixed.  I hate you.", "It's fixed, jerk.", "Super duper fixed deluxe gaiden!");

      if (strtolower($current_user->username) == 'laurgasms') echo "alert(\"" . $nags[rand(0, 6)] . "\")";

      ?>

      initLoginModal();
			initLogout();
		});

		</script>
  </head>
  <body>  
    <div id="precache">
      <div id="button-precache"></div>
      <div id="dark-button-precache"></div>
      <div id="modal-header-precache"></div>
    </div>
  
    <div id="page-container">
      <div id="page">
        <div id="header-container" class="page-section">
          <div id="header">
            <h1><a href="<?php echo Url::site(''); ?>"><img src="/images/shackmeets_logo.png" /></a></h1>
						<ul id="header-buttons">
              <?php if ($current_user != null): ?>
							<li>Logged in as <?php echo $current_user->username; ?></li>
							<li><a id="preferences" href="<?php echo Url::site('preferences', null, false); ?>">Preferences</a></li>
							<li><a id="logout">Log out</a></li>
              <?php else: ?>
              <li><a id="login">Click to log in</a></li>
              <?php endif; ?>
						</ul>
          </div>
        </div>
					
        <?php echo $content; ?>				

        <div id="footer-container" class="page-section" style="clear:both;">
          <div id="footer">
            <p>&#169; 2011 <a style="font-weight: normal;" href="http://www.shacknews.com/messages?method=compose&to=omnova">Andrew Schenck</a>. Please <a style="font-weight: normal;" href="http://www.shacknews.com/messages?method=compose&to=virus">shackmessage virus</a> for all inquiries.</p>
          </div>
        </div>
      </div>
    </div>
    
    <div class="modal-container">
      <div id="login_modal" class="standard-modal">
        <div class="modal-header">
          <h1>Login</h1>
        </div>
        <div class="modal-body">		
          <form id="form_login_modal" class="page-form" name="form_login_modal" action="<?php echo Url::site('ajax_user/login', null, false); ?>" method="post">          
            <div id="login_modal_error_div" style="display: none;">
              <div class="error-section">
                <p>The following errors prevented submission:</p>
                <ul id="login_modal_error_list"></ul>
              </div>		
            </div>
            
            <table cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td class="label"><label id="login_modal_username_label" for="login_modal_username">Username</label></td>
                <td class="field"><input id="login_modal_username" class="textbox" style="width: 200px;" type="text" name="login_modal_username" maxlength="40" value="" /></td>
              </tr>
              <tr>
                <td class="label"><label id="login_modal_password_label" for="login_modal_password">Password</label></td>
                <td class="field"><input id="login_modal_password" class="textbox" type="password" style="width: 200px;" name="login_modal_password" maxlength="50" value="" /></td>
              </tr>
              <tr>
                <td colspan="2" class="label">
                  <p style="width: auto;">Use your shack credentials.  These will not be stored in any form or used for anything beyond the initial logging in.</p>
                </td>
              </tr>
            </table>

            <div class="button-section" style="height: 26px;">
              <button id="login_modal_submit" type="submit" class="dark" value="Submit" name="login_modal_submit">Login</button>
              <button id="login_modal_cancel" type="button" value="Cancel" name="login_modal_cancel">Cancel</button>
            </div>          
          </form>	
        </div>
      </div>
    </div>
  </body>
</html>