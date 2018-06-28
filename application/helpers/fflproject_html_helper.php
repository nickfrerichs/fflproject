<?php

// This file contains simple html elements in an effort to abstract some of the commonly
// used visual elements. Hopefully doing so can make changing css less cumbersome in the
// future.



function fflp_html_block_begin()
{
    // $bold_bg_color = "#276cda";
    // $bold_color = "white";


        echo '
            <div class="card" style="margin-bottom:10px;">
                <div class="card-content" style="padding-left:1em;">
        ';

}

function fflp_html_block_end()
{
    echo '
            </div>
        </div>
    ';
}


?>
