<?php

	function script_tag($file)
	{
		$html = '<script type="text/javascript" src="'.$file.'" charset="utf-8"></script>';
		return $html;
	}

	if ( ! function_exists('link_tag'))
	{
		function link_tag($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = FALSE)
		{
			$CI =& get_instance();

			$link = '<link ';

			if (is_array($href))
			{
				foreach ($href as $k=>$v)
				{
					if ($k == 'href' AND strpos($v, '://') === FALSE)
					{
						if ($index_page === TRUE)
						{
							$link .= 'href="'.$CI->config->site_url($v).'" ';
						}
						else
						{
							$link .= 'href="'.$CI->config->slash_item('base_url').$v.'" ';
						}
					}
					else
					{
						$link .= "$k=\"$v\" ";
					}
				}

				$link .= "/>";
			}
			else
			{
				if ( strpos($href, '://') !== FALSE)
				{
					$link .= 'href="'.$href.'" ';
				}
				elseif ($index_page === TRUE)
				{
					$link .= 'href="'.$CI->config->site_url($href).'" ';
				}
				else
				{
					$link .= 'href="'.$CI->config->slash_item('base_url').$href.'" ';
				}

				$link .= 'rel="'.$rel.'" type="'.$type.'" ';

				if ($media	!= '')
				{
					$link .= 'media="'.$media.'" ';
				}

				if ($title	!= '')
				{
					$link .= 'title="'.$title.'" ';
				}

				$link .= '/>';
			}


			return $link;
		}
	}

?>