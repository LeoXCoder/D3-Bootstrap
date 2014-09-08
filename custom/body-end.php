<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// Scroll To Top
echo '<div id="to_top" style="position: fixed; right: 10px; bottom: 10px; width: 35px; height: 35px; cursor: pointer; background: url(' . getinfo('template_url') . 'images/scroll-to-top.png) no-repeat;" title="Вверх!"></div>';

// JQuery
echo NR . mso_load_jquery();

// Autoload JS
if ($autoload_js = mso_get_path_files(getinfo('template_dir') . 'js/autoload/', getinfo('template_url') . 'js/autoload/', true, array('js'))) {
    foreach ($autoload_js as $fn_js) {
        echo NR . '<script src="' . $fn_js . '"></script>';
    }
}

// Your JS
if (mso_fe('js/my.js'))
    echo NR . '<script src="' . getinfo('template_url') . 'js/my.js"></script>';

echo NR;
