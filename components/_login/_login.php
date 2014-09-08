<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) LeoXCoder, http://rgblog.ru/
	Описание: Форма логина. Подкомпонет.
*/

if (!is_login() && !is_login_comuser()) {
global $MSO;

// если разрешены регистрации, то выводим ссылку
$registration = '';
if (mso_get_option('allow_comment_comusers', 'general', '1')) {
    $registration = ' <a href="' . getinfo('siteurl') . 'registration" class="btn btn-default btn-xs btn-block">' . tf('Регистрация') . '</a>';
}

// возможен вход через соцсеть
$hook_login_form_auth = mso_hook_present('login_form_auth') ? '<span class="login-form-auth-title">' . tf('Вход через:') . ' </span>' . mso_hook('login_form_auth') : '';

if ($hook_login_form_auth) {
    $hook_login_form_auth = trim(str_replace('[end]', '     ', $hook_login_form_auth));
    $hook_login_form_auth = '<p class="login-form-auth">' . str_replace('     ', ', ', $hook_login_form_auth) . '</p>';
} else {
    $hook_login_form_auth = '';
}
?>

<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Вход <span class="caret"></span></a>
    <div class="dropdown-menu" style="padding: 10px;">
        <form class="form" method="post" role="form" action="<?= getinfo('siteurl') ?>login" name="flogin">
            <input type="hidden" value="<?= getinfo('siteurl') . mso_current_url() ?>" name="flogin_redirect">
            <input type="hidden" value="<?= $MSO->data['session']['session_id'] ?>" name="flogin_session_id">
            <input type="text" id="login" name="flogin_user" class="form-control btn-block" placeholder="<?= t('email/логин') ?>">
            <input type="password" id="password" name="flogin_password" class="form-control btn-block" placeholder="<?= t('пароль') ?>">
            <button type="submit" name="flogin_submit" class="btn btn-success btn-block">Вход</button>
            <?= $registration ?>
            <?= $hook_login_form_auth ?>
        </form>
    </div>
</li>
<?php
}

# end file