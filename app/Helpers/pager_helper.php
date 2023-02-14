<?php

function generatePageLinks($pager,$surround=2){
    $count = $surround * 2 + 1;
    $pageLinks = [];
    $pager->setSurroundCount($count);
    $pages = $pager->getLastPageNumber();
    $current = $pager->getCurrentPageNumber();
    $links = $pager->links();
    $prevItems = 0;
    $nextItems = 0;
    $prevLink = null;
    $nextLink = null;
    foreach($links as $link){
        if ($current>=$surround+1 && $link['title']<min($current-$surround,$pages+1-$count)){
            continue;
        }
        if ($current>$surround+1 && $link['title']>$current+$surround){
            continue;
        }
        $class = 'd-none d-sm-block';
        if ($link['title']<$current){
            $prevItems++;
        }
        if ($link['title']>$current){
            $nextItems++;
        }
        if ($link['title']==$current-1){
            $prevLink = $link;
        }
        if ($link['title']==$current+1){
            $nextLink = $link;
        }
        if ($prevItems+$nextItems>$count-1 && $current<$count){
            continue;
        }
        $link['class'] = $class;
        $pageLinks[] = $link;
    }
    return [
        "links" => $pageLinks,
        "prev" => $prevLink,
        "next" => $nextLink
    ];
}