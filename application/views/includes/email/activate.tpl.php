<html>
    <body>
        Activate account for: <?=$identity?> (<?=$email?>)
        <br>
        <br>
        Please click this link to confirm your email address: <?=site_url('accounts/activate/'.$id.'/'.$activation)?>
    </body>
</html>
