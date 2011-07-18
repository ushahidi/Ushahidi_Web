<div id="custom_forms">
					
     <?php
	foreach ($disp_custom_fields as $field_id => $field_property)
	{
		$isrequired = "";
		if($field_property['field_required'])
			$isrequired = "<font color=red> *</font>";

		$isprivate = '';	
		if($field_property['field_ispublic_visible'])
			$isprivate = '<font style="color:gray;font-size:70%">(' . Kohana::lang('ui_main.private') . ')</font>';

		
		// Workaround for situtaions where admin can view, but doesn't have suff. perms to edit.
		if (isset($custom_field_mismatch))
		{
			if(isset($custom_field_mismatch[$field_id]))
			{
				if(isset($form['custom_field'][$field_id]))
					echo $form['custom_field'][$field_id];
				else
					echo "no data";
				echo "</div>";
				continue;
			}
		}

		// Give all the elements an id so they can be accessed easily via javascript
		$id_name = 'id="custom_field_'.$field_id.'"';

		if ($field_property['field_type'] == 1)
		{ // Text Field
			echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
			$field_options = customforms::get_custom_field_options($field_id);
			if(isset($field_options['field_hidden']) && !isset($editor))
				if($field_options['field_hidden'] == 1)
					echo form::hidden($field_property['field_name'], $field_property['field_default']);
				else
				{
					echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
					echo form::input('custom_field['.$field_id.']', $field_property['field_default'], $id_name .' class="text custom_text"');
				}
			else
			{
				echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
				echo form::input('custom_field['.$field_id.']', $field_property['field_default'], $id_name .' class="text custom_text"');
			}
			echo "</div>";
		}
		elseif ($field_property['field_type'] == 2)
		{ // TextArea Field
			$field_options = customforms::get_custom_field_options($field_id);
			if(isset($field_options['field_datatype']))
			{
				$extra_fields = $id_name . ' class="textarea custom_text" rows="3"';

				if($field_options['field_datatype'] == 'text')
				{
					echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
					echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
					echo form::textarea('custom_field['.$field_id.']', $field_property['field_default'], $extra_fields);
					echo "</div>";
				}
				
				if($field_options['field_datatype'] == 'markup')
				{
					echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
					echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
					echo form::textarea('custom_field['.$field_id.']', $field_property['field_default'], $extra_fields, false);
					echo "</div>";
				}

				if($field_options['field_datatype'] == 'javascript')
					if(isset($editor))
					{
						echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
						echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
						echo form::textarea('custom_field['.$field_id.']', $field_property['field_default'], $extra_fields, false);
						echo "</div>";
					}
					else
						echo '<script type="text/javascript">' . $field_property['field_default'] . '</script>';
			}
			else
			{
				echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
				echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
				echo form::textarea('custom_field['.$field_id.']', $field_property['field_default'], $id_name .' class="textarea custom_text" rows="3"');
				echo "</div>";
			}
		}
		elseif ($field_property['field_type'] == 3)
		{ // Date Field
			echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
			echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
			echo form::input('custom_field['.$field_id.']', $field_property['field_default'],
			' id="custom_field_'.$field_id.'" class="text"');
			echo "<script type=\"text/javascript\">
				$(document).ready(function() {
				$(\"#custom_field_".$field_id."\").datepicker({ 
				showOn: \"both\", 
				buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\", 
				buttonImageOnly: true 
				});
				});
			</script>";
			echo "</div>";
		}
		elseif ($field_property['field_type'] >=5 && $field_property['field_type'] <=7)
		{// Multiple-selector Fields
			echo "<div class=\"report_row\" id=\"custom_field_row_" . $field_id ."\">";
			echo "<h4>" . $field_property['field_name'] . $isrequired . " " . $isprivate . "</h4>";
			$defaults = explode('::',$field_property['field_default']); 
			$default = 0;

			if(isset($defaults[1]))
				$default = $defaults[1];

			if (isset($form['custom_field'][$field_id]))
				if($form['custom_field'][$field_id] != '')
					$default = $form['custom_field'][$field_id];

			$options = explode(',',$defaults[0]);
			$html ='';	
			switch ($field_property['field_type']){
				case 5:
					foreach($options as $option){
						if($option == $default){
							$set_default = TRUE;	
						}else{
							$set_default = FALSE;	
						}

						$html .= "<span style=\"margin-right: 15px\">";
						$html .= form::label('custom_field['.$field_id.']'," ".$option." ");
						$html .= form::radio('custom_field['.$field_id.']',$option,$set_default, $id_name);
						$html .= "</span>";
					}
					break;
				case 6:
					$multi_defaults = explode(',',$default);
					$cnt = 0;
					$html .= "<table border=\"0\">";
					foreach($options as $option){
						if($cnt % 2 == 0)
							$html .= "<tr>";
						$html .= "<td>";
						$set_default = FALSE;	
						foreach($multi_defaults as $def){
							if($option == $def)
								$set_default = TRUE;	
						}
						$html .= "<span style=\"margin-right: 15px\">";
						$html .= form::checkbox("custom_field[".$field_id.'-'.$cnt.']',$option,$set_default,$id_name);
						$html .= form::label("custom_field[".$field_id.']'," ".$option);
						$html .= "</span>";

						$html .= "</td>";
						if ($cnt % 2 == 1 || $cnt == count($options)-1)
							$html .= "</tr>";

						$cnt++;
					}
					// XXX Hack to deal with required checkboxes that are submitted with nothing checked
					$html .= "</table>";
					$html .= form::hidden("custom_field[".$field_id."-BLANKHACK]",'',$id_name);
					break;
				case 7:
					$ddoptions = array();
					// Semi-hack to deal with dropdown boxes receiving a range like 0-100
					if(preg_match("/[0-9]+-[0-9]+/",$defaults[0]) && count($options == 1)){
						$dashsplit = explode('-',$defaults[0]);
						$start = $dashsplit[0];
						$end = $dashsplit[1];
						for($i = $start; $i <= $end; $i++)
							$ddoptions[$i] = $i;
					}
					else{
						foreach($options as $op)
							$ddoptions[$op] = $op;
					}

					$html .= form::dropdown("custom_field[".$field_id.']',$ddoptions,$default,$id_name);
					break;

			} 
		
			echo $html;
			echo "</div>";
		}	
		elseif ($field_property['field_type'] == 8 )
		{//custom div
			if($field_property['field_default'] != "")
				echo "<div class=\"" . $field_property['field_default'] . "\" $id_name>";
			else
				echo "<div class=\"custom_div\" $id_name >";

			$field_options = customforms::get_custom_field_options($field_id);
			
			if(isset($field_options['field_toggle']) && !isset($editor))
			{
				if($field_options['field_toggle'] >= 1)
				{
					echo "<script type=\"text/javascript\">
						$(function(){
						$('#custom_field_" .$field_id . "_link').click(function() {
  							$('#custom_field_" .$field_id . "_inner').toggle('slow', function() {
    						// Animation complete.
  							});
						});	
					});
					</script>";
					echo "<a href=\"javascript:void(0);\" id=\"custom_field_" . $field_id ."_link\">";
					echo "<h2>" . $field_property['field_name'] . "</h2>";
					echo "</a>";
					$inner_visibility = "visible";
					if($field_options['field_toggle'] == 2)
						$inner_visibility = "none";

					echo "<div id=\"custom_field_" . $field_id . "_inner\" style=\"display:$inner_visibility;\">";
				}
				else
				{
					echo "<h2>" . $field_property['field_name'] . "</h2>";
					echo "<div id=\"custom_field_" . $field_id . "_inner\">";
				}
			}else
				{
					echo "<h2>" . $field_property['field_name'] . "</h2>";
					echo "<div id=\"custom_field_" . $field_id . "_inner\">";
				}
		}
		elseif ($field_property['field_type'] == 9)
		{//end of custom div
			echo "</div></div>";
			if(isset($editor))
				echo "<h4 style=\"padding-top:0px;\">-------" . Kohana::lang('ui_admin.divider_end_field') . "--------</h4>";
		}
		

		if (isset($editor)){
			$form_fields = '';
        	$visibility_selection = array('0' => Kohana::lang('ui_admin.anyone_role'));
			$roles = ORM::factory('role')->find_all();
			foreach($roles as $role){
				$visibility_selection[$role->id] = ucfirst($role->name);
			}
			
			$isrequired = Kohana::lang('ui_admin.no');
			if($field_property['field_required'])
				$isrequired = Kohana::lang('ui_admin.yes');
	
			$form_fields .= "	<div class=\"forms_fields_edit\" style=\"clear:both\">
			<a href=\"javascript:fieldAction('e','EDIT',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">EDIT</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('d','DELETE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">DELETE</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('mu','MOVE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">MOVE UP</a>&nbsp;|&nbsp;
			<a href=\"javascript:fieldAction('md','MOVE',".$field_id.",".$form['id'].",".$field_property['field_type'].");\">MOVE DOWN</a>&nbsp;|&nbsp;
			". Kohana::lang('ui_admin.required').": ".$isrequired."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.reports_btn_submit').": ".$visibility_selection[$field_property['field_ispublic_submit']]."&nbsp;|&nbsp;
			". Kohana::lang('ui_main.view').": ".$visibility_selection[$field_property['field_ispublic_visible']]."
			</div>";
			echo $form_fields;
		}
	
		if($field_property['field_type'] != 8 && $field_property['field_type'] != 9)
		{//if we're doing custom divs we don't want these div's to get in the way.
			//echo "</div>";
		}
	}
	?>
</div>

