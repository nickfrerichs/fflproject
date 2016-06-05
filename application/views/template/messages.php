<?php //print_r($_messages); ?>

<?php foreach($_messages as $m): ?>
    <?php if(is_array($this->session->userdata('message_acks')) && in_array($m['id'],$this->session->userdata('message_acks'))){continue;}?>
    <div class="row" style="max-width: 800px;">
        <div class="columns callout small <?=$m['class']?>" data-closable>
            <?=$m['message']?>
            <button data-ackurl="<?=site_url('common/message_ack/'.$m['id'])?>" class="close-button _message-close " type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
<?php endforeach; ?>
