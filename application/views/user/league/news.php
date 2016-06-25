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
    <div class="columns small-order-1 medium-order-2 small-12 medium-5 large-4 right-bar">
      <div class="row">
        <div class="columns callout small-12">
          <h6 class="text-center">Recent Waiver Wire Activity</h6>

          <div id="news-ww-list" data-url="<?=site_url('player_search/ajax_news_ww_activity')?>">
          </div><br>
          <div class="row">
          <div class="columns text-right">
              <ul class="pagination" role="navigation" aria-label="Pagination">
                  <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="news-ww-list">Previous</a></li>
              </ul>
          </div>
          <div class="columns text-left small-order-2 medium-order-3">
              <ul class="pagination" role="navigation" aria-label="Pagination">
                  <li class="pagination-next"><a href="#" class="player-list-next" data-for="news-ww-list">Next</a></li>
              </ul>
          </div>
          </div>

        </div>

      </div>

      <?php if(1==1): ?>
      <div class="row">
        <div class="columns callout small-12">
          <h6 class="text-center">Trade Activity</h6>

          <div id="news-trade-list" data-url="<?=site_url('player_search/ajax_news_trade_activity')?>">
          </div>
          <br>
          <div class="row">
            <div class="columns text-right">
              <ul class="pagination" role="navigation" aria-label="Pagination">
                  <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="news-trade-list">Previous</a></li>
              </ul>
          </div>
          <div class="columns text-left small-order-2 medium-order-3">
              <ul class="pagination" role="navigation" aria-label="Pagination">
                  <li class="pagination-next"><a href="#" class="player-list-next" data-for="news-trade-list">Next</a></li>
              </ul>
          </div>
          </div>

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

<script>
  updatePlayerList('news-ww-list');
  updatePlayerList('news-trade-list');
</script>

