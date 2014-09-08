<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

echo
'<!DOCTYPE html>
<html lang="ru"' . mso_get_val('head_section_html_add') . '>
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>' . mso_head_meta('title') . '</title>' . mso_hook('head-start') . '
	<meta name="generator" content="MaxSite CMS" />
	<meta name="description" content="' . mso_head_meta('description') . '" />
	<meta name="keywords" content="' . mso_head_meta('keywords') . '" />
	<link rel="shortcut icon" href="' . getinfo('template_url') . 'images/favicons/' . mso_get_option('default_favicon', 'templates', 'favicon.png') . '" />
	';

if (mso_get_option('default_canonical', 'templates', 0)) echo mso_link_rel('canonical');

echo TAB . '<!-- RSS -->' . NT . mso_rss();

if ($fn = mso_fe('custom/head-start.php')) require($fn);

echo TAB . '<!-- CSS -->';
echo '<link rel="stylesheet/less" type="text/css" href="' . getinfo('template_url') . 'css-less/style.less">';
// если есть style.php в шаблоне, то подключается только он, исключая все остальные файлы
if ($fn = mso_fe('css/style.php')) {
    require($fn);
}

if (mso_fe('css/print.css')) {
    echo NT . '<link rel="stylesheet" href="' . getinfo('template_url') . 'css/print.css" media="print" />';
}

// если есть fonts.css, то подключаем его
// файл специально используется для подгрузки шрифтов через @import
mso_add_file('css/fonts.css');

// и import.css для каких-то других @import
mso_add_file('css/import.css');

out_component_css();

echo NT . '<!--[if lt IE 9]>
	<script src="' . getinfo('template_url') . 'js/html5shiv.min.js"></script>
	<script src="' . getinfo('template_url') . 'js/respond.min.js"></script>
	<![endif]-->';

echo NT . '<!-- plugins -->';
mso_hook('head');
echo NT . '<!-- /plugins -->';

mso_add_file('css/add_style.css');

default_out_profiles();

if ($fn = mso_fe('custom/head.php')) require($fn);
if ($fn = mso_page_foreach('head')) require($fn);
if (function_exists('ushka')) echo ushka('head');

if ($my_style = mso_get_option('my_style', 'templates', ''))
    echo NR . '<!-- custom css-my_style -->' . NR . '<style>' . NR . $my_style . '</style>';

mso_hook('head-end');

if (function_exists('ushka')) echo ushka('google_analytics_top');

echo NR . '</head>';
if (!$_POST) flush();
