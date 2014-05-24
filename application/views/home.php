<div class="body-title-container page-section">
  <div class="body-title">
      <div class="body-title-left">
        <h2>Upcoming Shackmeets</h2>
      </div>
      <div class="body-title-right">
        <?php if ($current_user != null): ?>    
          <?php if ($current_user->is_banned == 1): ?>          
          <h3>Congrats, you are why we can't have nice things.</h3>
          <?php else: ?>
          <a class="button" href="<?php echo Url::site('shackmeet/create'); ?>">Create a New Shackmeet</a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<div class="body-container page-section">
  <div class="body">	
    <div class="body-content">    
      <?php if (count($upcoming_shackmeets) > 0): ?>
      <div class="content-table-container">
        <table id="upcoming-shackmeets-table" class="meet-listing">
          <thead>
            <th class="nosort">Event Date</th>
            <th class="nosort">Title</th>
            <th class="nosort">Organizer</th>
            <th class="nosort">Location</th>
            <th class="nosort" style="width: 110px; padding-left: 0;" colspan="2">Attendees</th>
          </thead>
          <tbody>
            <?php foreach ($upcoming_shackmeets as $shackmeet): ?>
            <tr onclick="document.location.href = '<?php echo Url::site('shackmeet/view/' . $shackmeet['meet_id']); ?>';">
              <td><?php echo ($shackmeet['start_date'] != null) ? $shackmeet['start_date'] : 'TBD'; ?></td>
              <td><?php echo htmlentities($shackmeet['title']); ?></td>
              <td><?php echo htmlentities($shackmeet['organizer']); ?></td>
              <td><?php 
              
              $address = '';
              
              if (strlen($shackmeet['state']) > 0)
              {
                $address = $shackmeet['state'];
                
                if (strlen($shackmeet['country']) > 0)
                  $address .= ', ' . $shackmeet['country'];
              }
              else
                $address = $shackmeet['country'];
              
              echo $address; 
              
              ?></td>
              <td class="definite" title="The number of shackers marked as definite attending"><?php echo ($shackmeet['attendee_count_definite'] != null) ? $shackmeet['attendee_count_definite'] : '0'; ?></td>
              <td class="maybe" title="The number of shackers marked as maybe attending"><?php echo ($shackmeet['attendee_count_maybe'] != null) ? $shackmeet['attendee_count_maybe'] : '0'; ?></td>
            </tr>
            <?php endforeach; ?>       
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <p>There are no upcoming shackmeets at the moment. :(</p>
      <?php endif; ?>
    </div>

    <?php if (false && count($unscheduled_shackmeets) > 0): ?>

    <div class="body-header" style="margin-top: 30px;">
      <div class="body-header-left">
        <h2>Unscheduled Shackmeets</h2>
      </div>
      <div class="body-header-right">

      </div>
    </div>

    <div class="body-content">
      <div class="content-table-container">
        <table id="unscheduled-shackmeets-table" class="meet-listing">
          <thead>
            <th>Title</th>
            <th>Organized By</th>
            <th>Location</th>
            <th style="width: 110px; padding-left: 0;" colspan="2">Attendees</th>
          </thead>
          <tbody>
            <?php foreach ($unscheduled_shackmeets as $shackmeet): ?>
            <tr onclick="document.location.href = '<?php echo Url::site('shackmeet/view/' . $shackmeet['meet_id']); ?>';">         
              <td><?php echo htmlentities($shackmeet['title']); ?></td>
              <td><?php echo htmlentities($shackmeet['organizer']); ?></td>
              <td><?php echo htmlentities($shackmeet['location']); ?></td>
              <td class="definite" title="The number of shackers marked as definite attending"><?php echo ($shackmeet['attendee_count_definite'] != null) ? $shackmeet['attendee_count_definite'] : '0'; ?></td>
              <td class="maybe" title="The number of shackers marked as maybe attending"><?php echo ($shackmeet['attendee_count_maybe'] != null) ? $shackmeet['attendee_count_maybe'] : '0'; ?></td>
            </tr>
            <?php endforeach; ?>       
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>

    <div class="body-content">
      <p>Looking for a past shackmeet? <a href="<?php echo Url::site('archive'); ?>">Click here to view the archive</a>.</p>
    </div>
  </div>
</div>