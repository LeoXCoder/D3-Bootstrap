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
mso_set_val('widget_header_start', '<h4>');
mso_set_val('widget_header_end', '</h4>');

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

function forms_content_callback_bootstrap($matches) 
{
    $text = $matches[1];
    
    $text = str_replace("\r", "", $text);
    
    $text = str_replace('&nbsp;', ' ', $text);
    $text = str_replace("\t", ' ', $text);
    $text = str_replace('<br />', "<br>", $text);
    $text = str_replace('<br>', "\n", $text);
    $text = str_replace("\n\n", "\n", $text);
    $text = str_replace('     ', ' ', $text);
    $text = str_replace('    ', ' ', $text);
    $text = str_replace('   ', ' ', $text);
    $text = str_replace('  ', ' ', $text);
    $text = str_replace("\n ", "\n", $text);
    $text = str_replace("\n\n", "\n", $text);
    $text = trim($text);
    
    $out = ''; // убиваем исходный текст формы
    
    // на какой email отправляем
    $r = preg_match_all('!\[email=(.*?)\]!is', $text, $all);
    if ($r)
        $email = trim(implode(' ', $all[1]));
    else
        $email = mso_get_option('admin_email', 'general', 'admin@site.com');
    
    // тема письма
    $r = preg_match_all('!\[subject=(.*?)\]!is', $text, $all);
    if ($r)
        $subject = trim(implode(' ', $all[1]));
    else
        $subject = tf('Обратная связь');
    
    // имя, как оно будет показано в форме
    $r = preg_match_all('!\[name_title=(.*?)\]!is', $text, $all);
    if ($r)
        $name_title = trim(implode(' ', $all[1]));
    else
        $name_title = tf('Ваше имя');
    
    
    // email, как он будет показан в форме
    $r = preg_match_all('!\[email_title=(.*?)\]!is', $text, $all);
    if ($r)
        $email_title = trim(implode(' ', $all[1]));
    else
        $email_title = tf('Ваш email');
    
    
    // куда редиректить после отправки
    $r = preg_match_all('!\[redirect=(.*?)\]!is', $text, $all);
    if ($r)
        $redirect = trim(implode(' ', $all[1]));
    else
        $redirect = '';
    
    
    // ушка к форме
    $r = preg_match_all('!\[ushka=(.*?)\]!is', $text, $all);
    if ($r)
        $ushka = trim(implode(' ', $all[1]));
    else
        $ushka = '';
    
    // отправить копию на ваш email
    $r = preg_match_all('!\[nocopy\]!is', $text, $all);
    if ($r)
        $forms_subscribe = false;
    else
        $forms_subscribe = true;
    
    // кнопка Сброс формы
    $r = preg_match_all('!\[noreset\]!is', $text, $all);
    if ($r)
        $reset = false;
    else
        $reset = true;  
    
    
    // поля формы
    $r = preg_match_all('!\[field\](.*?)\[\/field\]!is', $text, $all);
    
    $f = array(); // массив для полей
    if ($r)
    {
        $fields = $all[1];

        
        if ($subject)
        {
            // поле тема письма делаем в виде обязательнного поля select.
            
            // формируем массив для формы
            $subject_f['require'] = 1;
            
            $subject_f['type'] = (mb_strpos($subject, '#') === false ) ? 'text' : 'select';
            
            // если это одиночное поле, но при этом текст сабжа начинается
            // с _ то ставим тип hidden
            if ($subject_f['type'] == 'text' and mb_strpos($subject, '_') === 0 ) 
            {
                $subject = mb_substr($subject . ' ', 1, -1, 'UTF-8');
                $subject_f['type'] = 'hidden'; 
            }
            
            $subject_f['description'] = tf('Тема письма');
            //$subject_f['tip'] = t('Выберите тему письма');
            $subject_f['values'] = $subject;
            $subject_f['value'] = $subject;
            $subject_f['default'] = '';

            // преобразования, чтобы сделать ключ для поля 
            $f1['subject'] = $subject_f; // у поля тема будет ключ subject
            foreach($f as $key=>$val) $f1[$key] = $val; 
            $f = $f1;
        }
        
        $i = 0;

        foreach ($fields as $val)
        {
            $val = trim($val);
            
            if (!$val) continue;
            
            $val = str_replace(' = ', '=', $val);
            $val = str_replace('= ', '=', $val);
            $val = str_replace(' =', '=', $val);
            $val = explode("\n", $val); // разделим на строки
            $ar_val = array();
            foreach ($val as $pole)
            {
                $pole = preg_replace('!=!', '_VAL_', $pole, 1);
                
                $ar_val = explode('_VAL_', $pole); // строки разделены = type = select
                if ( isset($ar_val[0]) and isset($ar_val[1]))
                    $f[$i][$ar_val[0]] = $ar_val[1];
            }
            
            
            $i++;
        }
        
        if (!$f) return ''; // нет полей - выходим
        
        // теперь по-идее у нас есть вся необходимая информация по полям и по форме
        // смотрим есть ли POST. Если есть, то проверяем введенные поля и если они корректные, 
        // то выполняем отправку почты, выводим сообщение и редиректимся
        
        // если POST нет, то выводим обычную форму
        
        if ($_POST) $_POST = mso_clean_post(array(
            'forms_antispam1' => 'integer',
            'forms_antispam2' => 'integer',
            'forms_antispam' => 'integer',
            'forms_name' => 'base',
            'forms_email' => 'email',
            'forms_session' => 'base',
            ));
        
        if ( $post = mso_check_post(array('forms_session', 'forms_antispam1', 'forms_antispam2', 'forms_antispam',
                    'forms_name', 'forms_email',  'forms_submit' )) )
        {
            mso_checkreferer();
            
            $out .= '<div class="forms-post">';
            // верный email?
            if (!$ok = mso_valid_email($post['forms_email']))
            {
                $out .= '<div class="message error small">' . tf('Неверный email!') . '</div>';
            }
            
            // антиспам 
            if ($ok)
            {
                $antispam1s = (int) $post['forms_antispam1'];
                $antispam2s = (int) $post['forms_antispam2'];
                $antispam3s = (int) $post['forms_antispam'];
                
                if ( ($antispam1s/984 + $antispam2s/765) != $antispam3s )
                { // неверный код
                    $ok = false;
                    $out .= '<div class="message error small">' . tf('Неверная сумма антиспама') . '</div>';
                }
            }
            
            if ($ok) // проверим обязательные поля
            {
                foreach ($f as $key=>$val)
                {
                    if ( $ok and isset($val['require']) and $val['require'] == 1 ) // поле отмечено как обязательное
                    {
                        if (!isset($post['forms_fields'][$key]) or !$post['forms_fields'][$key]) 
                        {
                            $ok = false;
                            $out .= '<div class="message error small">' . tf('Заполните все необходимые поля!') . '</div>';
                        }
                    }
                    if (!$ok) break;
                }
            }
            
            // всё ок
            if ($ok)
            {
                // формируем письмо и отправляем его
                
                if (!mso_valid_email($email)) 
                    $email = mso_get_option('admin_email', 'general', 'admin@site.com'); // куда приходят письма
                    
                $message = t('Имя: ') . $post['forms_name'] . "\n";
                $message .= t('Email: ') . $post['forms_email'] . "\n";
                
                foreach ($post['forms_fields'] as $key=>$val)
                {
                    if ($key === 'subject' and $val)
                    {
                        $subject = $val;
                        continue;
                    }
                    
                    $message .= $f[$key]['description'] . ': ' . $val . "\n\n";
                }
                
                if ($_SERVER['REMOTE_ADDR'] and $_SERVER['HTTP_REFERER'] and $_SERVER['HTTP_USER_AGENT']) 
                {
                    $message .= "\n" . tf('IP-адрес: ') . $_SERVER['REMOTE_ADDR'] . "\n";
                    $message .= tf('Отправлено со страницы: ') . $_SERVER['HTTP_REFERER'] . "\n";
                    $message .= tf('Браузер: ') . $_SERVER['HTTP_USER_AGENT'] . "\n";
                }
                
                // pr($message);
                
                mso_hook('forms_send', $post);
                
                $form_hide = mso_mail($email, $subject, $message, $post['forms_email']);
                
                if ( $forms_subscribe and isset($post['forms_subscribe']) ) 
                    mso_mail($post['forms_email'], tf('Вами отправлено сообщение:') . ' ' . $subject, $message);
                
                
                $out .= '<div class="message ok small">' . tf('Ваше сообщение отправлено!') . '</div><p>' 
                        . str_replace("\n", '<br>', htmlspecialchars($subject. "\n" . $message)) 
                        . '</p>';
                
                if ($redirect) mso_redirect($redirect, true);

            }
            else // какая-то ошибка, опять отображаем форму
            {
                $out .= forms_show_form_bootstrap($f, $ushka, $forms_subscribe, $reset, $subject, $name_title, $email_title);
            }
            
            
            $out .= '</div>';
            
            $out .= mso_load_jquery('jquery.scrollto.js');
            $out .= '<script>$(document).ready(function(){$.scrollTo("div.forms-post", 500, {offset:-45});})</script>';

        }
        else // нет post
        {
            $out .= forms_show_form_bootstrap($f, $ushka, $forms_subscribe, $reset, $subject, $name_title, $email_title);
        }
    }

    return $out;
}

function forms_show_form_bootstrap($f = array(), $ushka = '', $forms_subscribe = true, $reset = true, $subject = '', $name_title = '', $email_title = '')
{
    $out = '';

    $antispam1 = rand(1, 10);
    $antispam2 = rand(1, 10);
    
    $id = 1; // счетчик для id label
    
    if ($subject)
    {
        // поле тема письма делаем в виде обязательнного поля select.
        
        // формируем массив для формы
        $subject_f['require'] = 1;
        
        // если в  subject есть #, то это несколько значений - select
        // если нет, значит обычное текстовое поле
        
        $subject_f['type'] = (mb_strpos($subject, '#') === false ) ? 'text' : 'select';
        
        // если это одиночное поле, но при этом текст сабжа начинается
        // с _ то ставим тип hidden
        if ($subject_f['type'] == 'text' and mb_strpos($subject, '_') === 0 ) 
        {
            $subject = mb_substr($subject . ' ', 1, -1, 'UTF-8');
            $subject_f['type'] = 'hidden'; 
        }
        
        $subject_f['description'] = tf('Тема письма');
        //$subject_f['tip'] = t('Выберите тему письма');
        $subject_f['values'] = $subject;
        $subject_f['value'] = $subject;
        $subject_f['default'] = '';
        
        // преобразования, чтобы сделать ключ для поля 
        $f1['subject'] = $subject_f; // у поля тема будет ключ subject
        
        foreach($f as $key=>$val) $f1[$key] = $val; 
        $f = $f1;
        
    }

    $out .= NR . '<div class="forms"><form method="post" class="form-horizontal" role="form">' . mso_form_session('forms_session');
    
    $out .= '<input type="hidden" name="forms_antispam1" value="' . $antispam1 * 984 . '">';
    $out .= '<input type="hidden" name="forms_antispam2" value="' . $antispam2 * 765 . '">';
    
    // для сохранения отправленных полей смотрим POST
    if (!isset($_POST['forms_name']) or !$pvalue = mso_clean_str($_POST['forms_name'])) $pvalue = '';
    
    // обязательные поля
    if ($name_title)
    {
        $out .= '<div class="form-group"><label class="form-label col-sm-3" title="' . tf('Обязательное поле') . '" for="id-' . ++$id . '">' . $name_title . '*</label><span class=" col-sm-9"><input class="form-control" name="forms_name" type="text" value="' . $pvalue . '" placeholder="' . $name_title . '" required id="id-' . $id . '"></span></div>';
    }
    else 
    {
        $out .= '<div class="form-group"><label class="form-label col-sm-3" title="' . tf('Обязательное поле') . '" for="id-' . ++$id . '">' . tf('Ваше имя*') . '</label><span class=" col-sm-9"><input class="form-control" name="forms_name" type="text" value="' . $pvalue . '" placeholder="' . tf('Ваше имя') . '" required id="id-' . $id . '"></span></div>';
    }

    
    if (!isset($_POST['forms_email']) or !$pvalue = mso_clean_str($_POST['forms_email'], 'base|email')) $pvalue = '';
    
    if ($email_title)
    {
        
        $out .= '<div class="form-group"><label class="form-label col-sm-3" title="' . tf('Обязательное поле') . '" for="id-' . ++$id . '">' . $email_title . '*</label><span class=" col-sm-9"><input class="form-control" name="forms_email" type="email" value="' . $pvalue . '" placeholder="' . $email_title . '" required id="id-' . $id . '"></span></div>';
    }
    else 
    {
        $out .= '<div class="form-group"><label class="form-label col-sm-3" title="' . tf('Обязательное поле') . '" for="id-' . ++$id . '">' . tf('Ваш email*') . '</label><span class=" col-sm-9"><input class="form-control" name="forms_email" type="email" value="' . $pvalue . '" placeholder="' . tf('Ваш email') . '" required id="id-' . $id . '"></span></div>';
    }
    
    
    // тут указанные поля в $f
    foreach ($f as $key=>$val)
    {
        if (!isset($val['type'])) continue;
        if (!isset($val['description'])) $val['description'] = '';
        
        $val['type'] = trim($val['type']);
        $val['description'] = trim($val['description']);
        
        if (isset($val['require']) and  trim($val['require']) == 1) 
        {
            $require = '*';
            $require_title = ' title="' . tf('Обязательное поле') . '"';
            $required = ' required';
        }       
        else 
        {
            $require = '';
            $require_title = '';
            $required = '';
        }
        
        if (isset($val['attr']) and  trim($val['attr'])) $attr = ' ' . trim($val['attr']);
            else $attr = '';
        
        if (isset($val['value']) and  trim($val['value'])) $pole_value = htmlspecialchars(tf(trim($val['value'])));
            else $pole_value = '';
        
        
        // изменим $pole_value значение, если оно было в _POST
        // для полей можно задать правила фильрации функции mso_clean_str
        if (isset($val['clean']) and trim($val['clean'])) $clean = trim($val['clean']);
            else $clean = 'base';
        
        if (isset($_POST['forms_fields'][$key]) and $pvalue = mso_clean_str($_POST['forms_fields'][$key], $clean)) 
            $pole_value = $pvalue;
        
        
        if (isset($val['placeholder']) and  trim($val['placeholder'])) $placeholder = ' placeholder="' . htmlspecialchars(tf(trim($val['placeholder']))) . '"';
            else $placeholder = ''; 
            
        $description = t(trim($val['description']));
        
        if (isset($val['tip']) and trim($val['tip']) ) $tip = NR . '<span class="col-sm-9 col-sm-offset-3"><small>'. trim($val['tip']) . '</small></span>';
            else $tip = '';
            
        if ($val['type'] == 'text') #####
        {
            //type_text - type для input HTML5
            if (isset($val['type_text']) and  trim($val['type_text'])) $type_text = htmlspecialchars(trim($val['type_text']));
                else $type_text = 'text';
            
                $out .= NR . '<div class="form-group"><label class="form-label col-sm-3" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span class=" col-sm-9"><input class="form-control" name="forms_fields[' . $key . ']" type="' . $type_text . '" value="' . $pole_value . '" id="id-' . $id . '"' . $placeholder . $required . $attr . '></span>'.$tip.'</div>';
        
        }
        elseif ($val['type'] == 'select') #####
        {
            if (!isset($val['default'])) continue;
            if (!isset($val['values'])) continue;
            
            $out .= NR . '<div class="form-group"><label class="form-label col-sm-3" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span class="col-sm-9"><select class="form-control" name="forms_fields[' . $key . ']" id="id-' . $id . '"' . $attr . '>';
            
            $default = trim($val['default']);
            $values = explode('#', $val['values']);
            
            foreach ($values as $value)
            {
                $value = trim($value);
                
                if (!$value) continue; // пустые опции не выводим
                
                if ($pole_value and $value == $pole_value)
                {
                    $checked = ' selected="selected"';
                }
                elseif ($value == $default and !$pole_value) 
                {
                    $checked = ' selected="selected"';
                }
                else $checked = '';
                
                $out .= '<option' . $checked . '>' . htmlspecialchars(tf($value)) . '</option>';
            }
            
            $out .= '</select></span></div>' . $tip;

        }
        elseif ($val['type'] == 'textarea') #####
        {
            $out .= NR . '<div class="form-group"><label class="form-label col-sm-3" for="id-' . ++$id . '"' . $require_title . '>' . $description . $require . '</label><span class="col-sm-9"><textarea class="form-control" name="forms_fields[' . $key . ']" id="id-' . $id . '"' . $placeholder . $required. $attr . '>' . $pole_value . '</textarea></span>'.$tip.'</div>';
        
        }
        elseif ($val['type'] == 'hidden') #####
        {
            $out .= NR . '<input name="forms_fields[' . $key . ']" type="hidden" value="' . $pole_value . '" id="id-' . $id . '"' . $attr . '>';
        }
        
    }
    
    // обязательные поля антиспама и отправка и ресет
    $out .= NR . '<div class="forms_antispam form-group"><label class="form-label col-sm-3" for="id-' . ++$id . '">' . $antispam1 . ' + ' . $antispam2 . ' =</label>';
    $out .= '<span class="col-sm-9"><input class="form-control" name="forms_antispam" type="number" required maxlength="3" value="" placeholder="' . tf('Укажите свой ответ') . '" id="id-' . $id . '"></span></div>';
    
    if ($forms_subscribe)
        $out .= NR . '<div class="form-group"><span class="col-sm-9 col-sm-offset-3"><input name="forms_subscribe" value="" type="checkbox"> ' . tf('Отправить копию письма на ваш e-mail') . '</span></div>';
    
    $out .= NR . '<div class="submit col-sm-9 col-sm-offset-3"><button class="btn btn-success" name="forms_submit" type="submit" class="forms_submit">' . tf('Отправить') . '</button>';
    
    if ($reset) $out .= ' <button name="forms_clear" type="reset" class="forms_reset btn btn-default">' . tf('Очистить форму') . '</button>';
    
    $out .= '</div>';
    
    if (function_exists('ushka')) $out .= ushka($ushka);
    
    $out .= '</form></div>' . NR;
    
    return $out;
}

function forms_content_bootstrap($text = '')
{
    if (strpos($text, '[form]') !== false) $text = preg_replace_callback('!\[form\](.*?)\[/form\]!is', 'forms_content_callback_bootstrap', $text );
    return $text;
}

