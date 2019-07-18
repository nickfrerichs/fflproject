<!-- <style>
.section{padding-left:20px; padding-right:20px; margin-bottom:20px;}
.section-title{margin-left:20px; padding-right:20px; background-color:#eee; }
.date{font-size:.8em; color:#888; padding-bottom:7px;}
.title{margin-bottom:0px;border-bottom-style: solid; border-bottom-width: 1px;}
.news-body{font-family: 'PT Serif',serif; font-size:1.1em; line-height:1.7em}
table tr:nth-of-type(even) {
    background-color: transparent !important;
}
.right-bar{padding-left:40px; padding-right:40px}
.callout{border-radius:5px}
hr{border: 1px solid #eaeaea;}
/*#2199e8*/
</style> -->
<div class="section">
    <div class="container">
            <div class="tabs is-small is-boxed fflp-tabs-active">
                <ul>
                    <li class="is-active" data-for="news-news-tab" data-load-content="news-content"><a>News</a></li>
                    <li class="" data-for="news-moves-tab" data-load-content="moves-content"><a>Player Moves</a></li>
                    <li class="" data-for="news-standings-tab" data-load-content="standings-content"><a>Standings</a></li>
                    <?php if($show_moneylist):?>
                        <li class="" data-for="moneylist-tab" data-load-content="moneylist-content"><a>Money List</a></li>
                    <?php endif;?>
                </ul>
            </div>

            <div id="news-news-tab">

                <div id="news-content"
                    data-url="<?=site_url('load_content/news_items')?>"
                    data-per-page="3">
                </div>
                <?php $this->load->view('load_content/template/load_more_buttons',array('for' => 'news-content'));?>
                <br>

            </div>

            <div id="news-moves-tab" class="is-hidden">
                <div class="title">Player Moves</div>
                <hr>
                <div id="moves-content" data-url="<?=site_url('load_content/news_moves_items')?>" data-per-page="10">
                </div>
                <?php $this->load->view('load_content/template/load_more_buttons',array('for' => 'moves-content'));?>
            </div>


            <div id="news-standings-tab" class="is-hidden fflp-overflow">
                <div class="title">League Standings</div>
                <hr>
                <div id="standings-content"
                    data-url="<?=site_url('load_content/news_standings')?>">
                </div>
            </div>
        <?php if($show_moneylist): ?>
            <div id="moneylist-tab" class="is-hidden">
            <div class="title">Money List</div>
                <hr>
                <div id="moneylist-content"
                     data-url="<?=site_url('load_content/moneylist')?>">
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>






        <!-- <div id="news-news-list" data-url="<?=site_url('player_search/ajax_news_news_list')?>">
            <?php //debug($news,$this->session->userdata('debug'));?>

        </div>
        <div class="row">
            <div class="columns text-right">
                <ul class="pagination" role="navigation" aria-label="Pagination">
                    <li class="pagination-previous"><a href="#" class="player-list-prev" data-for="news-news-list">Previous</a></li>
                </ul>
            </div>
            <div class="columns text-left small-order-2 medium-order-3">
                <ul class="pagination" role="navigation" aria-label="Pagination">
                    <li class="pagination-next"><a href="#" class="player-list-next" data-for="news-news-list">Next</a></li>
                </ul>
            </div>
        </div> -->



        <!-- <h6 class="text-center"><a href="<?=site_url('myteam/waiverwire/log')?>">Waiver Wire Activity</a></h6>
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
        </div> -->





      <?php if(1==1): ?>

            <!-- <h6 class="text-center"><a href="<?=site_url('myteam/trade/log')?>">Trade Activity</a></h6>

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
            </div> -->


      <?php endif;?>


<script>
  //updatePlayerList('news-ww-list');
  //updatePlayerList('news-trade-list');
  //updatePlayerList('news-news-list');
  loadContent('news-content');

</script>
