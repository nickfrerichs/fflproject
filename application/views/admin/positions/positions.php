<?php //print_r($nfl_positions)?>

<!-- Confirm modal -->
<div class="reveal tiny" id="position-form-modal" data-reveal data-overlay="true">
    <div class="row">
        <div class="columns">
            <h5><span id="type-text">Add</span> Position</h5>
        </div>
    </div>
    <div class="row">
        <div class="columns">
            <table class="table-condensed">
                <thead>
                    <th style="width:50%"></th><th></th>
                </thead>
                <tbody id="position-form">

                </tbody>
            </table>
            <div class="row">
                <div class="columns">

                </div>
            </div>
            <div class="row">
                <div class="columns">
                    <button id="save-button" class="button">Add</button>
                </div>
            </div>
            <button class="close-button" data-close aria-label="Close modal" type="button">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>


<div class="row">
    <div class="columns">
        <h5>League Positions - <?=$this->session->userdata('current_year')?> Season</h5>
    </div>
</div>
<div class="row">
    <div class="columns">
        <a id="show-add-button">Add Position</a>
    </div>
</div>

<div class="row">
    <div class="columns">
        <table>
            <tr><th>Name</th><th>Position</th><th>NFL Pos</th><th>Roster Max</th><th>Roster Min</th>
                <th>Start Max</th><th>Start Min</th><th></th></tr>
        <?php foreach($league_positions as $lp): ?>

            <tr>
                <td><?php echo $lp->long_text; ?></td>
                <td><?php echo $lp->text_id;?></td>
                <td>
                    <?php foreach(explode(",",$lp->nfl_position_id_list) as $nfl_id){
                        //echo $nfl_id." ";
                        echo $nfl_positions[$nfl_id]." ";
                    }?>
                </td>
                <td><?php echo $lp->max_roster;?></td>
                <td><?php echo $lp->min_roster;?></td>
                <td><?php echo $lp->max_start;?></td>
                <td><?php echo $lp->min_start;?></td>
                <td>
                    <a class="show-edit-button" data-id="<?=$lp->id?>">edit</a>
                    <a href='<?php echo site_url('admin/positions/delete/'.$lp->id); ?>'>delete</a>
                </td>
            </tr>
        <?php endforeach;?>
        </table>
    </div>
</div>

<script LANGUAGE="JScript">

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
    $("#league-positions option").each(function(){
        league_positions.push($(this).val());
    });
    $.post(url,{"posid":id,"text_id":text_id,"long_text":long_text,"league_positions":league_positions,
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
    $("#position-form-modal").foundation('open');
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
    $("#position-form-modal").foundation('open');
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
