<?php



function html_notification($fullname,$avatar,$datetime,$icon,$title_html,$body_html,$tip,$href){

    $html = "<div class='nt-item nt-active d-flex'>
                    <div class='item-avatar'>
                        <img class='circle' name='avatar-image' height='40' width='40' src='{$avatar}' alt=''>
                            <span class='circle circle-icon'><i class='{$icon}'></i></span>
                    </div>
                    <div class='user-text'>";
                        if($tip == 'BEGEN'): 
                            $html .= html_notification_begen($href,$fullname,$datetime);
                        elseif($tip == 'YORUM'):
                            $html .= html_notification_yorum($href,$fullname,$datetime,$title_html);
                        elseif($tip == "YORUM_BEGEN"):
                            $html .= html_notification_yorum_begen($href,$fullname,$datetime,$title_html);
                        endif;
                    $html .= "</div>";
    $html .= "</div>";

    return $html; 
}

function html_notification_begen($href,$fullname,$datetime){

    $html = "
         <a href='".base_url('a/'.$href)."''>
            <b>{$fullname}</b> paylaşımı beğendi.
            <div><small>{$datetime}</small></div>
        </a>
    ";

    return $html;
}

function html_notification_yorum($href,$fullname,$datetime,$title_html){
    $html = "
        <a href='".base_url('a/'.$href)."''>
            <b>{$fullname}</b> paylaşıma <b>\"</b>{$title_html}<b>\"</b> yorum yaptı.
            <div><small>{$datetime}</small></div>
        </a>    
    ";
    return $html;
}


function html_notification_yorum_begen($href,$fullname,$datetime,$title_html){
    $html = "
        <a href='".base_url('a/'.$href)."''>
            <b>{$fullname}</b> yorumunu beğendi.
            <div><small>{$datetime}</small></div>
        </a>    
    ";
    return $html;
}