<?php foreach($_notifications as $m): ?>
    <?php if(is_array($this->session->userdata('notification_acks')) && in_array($m['id'],$this->session->userdata('notification_acks'))){continue;}?>
    <div class="columns is-centered">
        <div class="column" style="max-width:500px;">
            <div class="notification <?=$m['class']?> is-link">
                <button class="delete _notification-close" data-ackurl="<?=site_url('common/notification_ack/'.$m['id'])?>"></button>
                <?=$m['message']?>
            </div>
        </div>
    </div>
<?php endforeach; ?>