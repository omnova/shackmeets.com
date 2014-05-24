function redirect(url)
{
	window.location.replace(url);
}

function siteUrl(page)
{
	var baseUrl = "http://www.shackmeets.com/";
	
	return baseUrl + page;
}

// Login

function initLoginModal()
{
	$('#login').click(function (e) {
		$('#login_modal').modal({
			overlayClose: true
		});
    
    $('#login_username').focus();
	});
	
	$('#login_modal_cancel').click(function (e) {
		$.modal.close();
	});

  $('#form_login_modal').submit(function() {       
    $('#login_modal_submit').attr('disabled', 'disabled')

    var options = { 
      dataType: 'json', 		
      success:  loginComplete,
      error: loginError
    }; 
    
    $(this).ajaxSubmit(options); 
    
    return false; 
  });
}

function loginComplete(data) 
{ 
	if (data.success) 
	{
		window.location.reload();
	}
	else
	{
		clearLoginErrors();
		attachLoginErrors(data.errors);
    
    $('#login_modal_submit').removeAttr('disabled');
	}
}

function loginError(data)
{
    alert('ERROR');
}

function attachLoginErrors(errors)
{
	for (var i = 0; i < errors.length; i++)
	{
		if (errors[i].control_id === null)
		{
			$('#login_modal_error_div').css('display', 'block');
			$('#login_modal_error_list').append('<li>' + errors[i].message + '</li>');
		}
		else
		{
			$('#login_modal_error_div').css('display', 'block');
			$('#login_modal_error_list').append('<li>' + errors[i].message + '</li>');
		}
	}
}

function clearLoginErrors()
{
	$('#login_modal_error_div').css('display', 'none');
	$('#login_modal_error_list').html('');
}

// Logout

function initLogout()
{
	$('#logout').click(function (e) {
		$.ajax({
			type: "POST",
			url: siteUrl("ajax_user/logout"),
			dataType: "json",
			success: logoutComplete
		});
	});		
}
		
function logoutComplete(data)
{
	//window.location.replace(siteUrl(''));
  window.location.reload();
}

// Preferences

function initPreferences()
{
  $('#form_preferences').submit(function() {       
    $('#preferences_submit').attr('disabled', 'disabled')

    var options = { 
      dataType: 'json', 		
      success:  savePreferencesComplete 
    }; 
    
    $(this).ajaxSubmit(options); 
    
    return false; 
  });
    	
	$('#preferences_cancel').click(function (e) {
		window.location.replace(siteUrl(''));
	});
  
  // Convert Address to Geocode button
  $('#preferences_address_convert').click(function() {
    var address = $('#preferences_address').val();
    
    var temp = convertAddressToGeocode(address);
  });
  
  // Click button
  $('#preferences_notify_all').click(enableDisableMaxDistance);
  $('#preferences_notify_distance').click(enableDisableMaxDistance);
  $('#preferences_notify_none').click(enableDisableMaxDistance);
  
  enableDisableMaxDistance();
  
  $('#preferences_notify_email').click(enableDisableEmailAddress);
  
  enableDisableEmailAddress();
}

function savePreferencesComplete(data) 
{ 
	if (data.success) 
	{
		window.location.replace(siteUrl(''));
	}
	else
	{
		clearSavePreferencesErrors();
		attachSavePreferencesErrors(data.errors);
    
    $('#preferences_submit').removeAttr('disabled');
	}
}

function attachSavePreferencesErrors(errors)
{
	for (var i = 0; i < errors.length; i++)
	{
		if (errors[i].control_id === null)
		{
			$('#preferences_error_div').css('display', 'block');
			$('#preferences_error_list').append('<li>' + errors[i].message + '</li>');
		}
		else
		{
			$('#preferences_error_div').css('display', 'block');
			$('#preferences_error_list').append('<li>' + errors[i].message + '</li>');
		}
	}
}

function clearSavePreferencesErrors()
{
	$('#preferences_error_div').css('display', 'none');
	$('#preferences_error_list').html('');
}

function enableDisableMaxDistance()
{
  if ($('#preferences_notify_distance').is(':checked'))
  {
    $('#preferences_notify_max_distance').removeAttr('disabled');
  }
  else
  {
    $('#preferences_notify_max_distance').attr('disabled', 'disabled')
  }
}

function enableDisableEmailAddress()
{
  if ($('#preferences_notify_email').is(':checked'))
  {
    $('#preferences_email_address').removeAttr('disabled');
  }
  else
  {
    $('#preferences_email_address').attr('disabled', 'disabled')
  }
}

// Create Meet

function initCreateMeet()
{
  $('#form_meet').submit(function() {       
    $('#meet_submit').attr('disabled', 'disabled')

    var options = { 
      dataType: 'json', 		
      success:  saveMeetComplete 
    }; 
    
    $(this).ajaxSubmit(options); 
    
    return false; 
  });
  
  $('#meet_cancel').click(function (e) {
		window.location.replace(siteUrl(''));
	});
}

function initEditMeet()
{
  $('#form_meet').submit(function() {       
    $('#meet_submit').attr('disabled', 'disabled')

    var options = { 
      dataType: 'json', 		
      success:  saveMeetComplete 
    }; 
    
    $(this).ajaxSubmit(options); 
    
    return false; 
  });
  
  $('#meet_cancel').click(function (e) {
		window.location.replace(siteUrl(''));
	});
}

function saveMeetComplete(data) 
{ 
	if (data.success) 
	{
		window.location.replace(siteUrl('shackmeet/view/' + data.data));
	}
	else
	{
		clearMeetErrors();
		attachMeetErrors(data.errors);
    
    $('#meet_submit').removeAttr('disabled');
	}
}

function attachMeetErrors(errors)
{
	for (var i = 0; i < errors.length; i++)
	{
		if (errors[i].control_id === null)
		{
			$('#meet_error_div').css('display', 'block');
			$('#meet_error_list').append('<li>' + errors[i].message + '</li>');
		}
		else
		{
			$('#meet_error_div').css('display', 'block');
			$('#meet_error_list').append('<li>' + errors[i].message + '</li>');
		}
	}
}

function clearMeetErrors()
{
	$('#meet_error_div').css('display', 'none');
	$('#meet_error_list').html('');
}

// Cancel Meet Functions

function initCancelMeetModal()
{
	$('#cancel_shackmeet').click(function (e) {
		$('#cancel_modal').modal({
			overlayClose: true
		});

    $('#cancel_modal_cancel').focus();
	});
	
	$('#cancel_modal_cancel').click(function (e) {
		$.modal.close();
	});

  $('#form_cancel_modal').submit(function() {       
    $('#cancel_modal_submit').attr('disabled', 'disabled')

    var options = { 
      dataType: 'json', 		
      success:  cancelMeetComplete 
    }; 
    
    $(this).ajaxSubmit(options); 
    
    return false; 
  });
}

function cancelMeetComplete(data) 
{ 
	if (data.success) 
	{
		window.location.reload();
	}
}

// Attendance Functions

function initAttendanceModal()
{
	$('#attendance').click(function (e) {
		$('#attendance_modal').modal({
			overlayClose: true
		});
      
    enableDisableAdditionalGuests();
    
    $('#attendance_option').focus();
	});
	
	$('#attendance_modal_cancel').click(function (e) {
		$.modal.close();
	});

  $('#form_attendance_modal').submit(function() {       
    $('#attendance_modal_submit').attr('disabled', 'disabled')

    var options = { 
      dataType: 'json', 		
      success:  attendanceComplete 
    }; 
    
    $(this).ajaxSubmit(options); 
    
    return false; 
  });
  
  $('#attendance_option').change(enableDisableAdditionalGuests);
}

function attendanceComplete(data)
{ 
	if (data.success) 
	{
		window.location.replace(siteUrl('shackmeet/view/' + data.data));
	}
	else
	{
		clearAttendanceErrors();
		attachAttendanceErrors(data.errors);
    
    $('#attendance_modal_submit').removeAttr('disabled');
	}
}

function attachAttendanceErrors(errors)
{
	for (var i = 0; i < errors.length; i++)
	{
		if (errors[i].control_id === null)
		{
			$('#attendance_modal_error_div').css('display', 'block');
			$('#attendance_modal_error_list').append('<li>' + errors[i].message + '</li>');
		}
		else
		{
			$('#attendance_modal_error_div').css('display', 'block');
			$('#attendance_modal_error_list').append('<li>' + errors[i].message + '</li>');
		}
	}
}

function clearAttendanceErrors()
{
	$('#attendance_modal_error_div').css('display', 'none');
	$('#attendance_modal_error_list').html('');
}

function enableDisableAdditionalGuests()
{
  if ($('#attendance_option').val() != 0)
  {
    $('#attendance_extra_attendees').removeAttr('disabled');
  }
  else
  {
    $('#attendance_extra_attendees').attr('disabled', 'disabled')
  }
}


// Geocode Functions

function convertAddressToGeocode(addressToConvert)
{
  $.get(siteUrl('ajax_geocode/convert_address_to_geocode'), 
        { address: addressToConvert },
    function(data){
      if (data.data != null)
      {    
        var response = $.parseJSON(data.data);
        var address = '';         
        
        if (response.state.length > 0)
        {
          address = response.state;
          
          if (response.country.length > 0)
            address += ', ' + response.country;
        }
        else
          address = response.country
        
        $('#preferences_address').val(response.formatted_address);
        $('#preferences_latitude').val(response.latitude);
        $('#preferences_longitude').val(response.longitude);
      }
    }, 'json');
}