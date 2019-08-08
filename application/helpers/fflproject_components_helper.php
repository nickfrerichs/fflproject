<?php

// This file contains larger and more complex components in order to try to
// compartmentalize and standardize some of the common functionality used
// throughout the site.


// PLAYER SEARCH TABLE
function fflp_player_search_table($id,$url,$order="asc",$by="",$pos_dropdown=array(),$headers=array(),$classes="",$disable_search=False,$per_page=10,$check=array())
{
    // ID
    // Any sort by fields for table header row
    // URL
    // sort by
    // sort order (desc, asc)
    // per_page
    // headers array('by'=>'','order'=>'',classes=>'')
    // $positions[$p->text_id] = $p->id; //drop down select with options

    ?>

        <?php if($disable_search == False): ?>

                <input type="text" class="player-list-text-input input pagination-filter" data-for="<?=$id?>" data-filter="search" placeholder="Search">

        <?php endif;?>
        <div class="columns is-mobile">
            <?php if (count($pos_dropdown)>0): ?>
                <div class="column is-narrow">
                    <div class="control">
                        <div class="select">
                            <select data-for="<?=$id?>" class="player-list-position-select pagination-filter" data-filter="pos">
                                <?php foreach ($pos_dropdown as $text => $posid): ?>
                                    <option value="<?=$posid?>"><?=$text?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <?php if(count($check)>0): ?>
                <div class="column is-narrow" style="margin-top:5px;">
                        <input  class="switch is-link player-list-checkbox" data-for="<?=$id?>" 
                            id="<?=$id?>-checkbox" type="checkbox" <?php if($check['checked']){echo "checked";}?>>
                        <label for="<?=$id?>-checkbox" class="is-size-7"><?=$check['text']?>
                        </label>
                </div>
            <?php endif;?>
        </div>

        <div class="f-scrollbar">
            <table class="table is-fullwidth is-narrow <?=$classes?>">
                <?php if(count($headers)>0): ?>
                    <thead>
                        <?php foreach($headers as $header_text => $h):?>
                            <?php if(isset($h->classes) && is_array($classes)): ?>
                                <th class="<?php implode(" ",$h->classes);?>">
                            <?php else:?>
                                <th>
                            <?php endif;?>
                            <?php if(array_key_exists('order',$h) && array_key_exists('by',$h)):?>
                                <a href="#" 
                                <?php if(array_key_exists('order',$h)){echo 'data-order="'.$h['order'].'"';}?>
                                data-for="<?=$id?>"
                                <?php if(array_key_exists('by',$h)){echo 'data-by="'.$h['by'].'"';}?>
                                class="lc-sort"><?=$header_text?></a>
                            <?php else: ?>
                                <?=$header_text?>
                            <?php endif;?>
                            </th>
                        <?php endforeach;?>
                    </thead>
                <?php endif;?>
                <tbody id="<?=$id?>" data-by="<?=$by?>" data-per-page="<?=$per_page?>" data-order="<?=$order?>" data-url="<?=$url?>">
                </tbody>
            </table>
            <?=fflp_load_more_buttons($id)?>
            <?php //$this->load->view('load_content/template/load_more_buttons',array('for' => $id));?>
    </div>

    <?php
}

// LOAD MORE BUTTONS FOR LOAD CONTENT THINGS
function fflp_load_more_buttons($for)
{
    ?>

    <div class="tabs is-small is-fullwidth">
    <ul>
        <li class="">
            <a class="lc-load-more-button" data-for="<?=$for?>">
                More&nbsp(
                    <span class="lc-count" data-for="<?=$for?>"></span>/
                    <span class="lc-total" data-for="<?=$for?>"></span>)
                    <span class="icon"><i class="fa fa-angle-down"></i></span>
            </a>
        </li>
        <li class="">
            <a class="lc-load-all-button" data-for="<?=$for?>">
            Show All<span class="icon"><i class="fa fa-angle-down"></i></span>
            </a>
        </li>
        <li class="">
            <a class="lc-reset-button" data-for="<?=$for?>">
            Top<span class="icon"><i class="fa fa-angle-up"></i></span>
            </a>
        </li>
    </ul>
</div>
<?php
}

function fflp_modal($id,$title="",$body="",$reload_on_close=False)
{
    ?>

    <div class="modal" id="<?=$id?>" <?php if ($reload_on_close){echo 'data-reloadclose="1"';}?>>
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title"><?=$title?></p>
                <button class="delete modal-close-button" aria-label="close"></button>
            </header>
            <section class="modal-card-body has-text-centered">
                <?=$body?>
            </section>
            <footer class="modal-card-foot">
            <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
            </footer>
        </div>
    </div>

    <?php
}


function fflp_editable_select($id,$url,$options,$selected_val)
{
    ?>
    <?php 
    // Inputs: $id, $value, $blank_value, $url, $options, $selected_val

    ?>
    <div class="field has-addons">
        <div class="control is-expanded">
            <div class="select is-fullwidth">
                <select id="<?=$id?>" class="editable-select" disabled data-url="<?=$url?>">
                <?php foreach($options as $text => $val): ?>
                    <option value="<?=$val?>" <?php if($val == $selected_val){echo "selected";}?>>
                        <?=$text?>
                    </option>
                <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="control">
            <a id="<?=$id?>-edit-button" class="button is-link editable-select-edit-button">edit</a>
        </div>
        <div class="control">
            <a id="<?=$id?>-cancel-button" class="button is-danger editable-select-cancel-button is-hidden">cancel</a>
        </div>
        <div class="control">
            <a id="<?=$id?>-save-button" class="button is-success editable-select-save-button is-hidden">save</a>
        </div>
    </div>
    <?php
}


function fflp_editable_text($id,$url,$value="",$var1)
{
    ?>
    <div class="field has-addons">
        <div class="control is-expanded">
            <input class="input editable-text-input is-fullwidth"
                type="text"
                id="<?=$id?>-input"
                value="<?=$value?>"
                data-url="<?=$url?>"
                <?php if(isset($var1)){echo 'data-var1='.$var1;}?>
                disabled>
            </input>
        </div>
        <div class="control">
            <a id="<?=$id?>-edit-button" class="button is-link editable-text-edit-button">edit</a>
        </div>
        <div class="control">
            <a id="<?=$id?>-cancel-button" class="button is-danger editable-text-cancel-button is-hidden">cancel</a>
        </div>
        <div class="control">
            <a id="<?=$id?>-save-button" class="button is-success editable-text-save-button is-hidden">save</a>
        </div>
    </div>
    <?php
}


function fflp_stat_popup()
{
    ?>

    <div class="modal fflp-overflow" id="stat-popup-modal">
        <div class="modal-background"></div>
        <div class="modal-card" style="width: 800px;">
            <header class="modal-card-head" style="height: 10px;">
                <p class="modal-card-title">Quick Stats</p>
                <button class="delete modal-close-button" aria-label="close"></button>
            </header>
            <section class="modal-card-body has-text-centered">
                <div id="stat-popup-html">
                </div>
            </section>
            <!-- <footer class="modal-card-foot">
            <button class="button modal-close-button is-link is-fullwidth is-medium" aria-label="close">Close</button>
            </footer> -->
        </div>
    </div>

    <script>

    $(document).on('click','.stat-popup',function(e){
        e.preventDefault();
        type = $(this).data('type');
        id = $(this).data('id');
        week = $(this).data('week');
        var p = $(this).position();
        var url = "<?=site_url('quickstats')?>"+"/"+type;
        // console.log(url);
        $.post(url,{'type' : type, 'id' : id, 'week' : week},function(data)
        {
            $("#stat-popup-html").html(data);
            $("#stat-popup-modal").addClass('is-active');
        });

    });


    function showStatsPopup(id, type)
    {
        //var p = $(this).position();
        var url = "<?=site_url('quickstats')?>"+"/"+type;
        // console.log(url);
        $.post(url,{'type' : type, 'id' : id},function(data)
        {
            $("#stat-popup-html").html(data);
            $("#stat-popup-modal").addClass('is-active');
        });
    }

    </script>

    <?php
}


function fflp_toggle_switch($id,$url,$var1,$var2,$is_checked=False,$color="is-link")
{
    ?>
    <div class="field">
        <input  class="switch toggle-control <?=$color?>" <?php if(isset($var1)){echo 'data-var1="'.$var1.'"';} ?>
                                                    <?php if(isset($var2)){echo 'data-var2="'.$var2.'"';} ?>
                                                    data-url="<?=$url?>"
            id="<?=$id?>" type="checkbox" <?php if($is_checked){echo "checked";}?>>
        <label for="<?=$id?>">
        </label>

    </div>
    <?php
}
?>