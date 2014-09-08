<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) LeoXCoder, http://rgblog.ru/
	Описание: Меню Bootstrap.
*/
?>
<?php $pt = new Page_out; ?>
<nav class="navbar-static-top navbar-inverse navbar" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" data-toggle="collapse" data-target="#w0-collapse">
                <span class="sr-only">Открыть меню</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <p class="navbar-text">
                <?php echo mso_date_convert('D, j F Y г.', date('Y-m-d H:i:s'), true, 'Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье', 'января февраля марта апреля мая июня июля августа сентября октября ноября декабря'); ?>
            </p>
        </div>
        <div id="w0-collapse" class="collapse navbar-collapse">
            <ul class="navbar-nav nav navbar-right">
                <?php if ($fn = mso_fe('components/_menu/_menu.php')) require($fn); ?>
            </ul>
        </div><!-- /collapse -->
    </div><!-- /container -->
</nav>