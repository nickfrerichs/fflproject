<head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="<?php echo site_url(); ?>css/global.css" rel="stylesheet"></link>
    <?php
    if (file_exists(FCPATH.'css/'.$v.'.css'))
                echo '<link href="'.site_url().'css/'.$v.'.css" rel="stylesheet"></link>';
    ?>
    <?php
    if (file_exists(FCPATH.'css/'.$this->uri->segment(1).'.css'))
                echo '<link href="'.site_url().'css/'.$this->uri->segment(1).'.css" rel="stylesheet"></link>';
    ?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

                
</head>