<?php
$html = "<div class=\"fullscreenmap_cats\">";
$html .= "<div id=\"fullscreenmap_window_title\" class=\"fullscreenmap_window_title clearingfix\">";
$html .= "	<h2>".Kohana::lang('ui_main.category_filter')."</h2>";
$html .= "</div>";

$html .= "<ul class=\"category-filters\">";
$html .= "	<li><a class=\"active\" id=\"cat_0\" href=\"#\"><div class=\"swatch\" style=\"background-color:#".$default_map_all."\"></div><div class=\"category-title\">".Kohana::lang('ui_main.all_categories')."</div></a></li>";

foreach ($categories as $category => $category_info)
{
	$category_title = $category_info[0];
	$category_color = $category_info[1];
	$category_image = '';
	$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
	if($category_info[2] != NULL && file_exists('media/uploads/'.$category_info[2])) {
		$category_image = html::image(array(
			'src'=>'media/uploads/'.$category_info[2],
			'style'=>'float:left;padding-right:5px;'
			));
		$color_css = '';
	}
	$html .=  '<li><a href="#" id="cat_'. $category .'"><div '.$color_css.'>'.$category_image.'</div><div class="category-title">'.$category_title.'</div></a>';
	// Get Children
	$html .=  '<div class="hide" id="child_'. $category .'"><ul>';
	foreach ($category_info[3] as $child => $child_info)
	{
		$child_title = $child_info[0];
		$child_color = $child_info[1];
		$child_image = '';
		$color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
		if($child_info[2] != NULL && file_exists('media/uploads/'.$child_info[2])) {
			$child_image = html::image(array(
				'src'=>'media/uploads/'.$child_info[2],
				'style'=>'float:left;padding-right:5px;'
				));
			$color_css = '';
		}
		$html .=  '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><div '.$color_css.'>'.$child_image.'</div><div class="category-title">'.$child_title.'</div></a></li>';
	}
	$html .=  '</ul></div></li>';
}
$html .= "</ul>";

$html .= "</div>";

echo json_encode($html);
?>
