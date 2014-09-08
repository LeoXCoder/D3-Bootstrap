<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) LeoXCoder, http://rgblog.ru/
	Описание: Слайдер Bootstrap.

[slide]
header = заголовок
text = текст с html без переносов
link = ссылка
img = адрес картинки
[/slide]

*/

// где выводить записи
$slides_output = (array) mso_get_option('slides_output', 'templates', array());
if (!$slides_output)  return; // ничего не отмечено - нигде не показывать
if (!in_array('all', $slides_output)) // не отмечено выводить везде
{
	if (!in_array(getinfo('type'), $slides_output)) return;
		elseif (mso_current_paged() > 1) return; // на страницах пагинации не показывать (или показывать?..)
}

// опции слайдера
$slides_def = '
[slide]
header = заголовок1
text = текст с html без переносов
link = http://maxsite.org/
img = TEMPLATE_URL/images/placehold/1140x300.png
[/slide]

[slide]
header = заголовок2
text = текст с html без переносов
link = http://max-3000.com/
img = TEMPLATE_URL/images/placehold/1140x300.png
[/slide]
';

$slides0 = mso_get_option('slides', 'templates', $slides_def);
if (!$slides0) return; // слайды не определены - выходим
$slides0 = str_replace('TEMPLATE_URL/', getinfo('template_url'), $slides0);

// ищем вхождение [slide] ... [slide]
// указываем дефолтные атрибуты полей слайдера
$slides = mso_section_to_array($slides0, '!\[slide\](.*?)\[\/slide\]!is', array('header'=>'', 'text'=>'', 'link'=>'', 'img'=>''));

if (!$slides) return; // нет секций - выходим

mso_hook_add('body_end', 'slider_script', 9);
function slider_script(){
    $slides_play = (int) mso_get_option('slides_play', 'templates', 4000);
    // останавливать смену при hover
    if (mso_get_option('slidesjs_hoverpause', 'templates', ''))
    {
        $slides_hoverpause = 'pause: "hover",';
    }
    echo '<script>$(document).ready(function(){$(".carousel").carousel({'.$slides_hoverpause.' interval: '.$slides_play.'})});</script>';
}

// формируем html-код слайдера
?>
<div class="container">
    <div id="carousel" class="carousel slide" data-ride="carousel">
        <?php if (mso_get_option('slides_pagination', 'templates', 1)) { ?>
        <!-- Индикаторы слайдов -->
        <ol class="carousel-indicators">
            <?php foreach ($slides as $i => $slide) { ?>
            <li data-target="#carousel" data-slide-to="<?= $i ?>"<?=(($i == 0)?' class="active"':'')?>></li>
            <?php } ?>
        </ol>
        <?php } ?>
        <!-- Слайды -->
        <div class="carousel-inner">
            <?php foreach ($slides as $i => $slide) { ?>
                <div class="item<?=(($i == 0)?' active':'')?>">
                    <a href="<?= $slide['link'] ?>"><img src="<?=trim($slide['img'])?>" alt=""></a>
                    <?php if ($slide['header'] and $slide['text']) { ?>
                    <div class="carousel-caption">
                        <h3><?= trim($slide['header']) ?></h3>
                        <p><?= trim($slide['text']) ?></p>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php if (mso_get_option('slides_prev_next', 'templates', 1)) { ?>
        <!-- Стрелки переключения -->
        <a class="left carousel-control" href="#carousel" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="right carousel-control" href="#carousel" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>
        <?php } ?>
    </div>
</div>
