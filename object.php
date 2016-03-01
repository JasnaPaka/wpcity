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
    $oc = new ObjectController();
    return $oc->getGoogleMapSettings();
}

function kv_settings2() {
    $oc = new ObjectController();
    return $oc->getAllSettings();
}