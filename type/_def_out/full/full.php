<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

if (!$pages) return;

$p = new Page_out();

$p->format('title', '<h2 class="blog-post-title">', '</h2>', true);
$p->format('date', 'D, j F Y г.', '<span class="glyphicon glyphicon-time"></span> <time datetime="[page_date_publish_iso]">', '</time>');
$p->format('cat', ' -&gt; ', '<br /><span class="glyphicon glyphicon-info-sign"></span>&ensp;');
$p->format('tag', ' | ', '&emsp;<span class="glyphicon glyphicon-tags"></span>&ensp;');
$p->format('feed', tf('Комментарии по RSS'), ' | <span>', '</span>');
$p->format('edit', 'Изменить', '&emsp;<span class="glyphicon glyphicon-pencil"></span>&ensp;');
$p->format('view_count', '&emsp;<span class="glyphicon glyphicon-eye-open"></span>&ensp;');
$p->format('comments', '<button type="button" class="btn btn-link right">'.tf('Обсудить').'</button>', tf('Читать комментарии'));

// исключенные записи
$exclude_page_id = mso_get_val('exclude_page_id');


$p->div_start(mso_get_val('container_class', ''));

foreach ($pages as $page)
{
	if ($f = mso_page_foreach(getinfo('type'))) 
	{
		require($f); // подключаем кастомный вывод
		continue; // следующая итерация
	}

	$p->load($page);

	$p->div_start('blog-post');
		
		// для типа может быть свой info-top
		if ($f = mso_page_foreach('info-top-' . getinfo('type'))) 
		{
			require($f);
		}
		else
		{
			if ($f = mso_page_foreach('info-top'))
			{
				require($f);
			}
			else
			{
				$p->line('[title]');
				$p->html('<p class="blog-post-meta">');	
						$p->line('[date][cat][tag][view_count][edit]');
				$p->html('</p>');
			}
		}
		
		if ($f = mso_page_foreach('page-content')) 
		{
			require($f);
		}
		else
		{
			if ($f = mso_page_foreach('page-content-' . getinfo('type'))) 
			{
				require($f);
			}
			else
			{
				$p->div_start('page_content');
					
					if ($f = mso_page_foreach('content')) require($f);
					else
					{
						// если show_thumb_type_ТИП вернул false, то картинку не ставим
						// show_thumb - если нужно отключить для всех типов
						if ( mso_get_val('show_thumb', true)
							 and mso_get_val('show_thumb_type_' . getinfo('type'), true) )
						{
							// вывод миниатюры перед записью
							if ($image_for_page = thumb_generate(
									$p->meta_val('image_for_page'), 
									mso_get_option('image_for_page_width', 'templates', 280),
									mso_get_option('image_for_page_height', 'templates', 210)
								))
							{
								if (mso_get_option('image_for_page_link', 'templates', 1))
								{
									echo $p->page_url(true) . $p->img($image_for_page, mso_get_option('image_for_page_css_class', 'templates', 'image_for_page'), '', $p->val('page_title')) . '</a>';
								}
								else
								{
									echo $p->img($image_for_page, mso_get_option('image_for_page_css_class', 'templates', 'image_for_page'), '', $p->val('page_title'));
								}
							}
						}
						
						$p->content('', '');
					}

					// для page возможен свой info-bottom
					if ($f = mso_page_foreach('info-bottom-' . getinfo('type'))) 
					{
						require($f);
					}
					elseif ($f = mso_page_foreach('info-bottom')) require($f);
					mso_page_content_end();
					$p->line('[comments]');

				$p->div_end('page_content');
			}
		}
		
	$p->div_end('blog-post');
	
	if ($f = mso_page_foreach(getinfo('type') . '-page-only-end')) require($f);
	
	$exclude_page_id[] = $p->val('page_id');
	
} // end foreach

$p->div_end(mso_get_val('container_class'));

mso_set_val('exclude_page_id', $exclude_page_id);

# end file