<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) LeoXCoder, http://rgblog.ru/
	Описание: Меню Bootstrap.
*/
?>
<?php $pt = new Page_out;
$menu_type = mso_get_option('style_menu', 'templates', 'navbar-inverse');
?>
<nav class="navbar-static-top <?= $menu_type ?> navbar" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" data-toggle="collapse" data-target="#w0-collapse">
                <span class="sr-only">Открыть меню</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= getinfo('site_url') ?>"><?= getinfo('name_site') ?></a>
        </div>
        <div id="w0-collapse" class="collapse navbar-collapse">
            <ul class="navbar-nav nav navbar-right">
                <?php if ($fn = mso_fe('components/_menu/_menu.php')) require($fn); ?>
                <?php if ($fn = mso_fe('components/_login/_login.php')) require($fn); ?>
            </ul>
        </div><!-- /collapse -->
    </div><!-- /container -->
</nav>
