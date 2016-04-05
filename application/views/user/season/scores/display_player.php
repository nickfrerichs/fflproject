<?php if (isset($player['points'])){$points = $player['points'];}else{$points = '-';}?>
<td>
	<?=$player['pos']?>
</td>
 <td>
	<a href="<?=site_url('season/player/id/'.$player['id'].'/'.$year)?>"><?=$player['name']?></a>
</td>
<?php if(stripos($player['pos'],'_D')===false){$p='off_';}else{$p='def_';}?>
<td>
	<?=$player['points']?>
</td>
