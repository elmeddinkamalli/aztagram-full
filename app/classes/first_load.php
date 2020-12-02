<?php


class first_load
{
    //methods
    public function getCurrentPage(){
        $directoryURI = $_SERVER['REQUEST_URI'];
        $path = parse_url($directoryURI, PHP_URL_PATH);
        $components = explode('/', $path);
        return end($components);
    }
}

$new_first_load = new first_load();