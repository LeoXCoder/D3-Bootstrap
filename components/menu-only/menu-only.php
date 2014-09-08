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
	</div>
	<div id="w0-collapse" class="collapse navbar-collapse">
        <ul class="navbar-nav nav">
		    <?php if ($fn = mso_fe('components/_menu/_menu.php')) require($fn); ?>
        </ul>
	</div><!-- /collapse -->
</div><!-- /container -->
</nav>
