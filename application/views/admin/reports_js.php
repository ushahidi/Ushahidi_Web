/*
		* Main Reports Javascript
		*/	
		
		$(document).ready(function()
		{		
			$(".hide").click(function () {
				$("#submitStatus").hide();
				return false;
			});
		});
		
		// Check All / Check None
		function CheckAll( id, name )
		{
			$("INPUT[@name='" + name + "'][type='checkbox']").attr('checked', $('#' + id).is(':checked'));
		}
		
		// Ajax Submission
		function reportAction ( action, confirmAction )
		{
			var statusMessage;
			var answer = confirm('Are You Sure You Want To ' + confirmAction + ' items?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);
				
				// Submit Form
				$("input[@name='incident_id[]'][@checked]").each(
				function() 
				{
					$("#reportMain").submit()
				}
				);			
			}
			else{
				return false;
			}
		}