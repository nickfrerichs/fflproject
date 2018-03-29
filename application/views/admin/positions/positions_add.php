<!-- I think this file can be deleted, no longer used -->
adfafasdf
adfasdf

<div class="row">
    <div class="columns">
        <h5>Add league position</h5>
    </div>
 </div>

 <div class="row">
     <div class="columns">

  <?php
  if(!isset($edit)){$edit = false;}

  $attrib = array('onsubmit' => 'placeInHidden(\'league_positions_\',\'league_positions\')');
  if ($edit) {echo form_open('admin/positions/save/'.$pos->id, $attrib);}
  else {echo form_open('admin/positions/save', $attrib);}
  ?>

          <table>
              <tr>
                  <td><?php echo form_label('Short Name');?></td>
                  <?php $data = array('name' => 'text_id'); if($edit){$data['value'] = $pos->text_id;} ?>
                  <td><?php echo form_input($data);?></td>
              </tr>
              <tr>
                  <td><?php echo form_label('Long Name');?></td>
                  <?php $data = array('name' => 'long_text'); if($edit){$data['value'] = $pos->long_text;} ?>
                  <td><?php echo form_input($data);?></td>
              </tr>
              <tr>
                  <td><?php echo form_label('Roster Max');?></td>
                  <?php $data = array('name' => 'max_roster'); if($edit){$data['value'] = $pos->max_roster;} ?>
                  <td><?php echo form_input($data);?></td>
              </tr>
              <tr>
                  <td><?php echo form_label('Roster Min');?></td>
                  <?php $data = array('name' => 'min_roster'); if($edit){$data['value'] = $pos->min_roster;} ?>
                  <td><?php echo form_input($data);?></td>
              </tr>
              <tr>
                  <td><?php echo form_label('Start Max');?></td>
                  <?php $data = array('name' => 'max_start'); if($edit){$data['value'] = $pos->max_start;} ?>
                  <td><?php echo form_input($data);?></td>
              </tr>
              <tr>
                  <td><?php echo form_label('Start Min');?></td>
                  <?php $data = array('name' => 'min_start'); if($edit){$data['value'] = $pos->min_start;} ?>
                  <td><?php echo form_input($data);?></td>
              </tr>
              <tr>
                  <?php
                      $nfl_pos_array = array(); $lea_pos_array = array();
                      foreach($nfl_positions as $p){$nfl_pos_array[$p->id] = $p->text_id;}
                      if($edit){
                          foreach(explode(',',$pos->nfl_position_id_list) as $p){$lea_pos_array[$p] = $nfl_pos_array[$p];}
                          $nfl_pos_array = array_diff($nfl_pos_array, $lea_pos_array);}
                  ?>
                  <td>
                      <div><?php echo form_label('NFL Positions'); ?></div>
                      <?php echo form_multiselect('nfl_positions',$nfl_pos_array,null,'id=nfl_positions, style="width:125px"');?>
                  </td>
                  <td>
                      <div><?php echo form_label('League Positions'); ?></div>
                      <?php echo form_multiselect('league_positions_',$lea_pos_array,null,'id="league_positions_", style="width:125px"');?>
                  </td>
              </tr>
              <tr>
                  <td>
                      <?php echo form_button('rem_pos','<<','onclick="MoveItem(league_positions_, nfl_positions);"'); ?>
                    </td><td>
                      <?php echo form_button('add_pos','>>','onclick="MoveItem(nfl_positions, league_positions_);"'); ?>
                  </td>
                  <?php echo '<input type="hidden" name="league_positions" id="league_positions">'; ?>
              </tr>
              <tr>
                  <td>
                      <?php if($edit){echo form_submit('save','save');} else {echo form_submit('add','Add Position');} ?>
                  </td>
                  <td></td>
              </tr>
          </table>
    </div>
</div>

<script LANGUAGE="JScript">
function MoveItem(fromObj, toObj)
{
    console.log(fromObj);
   for (var selIndex = fromObj.length - 1; selIndex >= 0; selIndex--)
   {
      // Is this option selected?
      if (fromObj.options[selIndex].selected)
      {
         // Get the text and value for this option.
         var newText  = fromObj.options[selIndex].text;
         var newValue = fromObj.options[selIndex].value;

         // Create a new option, and add to the other select box.
         var newOption = new Option(newText, newValue)
         toObj[toObj.length] = newOption;

         // Delete the option in the first select box.
         fromObj[selIndex] = null;
      }
   }
}

function placeInHidden(selStr, hidStr)
{
  var selObj = document.getElementById(selStr);
  var hideObj = document.getElementById(hidStr);
  hideObj.value = '';

  for (var i=0; i<selObj.options.length; i++) {

    hideObj.value = hideObj.value == '' ? selObj.options[i].value : hideObj.value + "," + selObj.options[i].value;
  }
}
</script>
