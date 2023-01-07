<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(0);
?>
<nav>
	<ul class="pager">
		<li >
			<a <?= $pager->hasPrevious() ? '' : 'disabled' ?> href="<?= $pager->getPrevious() ?? '#' ?>" aria-label="<?= lang('Pager.previous') ?>">
            <img width="16" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAA+CAMAAACx4dPGAAAA21BMVEUAAACLi4uJiYmJiYmJiYmIiIiIiIiIiIiIiIiJiYmAgICJiYmJiYmIiIiKioqIiIiIiIiIiIiIiIiIiIiIiIiHh4eIiIiAgICIiIiIiIiIiIiJiYmIiIiIiIiIiIiHh4eIiIiJiYmJiYmIiIiLi4uIiIiIiIiIiIiHh4eIiIiIiIiIiIiHh4eIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiIiHh4eIiIiIiIiIiIiIiIiGhoaHh4eHh4eAgICGhoaAgICSkpKJiYmJiYmJiYmJiYmqqqqKioqJiYmJiYlQinHdAAAASXRSTlMAFmOiKbL/eD7GBIxS2xihZ/AttnxCygiQVt8cpWvzMbl/Rc4LlFrjIKlv9zW9g0nSD5he5yStcvvBOcJzEEoMB0F7te8DPXexuzMogQAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAUNJREFUeJy91tdOA0EMBVADwRBCDRA6odfQe+/l/7+ISCirYcft5oF5P9rVjH1ton84PTjp7UNFpZ9RM8AMmsEqg2aoxgya4REGzegYM2jGJxg09Ulm0Ewxg2a6waCZmeXy8czcfEYcs7CYC8csLUvEMs0VUVhmVRG6WVtXiWI2NnWhmK1ti0hmZ9cUktnbd0hmWgeeyMyhL0rm6DhCUnNyGhKpOTsPksJcXEZFYa6u4+TXtG4A0fnO7R1uiO6BnyvuDbiE5H3Cl53WwUPwUf/W22OoeMp1HSnSrH8CzSD0qdt0Uh54zS3njh0iSr49WWGl5qgRikZeq+FrzQUt5O35Iw8TZ86JQ8udp8Jw9Of2czaEI/vBSwM3RK9dGKq/4aa9JL3jJlnGkD2xs/RB++hHDTdEn1XcEH11YajyjZv28k/0AzeDLqTsVcKNAAAAAElFTkSuQmCC">
			</a>
		</li>
        <li>
            <a><?= $pager->getCurrentPageNumber() ?> / <?= $pager->getPageCount() ?></a>
        </li>
		<li >
			<a <?= $pager->hasNext() ? '' : 'disabled' ?> href="<?= $pager->getnext() ?? '#' ?>" aria-label="<?= lang('Pager.next') ?>">
            <img width="16" height="16" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADMAAAA+CAMAAACx4dPGAAAA51BMVEUAAACKioqJiYmJiYmJiYmIiIiIiIiJiYmJiYmLi4uIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmHh4eIiIiJiYmIiIiKioqJiYmIiIiJiYmIiIiJiYmIiIiJiYmIiIiMjIyIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmAgICIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmIiIiJiYmIiIiSkpKIiIiIiIiIiIiJiYmIiIiIiIiIiIiAgICHh4eIiIiIiIiIiIiFhYWPj4+Hh4eKioqJiYmIiIiIiIiHh4eHh4eHh4eIiIiVlZUPRessAAAATXRSTlMAGEWVDf/kW6oh+XC/NoXTS5kR6F+uJfx0wjqI106dFOxjsil4xj6MBNtSoe9ntS17ykGQB99WpRzza/QIQny28BkQ00qEvviq5FtHDCnTDk4AAAAJcEhZcwAADsQAAA7EAZUrDhsAAAEnSURBVHicndZnUwIxFIXhCL4CdhR7FxUUG2LFRlHB8v9/j6OjIstmk5P9/sxmdm/OucaEPEMBJpUe1g0jGd1ANqcbRsd0A+MTuoHJAMPUtG4gP6MbZgu6gbl53bCwqBtYWtYNrAQYVtd0A+sbumFzSzewXdQN7AQYdvd0A6Wybtg/0A1UBqLJbTg80g0cn+gGTgMM1TPdQO1cN1xc6gaurnXDTV02t3X5PT9HE8zfJ/A3tTv1/1Tv5Tl4kOftsX9EPUwjehXcptKMEpeJu9oOExshicYSVUnGFol2Y49em0mKeItJrJJY46isOOOqxkHjruCo8an6iPFaKfqM5+ry3+RbfqRn2k+eomeevcWveVFWy2/T0VbYL9MVV2WTen0ThTHvH6r4BHl+Ks33EIVBAAAAAElFTkSuQmCC" >
			</a>
		</li>
	</ul>
</nav>
