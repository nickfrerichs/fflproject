
<?php $color = array('O' => '#d11414',
                  'D' => 'yellow',
                  'Q' => 'orange',
                  'P' => 'green'); ?>

<span style="color:<?=$color[$text_id]?>;font-size:1em;text-shadow: 1px 1px 3px #000000;" 
      data-tooltip class="has-tip top"
      data-disable-hover="false" 
      tabindex="1" 
      title="Week <?=$week?>: <?=$short_text?> - <?=$injury?>">
      <b><?=strtoupper($text_id)?></b>
</span>