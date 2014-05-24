<div class="body-title-container page-section">
  <div class="body-title">
      <div class="body-title-left">
        <h2>Archived Shackmeets</h2>
      </div>
      <div class="body-title-right">
      
      </div>
    </div>
  </div>
</div>

<div class="body-container page-section">
  <div class="body">	
    <div class="body-content">
     <?php if (count($shackmeets) > 0): ?>     
     <div class="content-table-container">
        <table id="archived-shackmeets-table" class="meet-listing">
          <thead>
            <th class="nosort">Event Date</th>
            <th class="nosort">Title</th>
            <th class="nosort">Organizer</th>
            <th class="nosort">Location</th>
            <th class="nosort" style="width: 110px; padding-left: 0;" colspan="2">Attendees</th>
          </thead>
          <tbody>
            <?php foreach ($shackmeets as $shackmeet): ?>
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
              <td class="definite"><?php echo ($shackmeet['attendee_count_definite'] != null) ? $shackmeet['attendee_count_definite'] : '0'; ?></td>
              <td class="maybe"><?php echo ($shackmeet['attendee_count_maybe'] != null) ? $shackmeet['attendee_count_maybe'] : '0'; ?></td>
            </tr>
            <?php endforeach; ?>       
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <p>There are no past shackmeets. :(</p>
      <?php endif; ?>
    </div>
  </div>
</div>