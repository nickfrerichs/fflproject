<div class="row callout">
    <div class="columns medium-6 small-12">
        <h5>Manage Future Picks</h5>
        <?php if (count($pick_years) == 0):?>
            <span style="font-style:italic">No future years exist.</span>
        <?php else: ?>
            <div class="row">
                <div class="columns small-6">
                    <select id="manage-year">
                    <?php foreach($pick_years as $y): ?>
                        <option value="<?=$y?>"><?=$y?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="columns small-6">
                    <button id="manage-button" class="button small">Manage</button>
                </div>
            </div>
        <?php endif;?>
    </div>
    <div class="columns medium-6 small-12">
        <h5>Create Future Picks for Trading</h5>
        <div class="row">
            <div class="columns small-3">
                <select id="create-year">
                <?php for($i=$this->session->userdata('current_year')+1; $i<=$this->session->userdata('current_year')+15; $i++): ?>
                    <?php if(in_array($i,$pick_years)){continue;}?>
                        <option value="<?=$i?>"><?=$i?></option>
                <?php endfor;?>
                </select>
            </div>
            <div class="columns small-5">
                <select id="num-rounds">
                    <?php for($i=1; $i<=50; $i++): ?>
                        <option value="<?=$i?>"<?php if($i == $default_num_rounds){echo "selected";}?>><?=$i?> rounds</option>
                    <?php endfor;?>
                </select>
            </div>
            <div class="columns small-4">
                <button id="create-button" class="button small">Create</button>
            </div>
        </div>
        <span style="font-style:italic">Will use all current active teams.</span>
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
