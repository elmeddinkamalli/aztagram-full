<?php


class sharedTime
{
    public function __construct()
    {
        if(isset($_POST['created_at'])){
            $this->get_the_different($_POST['created_at']);
        }
    }

    public function get_the_different($created_at, $short = false){
        date_default_timezone_set('Asia/Baku');
        $create_time = strtotime($created_at);

        $cur_date = date('Y-m-d H:i:s');
        $current_time = strtotime($cur_date);


        $diff = $current_time-$create_time;
        //echo date('Y-m-d H:i:s', $diff);
        //echo $diff.'-';
        if($short){
            if($diff<60 && $diff>0){
                $seconds = $diff;
                echo $seconds . 's';
            }else if($diff>60 && $diff<3600){
                $minutes = floor(($diff / 60));
                echo $minutes . 'm';
            }else if($diff>3600 && $diff<86400){
                $hours = floor(($diff / 3600));
                echo $hours . 'h';
            }else if($diff>86400){
                $days = floor($diff / 86400);
                echo $days . 'd';
            }
        }else{
            if($diff<60 && $diff>0){
                $seconds = $diff;
                echo $seconds . ' seconds ago';
            }else if($diff>60 && $diff<3600){
                $minutes = floor(($diff / 60));
                echo $minutes . ' minutes ago';
            }else if($diff>3600 && $diff<86400){
                $hours = floor(($diff / 3600));
                echo $hours . ' hours ago';
            }else if($diff>86400){
                $days = floor($diff / 86400);
                echo $days . ' days ago';
            }
        }
    }
}

$new_time = new sharedTime();