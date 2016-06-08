<style>
.section{padding-left:20px; padding-right:20px; margin-bottom:20px;}
.section-title{margin-left:20px; padding-right:20px; background-color:#eee; }
.date{font-size:.8em; color:#888;}
.title{margin-bottom:0px;}
table tr:nth-of-type(even) {
    background-color: transparent !important;
}
.right-bar{padding-left:40px; padding-right:40px}
.callout{border-color:#eaeaea; border-radius:5px}
hr{border: 1px solid #eaeaea;}
</style>

<div class='row'>
    <div class="columns small-12 medium-order-1 small-order-2 medium-7 large-8">
            <?php //debug($news,$this->session->userdata('debug'));?>
            <?php foreach($news as $n): ?>
            <div class="section callout">

              <h5 class="title">
                  <?=$n->title?>
              </h5>
              <div class="date"><?=date("M j g:i a",$n->date_posted)?></div>
              <hr>
              <div>
                <?=$n->data?>
              </div>
            </div>
            <?php endforeach; ?>
    </div>
    <div class="columns small-order-1 medium-order-2 small-12 medium-5 large-4 callout right-bar">
      <div class="row">
        <div class="columns">

                <h6 class="text-center">Recent Waiver Wire Activity</h6>

        <?php if (count($waiverwire_log) > 0): ?>
            <br>
            <?php foreach($waiverwire_log as $i=>$w): ?>
              <div>
                  <span><?=$w->team_name?></span> <span class="date"><?=date("M j g:i a",$w->transaction_date)?></span><br>
                  <span><strong>Add: </strong><?=$w->pickup_short_name?> <?=$w->pickup_pos?> <?=$w->pickup_club_id?></span><br>
                  <span><strong>Drop: </strong><?=$w->drop_short_name?> <?=$w->drop_pos?> <?=$w->drop_club_id?></span>
              </div>
              <?php if($i+1 < count($waiverwire_log)): ?>
                  <hr>
            <?php endif;?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center" style="font-style:italic">Nothing to report</div>
        <?php endif;?>
<!--             <div>
                <span>Nick's Team</span> <span class="date">(9/22 8:03pm)</span><br>
                     <span><strong>Add:</strong> A. Petersen - RB MIN</span><br>
                     <span><strong>Drop:</strong> J. Cutler - RB CHI</span>
            </div>
            <br>
            <div>
                 <span>Matt's Team</span> <span class="date">(9/21 4:23pm)</span><br>
                      <span><strong>Add:</strong> J. Feely - K JAX</span><br>
                      <span><strong>Drop:</strong> E. Manning - QB NYG</span>

            </div> -->
        </div>
      </div>
      <?php if(1==0): ?>
      <div class="row">
        <div class="columns">
         <br><hr>
         <h6>Trade Activity</h6>
        </div>
      </div>
      <?php endif;?>

      <?php if(1==0): ?>
      <div class="row">
        <div class="columns">
         <br><hr>
         <h6>Money List</h6>
        </div>
      </div>
      <?php endif;?>
      <?php //debug($waiverwire_log,$this->session->userdata('debug'))?>
    </div>
</div>
