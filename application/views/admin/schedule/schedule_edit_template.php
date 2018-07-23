
<?php //print_r($matchups); ?>

<div class="section">
    <div class="columns is-centered">
        <div class="column fflp-lg-container">
            <div class="is-size-5"><?=$template->teams.' teams - '.$template->divisions.' divisions'?></div>
            <br>
            <br>
            <div class="is-size-6">Edit Template Info</div>
            <table class="table is-fullwidth is-narrow fflp-table-fixed is-bordered">
                <tr>
                    <td>Template Name</td>
                    <td><input data-post-id="name" class="input post-edit-info" type="text" value="<?=$template->name?>"></td>
                </tr>
                <tr>
                    <td>Template Description</td>
                    <td><input data-post-id="desc" class="input post-edit-info" type="text" value="<?=$template->description?>"></td>
                </tr>
                <tr>
                    <td>Number of Teams</td>
                    <td><input data-post-id = "num_teams" class="input post-edit-info" type="text" value="<?=$template->teams?>"></td>
                </tr>
                <tr>
                    <td>Number of Divisions</td>
                    <td><input data-post-id = "num_divs" class="input post-edit-info" type="text" value="<?=$template->divisions?>"></td>
                </tr>
                <tr>
                    <td>Number of Regular Season Weeks</td>
                    <td><input data-post-id = "num_weeks" class="input post-edit-info" type="text" value="<?=$template->weeks?>"></td>
                </tr>
                <tr>
                    <td>Games per Week</td>
                    <td><input data-post-id = "per_week" class="input post-edit-info" type="text" value="<?=$template->per_week?>"></td>
                </tr>
            </table>
            <button 
                    class="button is-small is-link post-edit-info ajax-submit-button"
                    data-url="<?=site_url('admin/schedule_templates/ajax_edit_template_info')?>"
                    data-varclass="post-edit-info"
                    data-post-id="template_id"
                    data-reload="false"
                    value="<?=$template->id?>">Update</button>
        </div>
    </div>
    <hr>
    <div class="columns is-centered">
        <div class="column fflp-lg-container">
            <div class="is-size-6">Edit Matchups</div>
            <small>Use numbers to denote teams (Ex: 1, 2, 3, 4)</small>
            <br><br>
            <?php $count=0; ?>

            <?php for($w=1; $w<=$template->weeks; $w++): ?>


                    <table class="table is-fullwidth is-narrow is-bordered">
                    <div class="is-size-6">Week <?=$w?></div>
                    <?php for($g=1; $g<=$template->per_week; $g++): ?>
                    <tr>
                        <td>
                            Home
                            <input class="post-edit-data input" 
                                data-post-arrayid="games"
                                data-post-week="<?=$w?>" 
                                data-post-game="<?=$g?>"
                                data-post-homeaway="home"
                                <?php if (isset($matchups[$w][$g]['home'])): ?>
                                value = "<?=$matchups[$w][$g]['home']?>"
                                <?php endif;?>>
                        </td>
                        <td>
                            Away
                            <input class="post-edit-data input" 
                                data-post-arrayid="games"
                                data-post-week="<?=$w?>" 
                                data-post-game="<?=$g?>"
                                data-post-homeaway="away"
                                <?php if (isset($matchups[$w][$g]['away'])): ?>
                                value = "<?=$matchups[$w][$g]['away']?>"
                                <?php endif;?>>
                        </td>
                    </tr>
                    <?php $count++; ?>
                    <?php endfor; ?>
                    </table>
  
            <?php endfor; ?>


            <button 
                class="button is-small is-link post-edit-data ajax-submit-button"
                data-url="<?=site_url('admin/schedule_templates/ajax_edit_template_data')?>"
                data-varclass="post-edit-data"
                data-post-id="template_id" 
                value="<?=$template->id?>"
                data-reload="false">Update</button>

        </div>
    </div>
</div>