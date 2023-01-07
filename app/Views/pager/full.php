<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
if ($pager->getPageCount()<2){
    return;
}
$pager->setSurroundCount(5);
$groupSuffix = @$pager_group ? '_' . $pager_group : '';
$links = $pager->links();
$count = $pager->getLastPageNumber();
$current = $pager->getCurrentPageNumber();
$pageSize = @$_GET["pagesize$groupSuffix"];
$prevLink=null;
$nextLink=null;
$prevItems = 0;
$nextItems = 0;
$pageLinks = [];

foreach($links as $link){
    if ($current>=3 && $link['title']<min($current-2,$count-4)){
        continue;
    }
    if ($current>3 && $link['title']>$current+2){
        continue;
    }
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
    if ($prevItems+$nextItems>4 && $current<5){
        continue;
    }
    $pageLinks[] = $link;
}
?>

<nav class="pager" aria-label="<?= lang('Pager.pageNavigation') ?>">
	<ul class="pagination">
			<li class="button">
                <a href="<?= $pager->getFirst() ?>" <?php if (!$prevLink) echo "disabled" ?> aria-label="<?= lang('Pager.first') ?>">
					<span aria-hidden="true">
                        <img height="16" width="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEQAAAA/CAMAAABuKJ6CAAABTVBMVEUAAACHh4eIiIiIiIiIiIiIiIiZmZmIiIiJiYmIiIiIiIiJiYmIiIiHh4eIiIiHh4eHh4eGhoaIiIiqqqqJiYmJiYmIiIiMjIyIiIiHh4eIiIiGhoaIiIiHh4eIiIiLi4uIiIiIiIiIiIiIiIiJiYmIiIiIiIiHh4eIiIiHh4eHh4eIiIiKioqIiIiJiYmJiYmIiIiZmZmIiIiHh4eIiIiFhYWHh4eHh4eIiIiMjIyIiIiIiIiHh4eIiIiIiIiIiIiIiIiIiIiIiIiNjY2IiIiKioqIiIiAgICJiYmHh4eHh4eJiYmHh4eIiIiIiIiKioqIiIiJiYmIiIiGhoaIiIiIiIiIiIiAgICIiIiIiIiIiIiIiIiIiIiIiIiEhISHh4eAgICHh4eIiIiHh4eJiYmIiIiJiYmIiIiKioqIiIiJiYmIiIiLi4uHh4eJiYmYHiaSAAAAb3RSTlMARmn/99AKlNFY4Ryl8y23ez/IA4xQ2hSeYusmr3P9N8GFSdINllrkHqhs9TC5fUHLBY9T3BegZO4osnZAyXwvuGv0HadZ4wyVSIQ2wHL8Ja5h6hOdT9kCiz7HerbyG1cKk8+CNL5w+iOsX+gWEZvUkfPtAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABTklEQVR4nK3XRVMDQRiE4SaDhBCCu7u7u7u7u+v/PxIoCrZgZ/ebne77+9wboCwh5FhgRP0uMUCeZI0kp6iwLZIaUbZIWvSzsUNC6coWiWV8NxZI5k8TGMnKVrZITq4jCYjk5StbpKBQKVukqFjZIiWlfwlzpKz8v2GIVFS6EIZIlSthhFTXaAw5UlunI+RIfYPeECKNTR6EEGlu8TQkSGubNyFB2v0If6Sj09/wQbq6BYQP0tMrMryQvn4Z4YUMDEoNLTI0LCa0yIgBoUVGxwgIMM5AMDFJQICpaQKCmVkCAszNExAsLBIQYClMQIBlBoKVVQICrK0TEGxsEhBga5uAYGeXgAB7+wQEB4cEBDg6JiDACQNB7JSAxH/BGQHBeZSAABcRAoLLKwISr64JCHDDQHB7R0CA+wcCgscnAgI8v3whr84ZI3h7N29c9wFL3Tbx+AH+lwAAAABJRU5ErkJggg==">
                    </span>
				</a>
			</li>
			<li class="button">
				<a href="<?= @$prevLink['uri'] ?>" <?php if (!$prevLink) echo "disabled" ?> aria-label="<?= lang('Pager.previous') ?>">
					<span aria-hidden="true">
                        <img width="16" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAA+CAMAAACx4dPGAAAA21BMVEUAAACLi4uJiYmJiYmJiYmIiIiIiIiIiIiIiIiJiYmAgICJiYmJiYmIiIiKioqIiIiIiIiIiIiIiIiIiIiIiIiHh4eIiIiAgICIiIiIiIiIiIiJiYmIiIiIiIiIiIiHh4eIiIiJiYmJiYmIiIiLi4uIiIiIiIiIiIiHh4eIiIiIiIiIiIiHh4eIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiHh4eIiIiIiIiIiIiIiIiGhoaHh4eHh4eAgICGhoaAgICSkpKJiYmJiYmJiYmJiYmqqqqKioqJiYmJiYlQinHdAAAASXRSTlMAFmOiKbL/eD7GBIxS2xihZ/AttnxCygiQVt8cpWvzMbl/Rc4LlFrjIKlv9zW9g0nSD5he5yStcvvBOcJzEEoMB0F7te8DPXexuzMogQAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAUNJREFUeJy91tdOA0EMBVADwRBCDRA6odfQe+/l/7+ISCirYcft5oF5P9rVjH1ton84PTjp7UNFpZ9RM8AMmsEqg2aoxgya4REGzegYM2jGJxg09Ulm0Ewxg2a6waCZmeXy8czcfEYcs7CYC8csLUvEMs0VUVhmVRG6WVtXiWI2NnWhmK1ti0hmZ9cUktnbd0hmWgeeyMyhL0rm6DhCUnNyGhKpOTsPksJcXEZFYa6u4+TXtG4A0fnO7R1uiO6BnyvuDbiE5H3Cl53WwUPwUf/W22OoeMp1HSnSrH8CzSD0qdt0Uh54zS3njh0iSr49WWGl5qgRikZeq+FrzQUt5O35Iw8TZ86JQ8udp8Jw9Of2czaEI/vBSwM3RK9dGKq/4aa9JL3jJlnGkD2xs/RB++hHDTdEn1XcEH11YajyjZv28k/0AzeDLqTsVcKNAAAAAElFTkSuQmCC">
                    </span>
				</a>
			</li>
        <?php foreach ($pageLinks as $link) : ?>
			<li class="<?= $link['active'] ? "active" : 'page-link' ?>">
				<a href="<?= $link['uri'] ?>">
					<?= $link['title'] ?>
				</a>
			</li>
		<?php endforeach ?>

			<li class="button">
				<a href="<?= @$nextLink['uri'] ?>" class="page-link" <?php if (!$nextLink) echo "disabled" ?> aria-label="<?= lang('Pager.next') ?>">
					<span aria-hidden="true">
                        <img width="16" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAA+CAMAAACx4dPGAAAA51BMVEUAAACKioqJiYmJiYmJiYmIiIiIiIiJiYmJiYmLi4uIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmHh4eIiIiJiYmIiIiKioqJiYmIiIiJiYmIiIiJiYmIiIiJiYmIiIiMjIyIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmAgICIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmIiIiSkpKIiIiIiIiIiIiJiYmIiIiIiIiIiIiAgICHh4eIiIiIiIiIiIiFhYWPj4+Hh4eKioqJiYmIiIiIiIiHh4eHh4eHh4eIiIiVlZUPRessAAAATXRSTlMAGEWVDf/kW6oh+XC/NoXTS5kR6F+uJfx0wjqI106dFOxjsil4xj6MBNtSoe9ntS17ykGQB99WpRzza/QIQny28BkQ00qEvviq5FtHDCnTDk4AAAAJcEhZcwAADsQAAA7EAZUrDhsAAAEnSURBVHicndZnUwIxFIXhCL4CdhR7FxUUG2LFRlHB8v9/j6OjIstmk5P9/sxmdm/OucaEPEMBJpUe1g0jGd1ANqcbRsd0A+MTuoHJAMPUtG4gP6MbZgu6gbl53bCwqBtYWtYNrAQYVtd0A+sbumFzSzewXdQN7AQYdvd0A6Wybtg/0A1UBqLJbTg80g0cn+gGTgMM1TPdQO1cN1xc6gaurnXDTV02t3X5PT9HE8zfJ/A3tTv1/1Tv5Tl4kOftsX9EPUwjehXcptKMEpeJu9oOExshicYSVUnGFol2Y49em0mKeItJrJJY46isOOOqxkHjruCo8an6iPFaKfqM5+ry3+RbfqRn2k+eomeevcWveVFWy2/T0VbYL9MVV2WTen0ThTHvH6r4BHl+Ks33EIVBAAAAAElFTkSuQmCC" >
                    </span>
				</a>
			</li>
			<li class="button">
				<a href="<?= $pager->getLast() ?>" <?php if (!$nextLink) echo "disabled" ?> aria-label="<?= lang('Pager.last') ?>">
					<span aria-hidden="true">
                        <img width="16" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEQAAAA/CAMAAABuKJ6CAAABVlBMVEUAAACLi4uIiIiHh4eJiYmAgICHh4eIiIiJiYmIiIiKioqIiIiJiYmIiIiIiIiIiIiIiIiIiIiJiYmGhoaJiYkAAACIiIiJiYmJiYmOjo6IiIiJiYmIiIiHh4eJiYmHh4eJiYmHh4eJiYmIiIiIiIiIiIiLi4uIiIiIiIiIiIiJiYmIiIiJiYmFhYWJiYmIiIiKioqJiYmqqqqIiIiHh4eIiIiGhoaJiYmHh4eJiYmGhoaIiIiJiYmIiIiJiYmIiIiAgICIiIiIiIiIiIiIiIiLi4uIiIiJiYmJiYmEhISJiYmHh4eHh4eOjo6IiIiJiYmIiIiIiIiJiYmIiIiJiYmIiIiDg4OIiIiGhoaHh4eHh4eHh4eIiIiIiIiIiIiIiIiMjIyKioqJiYmFhYWJiYmHh4eHh4eSkpKIiIiJiYmJiYmIiIiKioqIiIiJiYmJiYmDg4OGhobG35F3AAAAcnRSTlMAFmlGkQhs/9HfVaMa8Ge0K3jGPYoB106bEulfrST6cb41gtBHlAvhWKUc87cue8g/jAPaUZ4V62KvJv1zwTiLAsc+erYs8mikG+BXkwnPRYG9NPlwrCPoX5sR102JxTwqZqIZ3lWRB81Df7sy926qIV1trF6oAAAACXBIWXMAAA7EAAAOxAGVKw4bAAABUklEQVR4nK3WxW6EMQwE4GxpStstMzMzMzMzM3Pf/1K1l67a5E/czBwt+ZMsO1KUoiT0j56Y6HwVYuPixUgCfpL4jSApmYAAKakEBOE0AgJE0gkIkMFAkJlFQIDsHAKC3DwCAuQXEBAUFhEQoLiEgKC0jIAA5RUEBKhkIKiqJiBATS0BQV09AQEaGgkImmIICNDcQkCAVgaCtnYCAnR0EhB0dRMQoKeXgKCvn4AAA4MEBEPDBAQYGSUgY+PeyMSk/zhT/tuZnvFe8eyc/8XOL3if/eKS/yteXlG+yOqa+h0xsv6HECMbmxpDhmxt6wgZsrOrNwTI3r6BECAHh0bDFTk6NhOuyEkQ4YacngUbLoj9f25FIudWw4aEL+yEDbm8cjECkesbJyIQub1zNMzI/YMrYUYe3QkT8vQsMbTIy6uI0CJv70JDg3xICaVC0ZG3a/MJF9ZTf3GZ+kIAAAAASUVORK5CYII=" >
                    </span>
				</a>
			</li>
            <li style="flex:0 1 20px"></li>
            <li class="row-selector">
                <span >Rows&nbsp;</span>
                <select onchange="document.location=this.value">
                <?php 
                    foreach ([10,25,50,100] as $size) {
                        $uri = current_url(true);
                        $uri->stripQuery("page$groupSuffix");
                        $uri->stripQuery("pagesize$groupSuffix");
                        $uri->addQuery("pagesize$groupSuffix",$size);
                ?>
                    <option value="<?=$uri?>" <?php if ($pageSize==$size) echo "selected" ?>><?=$size?></option>
                <?php } ?>
                </select>
            </li>
	</ul>
</nav>
