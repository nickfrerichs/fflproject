<?php if(array_key_exists("previous_week",$schedule) || array_key_exists("current_week",$schedule)):?>
    <div class="section">
        <div class="container">
        <?php if(array_key_exists("current_week",$schedule)): ?>
            <div class="title">Week <?=$this->session->userdata('current_week');?></div>
            <?php $this->load->view('user/season/schedule/oneweek',array('schedule' => $schedule['current_week'])); ?>
        <?php endif;?>
        <br><br>
        <?php if(array_key_exists("previous_week",$schedule)): ?>
            <div class="title">Week <?=$this->session->userdata('current_week')-1;?></div>
            <?php $this->load->view('user/season/schedule/oneweek',array('schedule' => $schedule['current_week'])); ?>
        <?php endif;?>
        </div>
    </div>
<?php endif;?>


<div class="section">
    <div class="container">
    <div class="title">Full Schedule & Results</div>

    <div class="columns is-multiline is-5 is-variable">
    <?php foreach($schedule['weeks'] as $week => $schedule): ?>
        <div class="column is-half-desktop is-12-touch" style="padding-bottom:40px;">
            <div class="subtitle">Week <?=$week?></div>
            <?php $this->load->view('user/season/schedule/oneweek',array('schedule' => $schedule, 'small' => true)); ?>
        </div>
    <?php endforeach; ?>
    </div>
    </div>
</div>
