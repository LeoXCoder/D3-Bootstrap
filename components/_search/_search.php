<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/*
	(c) LeoXCoder, http://rgblog.ru/
	Описание: Форма поиска. Подкомпонет.
*/
?>
<form class="navbar-form navbar-right" method="get" onsubmit="location.href='<?= getinfo('siteurl') ?>search/' + encodeURIComponent(this.s.value).replace(/%20/g, '+'); return false;">
	<div class="form-group">
		<input type="text" class="form-control" name="s" placeholder="Поиск">
	</div>
	<button type="submit" class="btn btn-default">Поиск</button>
</form>
