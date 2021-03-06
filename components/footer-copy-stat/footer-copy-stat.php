<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	(c) MaxSite CMS, http://max-3000.com/
	
	Вывод в подвале копирайтов и статистики.
*/

$pt = new Page_out;

$pt->div_start('footer-copy-stat', 'wrap');

	$pt->div('&copy; ' . getinfo('name_site') . ', ' . date('Y'), 'copyright');
	
	$pt->div_start('statistic');
		
		$CI = & get_instance();	
		
		echo sprintf(
					tf('Работает на <a href="http://max-3000.com/">MaxSite CMS</a> | Время: {elapsed_time} | SQL: %s | Память: {memory_usage}')
						, $CI->db->query_count) 
				. '<!--global_cache_footer--> | ';
	
		if (is_login())
		{
			echo $pt->link(getinfo('siteurl') . 'admin', tf('Управление'))
				. ' | '
				. $pt->link(getinfo('siteurl') . 'logout', tf('Выйти'));
		}
		else
		{
			echo $pt->link(getinfo('siteurl') . 'login', tf('Вход'));
		}
		
	$pt->div_end('statistic');

$pt->div_end('footer-copy-stat', 'wrap');

# end file