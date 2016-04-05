<script>
function showResult(str) {
  if (str.length==0) { 
    document.getElementById("livesearch").innerHTML="";
    document.getElementById("livesearch").style.border="0px";
    return;
  }
  xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      x=xmlhttp.responseXML.documentElement.getElementsByTagName("player");
      txt = "<table id='livesarch'>";
      for(i=0; i< x.length; i++)
      {
          node = x[i].getElementsByTagName("name")[0];
          txt = txt+"<tr><td>"+node.childNodes[0].nodeValue+"</td></tr>";
      }
      txt = txt+"</table>";
      document.getElementById("livesearch").innerHTML=txt;
      document.getElementById("livesearch").style.border="1px solid #A5ACB2";
    }
  }
  xmlhttp.open("GET","<?php echo site_url() ?>ajax/players/"+str,true);
  xmlhttp.send();
}
</script>

<div id='teamlist'>
    <?php print_r($teamname); ?>
    <?php   echo form_open();
            echo form_input(array('id'=>'addplayer', 'onkeyup' => 'showResult(this.value)', 'size' => '30'));
            echo '<div id="livesearch"></div>';
            echo form_submit('addplayer','Add Player');?>
    <table>        
        <tr>
            <td>Player</td><td>Team</td><td></td>
        </tr>
        <?php foreach ($roster as $player){ ?>
        <tr>
            <td><?php echo $player->short_name; ?></td>
            <td><?php echo $player->club_id; ?></td>
            <td>
                edit
            </td>
        </tr>


        <?php }?>
    </table>
</div>
