<!-- Small menu bar for mobile devices -->
<div class="show-for-small-only small-top-bar row">
    <div class="title-bar small-9 columns align-left row" data-responsive-toggle="main-menu">
            <button class="menu-icon" type="button" data-toggle></button>
            <?php if($this->session->userdata('league_id')): ?>
                <?php if($this->session->userdata('live_scores')){$live="";$title="hide";}else{$live="hide";$title="";}?>
                <span class="<?=$live?>">
                    <a class="livescores-link" href="<?=site_url('season/scores/live')?>">Games in progress</a>
                </span>
                <span class="<?=$title?>">
                    <?=$this->session->userdata('site_name')?>
                </span>
            <?php endif;?>
    </div>
    <div class="small-3 columns text-right">
      <?php if($this->session->userdata('league_id')): ?>
         <button id="chat-button-small" class="button chat-button">chat</button>
      <?php endif;?>

    </div>
</div>

<!-- Normal menu bar for normal computers -->
<div id="title-bar-row" class="row align-spaced align-middle">
    <div class="columns small-12">
        <div class="row align-spaced align-middle">
            <div class="columns">
                <div class="row align-left align-middle">
                    <!-- At one point, I was going to put in a logo, this is where it was.
                    <div id="site-logo" class="columns show-for-medium shrink">

                    </div>
                    -->
                    <div id="league-site-names" class="columns shrink">
                        <div class="site-title hide-for-small-only hide-for-small-custom"><?=$this->session->userdata('site_name')?></div>
                        <div class="league-name-title hide-for-small-only hide-for-small-custom"><?=$this->session->userdata('league_name')?></div>
                    </div>
                </div>
            </div>

            <div class="top-bar columns shrink" id="main-menu">
                <ul class="menu show-for-small-only site-title-small"><?=$this->session->userdata('league_name')?></ul>
                <ul class="dropdown menu medium-horizontal drilldown vertical" data-responsive-menu="drilldown medium-dropdown">
                    <?php foreach($menu_items as $button => $subitem): ?>
                        <?php if (!is_array($subitem)): ?>
                            <li><a href="<?=site_url($subitem)?>"><?=$button?></a></li>
                        <?php continue;?>
                        <?php endif;?>
                      <li><a href="#"><?=$button?></a>
                          <ul class="menu vertical">
                          <?php foreach($subitem as $subtext => $url): ?>
                              <li><a href="<?=site_url($url)?>"><?=$subtext?></a></li>
                          <?php endforeach; ?>
                          </ul>
                      </li>
                    <?php endforeach; ?>
                    <li class="show-for-small-only">
                        <a href="<?=site_url('auth/logout')?>">Logoff</a>
                    </li>
                </ul>
            </div>
            <div class="align-right columns shrink">
                <?php if($this->session->userdata('league_id')): ?>
                        <button id="chat-button" class="button chat-button show-for-medium">chat<span class="unread-count"></span></button>
                <?php endif;?>
            </div>
            <div class="hide-for-small-only align-center" style="padding-right:5px;">
                <a href="<?=site_url('auth/logout')?>"><i class="fi-power columns" style="font-size:1.5em"></i></a>
            </div>
        </div>
        <div id="title-bar-small-row" class="row">
            <div class="columns medium-3 hide-for-small-only">
                <?php if($this->session->userdata('league_id')): ?>
                    <?php if($this->session->userdata('live_scores')){$live="";}else{$live="hide";}?>
                            <a class="livescores-link <?=$live?>" href="<?=site_url('season/scores/live')?>">Games in progress</a>
                <?php endif;?>
            </div>
            <div id="whos-online" class="columns text-right small-12 medium-9">
            </div>
        </div>
    </div>
</div>

<?php if($this->session->userdata('league_id')): ?>

<div id="chat-modal" hidden>
    <div id="chat-history-table" class="chat-history-table">
        <table>
            <tbody id="chat-history-ajax" class="chat-history-ajax">
            </tbody>
        </table>
    </div>
    <div>
        <textarea id="chat-message" rows="2" placeholder="You put your trash talk in here..." autofocus></textarea>
    </div>
</div>
<?php endif;?>

<div id="livedata" class="hide">
</div>

<script>

<?php if($this->session->userdata('league_id')): ?>

// Start up an SSE stream to make things live
sse_stream_start();


<?php endif;?>

</script>
