<body>
    <div id='main_wrap'>
        <?php $this->load->view('admin_template/header.php'); ?>
        
        <div id='body_wrap'>
            <?php $this->load->view($v); ?>
        </div>

        <?php $this->load->view('admin_template/footer.php'); ?>
    </div>
</body>