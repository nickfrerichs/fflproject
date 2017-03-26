<div class="row callout">
    <div class="columns">
        <?php //$this->load->view('admin/past_seasons/year_bar.php'); ?>
        <h5>Edit <?=$selected_year?> Season</h5>
        <table>
            <thead>
            </thead>
            <tbody>
                <tr><td><a href="<?=site_url('admin/past_seasons/schedule/'.$selected_year)?>">Schedule</a></td><td>League matchups, game types, titles assigned to games.</td></tr>
                <tr><td><a href="<?=site_url('admin/past_seasons/edit_lineups/'.$selected_year)?>">Starting Lineups</td><td>Modify players who were started each week.</td></tr>
            </tbody>
        </table>

    </div>
</div>