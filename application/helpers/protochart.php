<?php

class protochart {
	
	function protochart() {
	
	}

	/*
	 * The chart function creates a nice protochart
	 * 
	 * name: The name of the div element for the chart. Should be unique from all other charts
	 * data: Multi-dimensional array. Ex: array('label1'=>array(1,2,3,4,5),'label2'=>array(3,2,4,2))
	 * options: Multi-dimensional array. Ex: array('bars'=>array('show'=>'true'))
	 *          See protochart site for more details related to options: http://www.deensoft.com/lab/protochart/
	 *          Eaxample bar graph options: array('bars'=>array('show'=>'true'));
	 * custom_color: array with label as key and a RRGGBB code (ex: FF0000) as a value.
	 * width: width of the chart in pixels
	 * height: height of the chart in pixels
	 *
	 */
	public function chart($name='chart',$data,$options_array=null,$custom_color=null,$width=400,$height=300) {
		
		// Set default options
		if($options_array === null) {
			$options_array = array('xaxis'=>array('tickSize'=>'1'));
		}
		
		// Compile options
		$options = '';
		foreach($options_array as $modifying => $opts){
			$options .= $modifying.': {';
			foreach($opts as $key => $val){
				$options .= "$key: $val,
				";
			}
			$options .= '},';
		}
		
		$name = 'protochart_'.$name;
		
		$html = '<script type="text/javascript" charset="utf-8">
			Event.observe(window, \'load\', function() {
				';
				
		// Compile data
		$labels = array();
		$i = 0;
		foreach($data as $label => $data_array){
			$html .= "data$i = new Array(";
			$labels[$i] = $label;
			$show_comma = false;
			if(is_array($data_array)){
				foreach($data_array as $pos => $value){
					if($show_comma){
						$html .= ",";
					}
					$html .= "[$pos,$value]";
					$show_comma = true;
				}
			}
			$html .= ");
			";
			$i++;
		}
		
		$html .= "new Proto.Chart($('$name'),[";
		
		foreach($labels as $i => $label_name){
			
			// Apply custom colors, otherwise use defaults.
			$color = '';
			if(isset($custom_color[$label_name])) $color = "color:\"#".$custom_color[$label_name]."\",";
			
			$html .= "{label: \"$label_name\", $color data: data$i},";
		}
		
		$html .= "],{
				$options
				});		
			});
		</script>
		<div id=\"$name\" style=\"width:".$width."px;height:".$height."px\"></div>";
		
		return $html;
	}

}


?>