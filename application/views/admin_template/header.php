<link href="<?php echo site_url(); ?>css/header.css" rel="stylesheet"></link>

<script>
    // script to toggle menus visibility
    $(function () {
        $('li.menuheader').children().hide();
        $('li.menuheader').click(function(e) {
              $('li.menuheader').not(this).children().hide();
              $(this).children().slideToggle(100);
              e.stopPropagation();
        });
    });
    // slideup any visible menus when clicking anywhere on document
    $(document).click(function() {
    if ($('li.menuheader').children().is(':visible')) {
        $('li.menuheader', this).children().slideUp(100);
        }})
</script>

<div id="menubar">
    <ul>
        <li class="menuheader">Teams
            <ul class="submenu">
                <li><a href='<?php echo site_url('admin/teams'); ?>'>Teams</a></li>
            </ul>
        </li>
        <li class="menuheader">League
            <ul class="submenu">
                <li><a href='<?php echo site_url('admin/owners'); ?>'>Owners</a></li>
                <li><a href='<?php echo site_url('admin/positions'); ?>'>Positions</a></li>
                <li><a href='<?php echo site_url('admin/scoring'); ?>'>Scoring</a></li>
                <li><a href='<?php echo site_url('admin/schedule'); ?>'>Schedule</a></li>
                <li><a href='<?php echo site_url('admin/divisions'); ?>'>Divisions</a></li>
            </ul>
        </li> 
        <li class='menulink'><a href='<?php echo site_url(); ?>auth/logout'>Logout</a>
        </li>
    </ul>
</div>



<!----
<div class="click-nav">
  <ul class="no-js">
    <li>
      <a href="#" class="clicker">My Team</a>
      <ul>
        <li><a href="#">Roster</a></li>
        <li><a href="#">Starting Lineup</a></li>
        <li><a href="#">Waiver Wire</a></li>
        <li><a href="#">Trades</a></li>
        <li><a href="#">Schedule</a></li>
      </ul>
      <a href="#" class="clicker">Statistics</a>
      <ul>
        <li><a href="#">Standings</a></li>
        <li><a href="#">Player Statistics</a></li>
        <li><a href="#">Team Statistics</a></li>
        <li><a href="#">Trades</a></li>
      </ul>
      <a href="#" class="clicker">League</a>
      <ul>
        <li><a href="#">Schedule</a></li>
        <li><a href="#">Playoffs</a></li>
        <li><a href="#">Draft</a></li>
        <li><a href="#">Trades</a></li>
      </ul>
    </li>
  </ul>
</div>
-->