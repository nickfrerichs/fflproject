<?php
// ID
// Any sort by fields for table header row
// URL
// sort by
// sort order (desc, asc)
// per_page
// headers array('by'=>'','order'=>'',classes=>'')
// $positions[$p->text_id] = $p->id; //drop down select with options


if (!isset($per_page))
    $per_page = 10;

?>

<div>
    <?php if(isset($disable_search) && $disable_search == True): ?>
    <?php else: ?>
            <input type="text" class="player-list-text-input input pagination-filter" data-for="<?=$id?>" data-filter="search" placeholder="Search">
    <?php endif;?>

    <?php if (isset($pos_dropdown) && is_array($pos_dropdown)): ?>
        <div class='column'>
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



    <table class="table is-fullwidth fflp-table-fixed" >
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
                <?php endif;?>
                </th>
            <?php endforeach;?>
        </thead>
        <tbody id="<?=$id?>" data-by="<?=$by?>" data-per-page="<?=$per_page?>" data-order="<?=$order?>" data-url="<?=$url?>">
        </tbody>
    </table>
    <?php $this->load->view('load_content/template/load_more_buttons',array('for' => $id));?>
</div>
