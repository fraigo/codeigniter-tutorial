<?php

function array_filter_by_keys($array,$keys){
    return array_intersect_key($array, array_flip($keys));
}