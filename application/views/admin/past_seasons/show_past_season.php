<div class="section">

        <?php //$this->load->view('admin/past_seasons/year_bar.php'); ?>
        <div class="is-size-5">Edit <?=$selected_year?> Season</div>
        This section is unfinished.
        <table class="table is-fullwidth is-narrow">
            <thead>
            </thead>
            <tbody>
                <tr><td><a href="<?=site_url('admin/schedule/edit/'.$selected_year)?>">Schedule</a></td><td>League matchups, game types, titles assigned to games.</td></tr>
                <!-- <tr><td><a href="<?=site_url('admin/past_seasons/edit_lineups/'.$selected_year)?>">Starting Lineups</td><td>Modify players who were started each week.</td></tr> -->
            </tbody>
        </table>

    </div>
</div>