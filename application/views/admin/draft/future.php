<div class="section">
    <div class="columns">
        <div class="column">
            <div class="is-size-5">Manage Future Picks</div>
            <?php if (count($pick_years) == 0):?>
                <span style="font-style:italic">No future years exist.</span>
            <?php else: ?>
                <div class="columns">
                    <div class="column is-6">
                        <div class="select">
                            <select id="manage-year">
                            <?php foreach($pick_years as $y): ?>
                                <option value="<?=$y?>"><?=$y?></option>
                            <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="column is-6">
                        <button id="manage-button" class="button is-small is-link">Manage</button>
                    </div>
                </div>
            <?php endif;?>
        </div>

        <div class="column">
            <div class="is-size-5">Create Future Picks for Trading</div>
            <div class="columns">
                <div class="column is-3">
                    <div class="select">
                        <select id="create-year">
                        <?php for($i=$this->session->userdata('current_year')+1; $i<=$this->session->userdata('current_year')+15; $i++): ?>
                            <?php if(in_array($i,$pick_years)){continue;}?>
                                <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor;?>
                        </select>
                    </div>
                </div>
                <div class="column is-5">
                    <div class="select">
                        <select id="num-rounds">
                            <?php for($i=1; $i<=50; $i++): ?>
                                <option value="<?=$i?>"<?php if($i == $default_num_rounds){echo "selected";}?>><?=$i?> rounds</option>
                            <?php endfor;?>
                        </select>
                    </div>
                </div>
                <div class="column is-4">
                    <button id="create-button" class="button is-small is-link">Create</button>
                </div>
            </div>
            <span style="font-style:italic">Will use all current active teams.</span>
        </div>
    </div>
</div>

<script>

$("#create-button").on('click',function(){
    var url="<?=site_url('admin/draft/ajax_create_future_picks')?>";
    var year = $("#create-year").val();
    var rounds = $("#num-rounds").val();
    $.post(url,{'year':year,'rounds':rounds},function(data){
        location.reload();
    },'json');
});

$("#manage-button").on('click',function(){
    var year = $("#manage-year").val();
    window.location.href = "<?=site_url('admin/draft/future_manage')?>"+"/"+year;
});

</script>
