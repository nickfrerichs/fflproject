<?php
// ID
// Any sort by fields for table header row
// URL
// sort by
// sort order (desc, asc)
// per_page
// headers array('by'=>'','order'=>'',classes=>'')
// $dropdowns['id']['options'][$p->text_id] = $p->id; //drop down select with options
// $dropdowns['id']['label']


if (!isset($per_page))
    $per_page = 10;

?>
    <div class="columns">
        <div class="column is-8-tablet is-4-desktop">
            <?php if(isset($disable_search) && $disable_search == True): ?>
            <?php else: ?>
                <div class="field">
                    <div class="control">
                        <input type="text" class="player-list-text-input input pagination-filter" data-for="<?=$id?>" data-filter="search" placeholder="Search">
                    </div>
                </div>
            <?php endif;?>

            <?php if (isset($pos_dropdown) && is_array($pos_dropdown)): ?>
                <div class="field">
                    <div class="control">
                        <div class="select">
                            <select data-for="<?=$id?>" class="pagination-filter" data-filter="pos">
                                <?php foreach ($pos_dropdown as $text => $posid): ?>
                                    <option value="<?=$posid?>"><?=$text?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            <?php if (isset($dropdowns) && is_array($dropdowns)): ?>
                <div class="columns">
                    <?php foreach($dropdowns as $dd_id => $dd_data): ?>
                    <div class="column">
                        <div class="field">
                            <label class="label"><?=$dd_data['label']?></label>
                            <div class="control">
                                <div class="select">
                                    <select data-for="<?=$id?>" class="pagination-filter" data-filter="<?=$dd_id?>">
                                        <?php foreach ($dd_data['options'] as $item_text => $item_id): ?>
                                            <option value="<?=$item_id?>"><?=$item_text?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
            <?php endif;?>
        </div>
    </div>
    <div class="f-scrollbar">
        <table class="table is-fullwidth f-table-fixed is-size-7-mobile is-striped" style="min-width: 500px;">
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
                    <?php else:?>
                        <?=$header_text?>
                    <?php endif;?>
                    </th>
                <?php endforeach;?>
            </thead>
            <tbody id="<?=$id?>" data-by="<?=$by?>" data-per-page="<?=$per_page?>" data-order="<?=$order?>" data-url="<?=$url?>">
            </tbody>
        </table>
        
    </div>
    <?php $this->load->view('load_content/template/load_more_buttons',array('for' => $id));?>
    
    

