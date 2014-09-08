<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# основной файл html-структуры

# секция HEAD
if ($fn = mso_fe('custom/head-section.php')) require($fn); // подключение HEAD из файла
	else mso_default_head_section(); // подключение через функцию

?>

<body<?= (mso_get_val('body_class')) ? ' class="' . mso_get_val('body_class') . '"' : ''; ?>>
<!-- end header -->
<?php 
	mso_hook('body_start');
	if (function_exists('ushka')) echo ushka('body_start');
	if ($fn = mso_fe('custom/body-start.php')) require($fn);
    if ($fn = get_component_fn('menu_component', 'menu-only')) require($fn);
?>
<div class="container">
    <div class="blog-header">
        <?php
        if (function_exists('ushka')) echo ushka('header-pre');
        if ($fn = mso_fe('custom/header-pre.php')) require($fn);
        ?>
        <h1 class="blog-title"><?=getinfo('name_site')?></h1>
        <p class="lead blog-description"><?=getinfo('description_site')?></p>
        <?php
        if (function_exists('ushka')) echo ushka('header-start');
        if ($fn = mso_fe('custom/header-start.php')) require($fn);

        if ($fn = mso_fe('custom/header_components.php')) require($fn);
        else
        {
            if ($fn = get_component_fn('header_component_1')) require($fn);
            if ($fn = get_component_fn('header_component_2')) require($fn);
            if ($fn = get_component_fn('header_component_3')) require($fn);
            if ($fn = get_component_fn('header_component_4')) require($fn);
        }

        if (function_exists('ushka')) echo ushka('header-end');
        if ($fn = mso_fe('custom/header-end.php')) require($fn);

        ?>
    </div>
    <?php if (function_exists('ushka')) echo ushka('header-out'); ?>
	<div class="row">
        <div class="col-sm-9 blog-main">
            <?php
                if (function_exists('ushka')) echo ushka('main-start');
                if ($fn = mso_fe('custom/main-start.php')) require($fn);

                if (function_exists('ushka')) echo ushka('content-start');
                if ($fn = mso_fe('custom/content-start.php')) require($fn);

                if ($fn = mso_fe('custom/content-out.php')) require($fn);
                else
                {
                    global $CONTENT_OUT;
                    echo $CONTENT_OUT;
                }

                if (function_exists('ushka')) echo ushka('content-end');
                if ($fn = mso_fe('custom/content-end.php')) require($fn);
            ?>
        </div><!-- /blog-main -->
        <div class="col-sm-3 blog-sidebar">
        <?php
            if ($fn = mso_fe('custom/sidebars.php')) require($fn);
            else
            {
                mso_show_sidebar('1','<div class="sidebar-module"><div class="panel panel-default">','</div></div>', true);
            }

            if (function_exists('ushka')) echo ushka('main-end');
            if ($fn = mso_fe('custom/main-end.php')) require($fn);
        ?>
        </div><!-- /blog-sidebar -->
	</div><!-- /row -->
</div><!-- /container -->
<?php if (function_exists('ushka')) echo ushka('footer-pre'); ?>
<div class="blog-footer">
    <?php
    if (function_exists('ushka')) echo ushka('footer-start');
    if ($fn = mso_fe('custom/footer-start.php')) require($fn);

    if ($fn = mso_fe('custom/footer_components.php')) require($fn);
    else
    {
        if ($fn = get_component_fn('footer_component_1', 'footer-copy-stat')) require($fn);
        if ($fn = get_component_fn('footer_component_2')) require($fn);
        if ($fn = get_component_fn('footer_component_3')) require($fn);
        if ($fn = get_component_fn('footer_component_4')) require($fn);
    }

    if (function_exists('ushka')) echo ushka('footer-end');
    if ($fn = mso_fe('custom/footer-end.php')) require($fn);
    ?>
</div><!-- /blog-footer -->
<?php
	if ($fn = mso_fe('custom/body-end.php')) require($fn);
	if (function_exists('ushka')) 
	{
		echo ushka('google_analytics'); 
		echo ushka('body_end');
	}
	mso_hook('body_end');
?>
</body></html>