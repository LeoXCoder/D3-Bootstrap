<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_set_val('home_list_header', '<div class="home_header">Последние записи</div>');

// используется только если выбран main-шаблон для двух сайдбаров
// mso_register_sidebar('2', t('2-й сайдбар'));

// сайдбары в подвале
mso_register_sidebar('3', t('Подвал: 1-й сайдбар'));
mso_register_sidebar('4', t('Подвал: 2-й сайдбар'));
mso_register_sidebar('5', t('Подвал: 3-й сайдбар'));
mso_register_sidebar('6', t('Подвал: 4-й сайдбар'));
mso_register_sidebar('7', t('Подвал: 5-й сайдбар'));

// заголовок виджета
mso_set_val('widget_header_start', '<div class="panel-heading">');
mso_set_val('widget_header_end', '</div>');

# формируем li-элементы для меню
# элементы представляют собой текст, где каждая строчка один пункт
# каждый пункт делается так:  http://ссылка | название | подсказка | class | class_для_span
# на выходе так:
# <li class="selected"><a href="url"><span>ссылка</span></a></li>
# если первый символ [ то это открывает группу ul
# если ] то закрывает - позволяет создавать многоуровневые меню
# если адрес равен # то ссылка не формируется, только текст <li class=""><span>ссылка</span></li>
# если пункт меню равен --- то формируется разделитель li.divider Имеет смысл только в подпунктах
function bootstrap_menu($menu = '', $select_css = 'selected', $add_link_admin = false)
{
    # добавить ссылку на admin
    if ($add_link_admin and is_login()) $menu .= NR . 'admin|Admin';

    $menu = str_replace("\r", "", $menu); // если это windows
    $menu = str_replace("_NR_", "\n", $menu);
    $menu = str_replace(" ~ ", "\n", $menu);
    $menu = str_replace("\n\n\n", "\n", $menu);
    $menu = str_replace("\n\n", "\n", $menu);

    # в массив
    $menu = explode("\n", trim($menu));

    # обработаем меню на предмет пустых строк, корректности и подсчитаем кол-во элементов
    $count_menu = 0;
    foreach ($menu as $elem) {
        if (strlen(trim($elem)) > 1) $count_menu++;
    }

    # определим текущий url
    $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https://" : "http://";
    $current_url = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $out = '';
    # обходим в цикле

    $i = 1; // номер пункта
    $n = 0; // номер итерации цикла

    $group_in = false;
    $group_in_first = false;
    $group_num = 0; // номер группы
    $group_work = false; // открытая группа?
    $selected_present = false; // есть ли выделеный пункт?
    $group_elem = 0; // элемент в группе

    foreach ($menu as $elem) {
        # разобъем строчку по адрес | название
        $elem = explode('|', trim($elem));

        # должно быть два элемента
        if (count($elem) > 1) {
            $url = trim($elem[0]); // адрес
            $name = trim($elem[1]); // название

            if (isset($elem[2])) $title = ' title="' . htmlspecialchars(trim($elem[2])) . '"';
            else $title = '';

            // если адрес = ## то не выводим ссылку
            $a_link = ($url != '##');

            // нет в адресе http:// - значит это текущий сайт
            if (($url != '#') and strpos($url, 'http://') === false and strpos($url, 'https://') === false) {
                if ($url == '/') $url = getinfo('siteurl'); // это главная
                else $url = getinfo('siteurl') . $url;
            }

            # если текущий адрес совпал, значит мы на этой странице
            if ($url == $current_url) {
                $class = ' ' . $select_css;
                $selected_present = true;
            } else $class = '';

            // возможно указан css-класс
            if (isset($elem[3])) $class .= ' ' . trim($elem[3]);

            // возможно указан class_для_span
            if (isset($elem[4])) $class_span = ' class="' . trim($elem[4]) . '"';
            else $class_span = '';

            # для первого элемента добавляем класс first
            if ($i == 1) $class .= ' first';

            if ($group_in_first) {
                $class .= ' group-first';
                $group_in_first = false;
            }

            # для последнего элемента добавляем класс last
            if ($i == $count_menu) $class .= ' last';

            if ($class == ' ') $class = '';

            if ($group_in) // открываем группу
            {
                if ($a_link) {
                    $out .= '<li class="dropdown"><a href="' . $url . '" class="dropdown-toggle" data-toggle="dropdown">' . $name . '<span class="caret"></span></a>'
                        . NR . '<ul class="dropdown-menu" role="menu">' . NR;
                } else {
                    $out .= '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">' . $name . '<span class="caret"></a>'
                        . NR . '<ul class="dropdown-menu" role="menu">' . NR;
                }


                $group_in = false;
                $group_in_first = true;
            } else {
                if ($group_elem > 0 and array_key_exists($i, $menu) and isset($menu[$n + 1]) and trim($menu[$n + 1]) == ']') $class .= ' group-last';

                if ($a_link) {
                    $out .= '<li class="' . trim($class) . '"><a href="' . $url . '"' . $title . '><span' . $class_span . '>' . $name . '</span></a></li>' . NR;
                } else {
                    $out .= '<li class="' . trim($class) . '"><span' . $class_span . '>' . $name . '</span></li>' . NR;
                }
            }

            $i++;
            $group_elem++;
        } else {
            // если это [, то это начало группы ul
            // если ] то /ul

            if ($elem[0] == '[') {
                $group_in = true;
                $group_elem = 0;
            }

            if ($elem[0] == ']') {
                $group_elem = 0;
                $group_in = false;
                $out .= '</ul>' . NR . '</li>' . NR;
            }

            if ($elem[0] == '---') // разделитель
            {
                $out .= '<li class="divider"></li>' . NR;
            }

        }

        $n++;
    }

    $out = str_replace('<li class="">', '<li>', $out);

    // если ничего не выделено, то для первой группы прописываем класс group-default
    if (!$selected_present)
        $out = str_replace('group-num-1', 'group-num-1 group-default', $out);

    //pr($out, 1);
    return $out;
}

