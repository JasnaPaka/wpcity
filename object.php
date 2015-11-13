<?php

function kv_random_object() {
    $oc = new ObjectController();
    return $oc->getRandomObjectWithPhoto();
}

function kv_last_object() {
    $oc = new ObjectController();
    return $oc->getLastObjectWithPhoto();	
}

function kv_settings() {
    global $KV_SETTINGS;   
    return $KV_SETTINGS;
}
