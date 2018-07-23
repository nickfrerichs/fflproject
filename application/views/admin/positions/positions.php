<?php 
    // League admins modal
    $body = '<table class="table is-fullwidth is-narrow">
                <tbody id="position-form">

                </tbody>
            </table>
            <button id="save-button" class="button is-link">Add</button>
            ';
    $this->load->view('components/modal', array('id' => 'position-form-modal',
                                                          'title' => 'Add Position',
                                                          'body' => $body,
                                                         'reload_on_close' => True));
?>

<div class="section">
        <?php $this->load->view('admin/positions/year_bar.php'); ?>

    <br><br>
    <div class="is-size-5"><?=$selected_year?> Season</div>
    Pos. definition year range: <?=$def_range['end']?> - <?php if($def_range['start'] == 0){echo "League Origin";}else{echo $def_range['start'];}?>
    <br><br>
    <div class="is-size-6"><a id="show-add-button">Add Position</a></div>
    <br><br>

    <table class="table is-fullwidth is-narrow is-striped">
        <tr><th class="text-left">Name</th><th>Position</th><th>NFL Pos</th><th>Roster Max</th><th>Roster Min</th>
            <th>Start Max</th><th>Start Min</th><th></th></tr>
    <?php foreach($league_positions as $lp): ?>

        <tr>
            <td class="text-left"><?php echo $lp->long_text; ?></td>
            <td><?php echo $lp->text_id;?></td>
            <td>
                <?php foreach(explode(",",$lp->nfl_position_id_list) as $nfl_id){
                    //echo $nfl_id." ";
                    echo $nfl_positions[$nfl_id]." ";
                }?>
            </td>
            <td><?=$lp->max_roster == -1 ? "No max" : $lp->max_roster?></td>
            <td><?=$lp->min_roster == -1 ? "No min" : $lp->min_roster?></td>
            <td><?=$lp->max_start == -1 ? "No max" : $lp->max_start?></td>
            <td><?=$lp->min_start == -1 ? "No min" : $lp->min_start?></td>
            <td>
                <a class="show-edit-button" data-id="<?=$lp->id?>">edit</a>
                <a href='<?php echo site_url('admin/positions/delete/'.$selected_year.'/'.$lp->id); ?>'>delete</a>
            </td>
        </tr>
    <?php endforeach;?>
    </table>
    <br><br>
    <span style="font-style:italic">Use this URL to display to your league (ex: in League Rules): </span><?=site_url('league/rules/positions')?>
</div>


<script>


$("#save-button").on("click",function(){
    var url = "<?=site_url('admin/positions/save')?>";
    var id = $("#save-button").data('id');
    var text_id = $("#short-text").val();
    var long_text = $("#long-text").val();
    var min_roster = $("#roster-min").val();
    var max_roster = $("#roster-max").val();
    var min_start = $("#start-min").val();
    var max_start = $("#start-max").val();
    var league_positions = []
    var year = <?=$selected_year?>

    $("#league-positions option").each(function(){
        league_positions.push($(this).val());
    });
    $.post(url,{"posid":id,"text_id":text_id,"long_text":long_text,"league_positions":league_positions,"year":year,
                "max_roster":max_roster,"min_roster":min_roster,"min_start":min_start,"max_start":max_start},function(data){
                    location.reload();
    });
});

$("#show-add-button").on("click",function(){
    var url = '<?=site_url("admin/positions/ajax_load_position_form")?>';
    $("#type-text").text("Add");
    $("#save-button").text("Add position");
    $("#save-button").data('id',"");
    $.post(url,{},function(data)
    {
        $("#position-form").html(data);
    });
    $("#position-form-modal").addClass('is-active');

});

$(".show-edit-button").on("click",function(){
    var posid = $(this).data('id');
    var url = '<?=site_url("admin/positions/ajax_load_position_form/")?>/'+posid;
    $("#type-text").text("Edit");
    $("#save-button").text("Save position");
    $("#save-button").data('id',posid);
    $.post(url,{},function(data)
    {
        $("#position-form").html(data);
    });
    $("#position-form-modal").addClass('is-active');

});

function MoveItem(fromId, toId)
{
    var fromObj = $("#"+fromId).get(0);
    var toObj = $("#"+toId).get(0);

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


    // var fromObj = $("#"+fromID);
    // var toObj = $("#"+toID);
    //
    // $.each(fromObj.val(), function(index,value){
    //     console.log(fromObj[0].options[value-1].text);
    //
    //     // Get the text and value for this option.
    //     var newText  = fromObj[0].options[value-1].text;
    //     var newValue = fromObj[0].options[value-1].value;
    //
    //     // Create a new option, and add to the other select box.
    //     var newOption = new Option(newText, newValue)
    //     toObj[toObj.length] = newOption;
    //
    //     // Delete the option in the first select box.
    //     fromObj[0].options[value-1] = null;
    //
    // });

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
