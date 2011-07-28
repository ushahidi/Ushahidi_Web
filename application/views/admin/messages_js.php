/**
 * Messages js file.
 *
 * Handles javascript stuff related to messages function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>
		
		function limitChars(textid, limit, infodiv)
		{
			var text = $('#'+textid).val();	
			var textlength = text.length;
			if(textlength > limit)
			{
				$('#' + infodiv).html('You cannot write more then '+limit+' characters!');
				$('#'+textid).val(text.substr(0,limit));
				return false;
			}
			else
			{
				$('#' + infodiv).html('You have '+ (limit - textlength) +' characters left.');
				return true;
			}
		}
		
		function showReply(id)
		{
			if (id) {
				$('#' + id).toggle(400);
			}
		}
		
		function sendMessage(id, loader)
		{
			$('#' + loader).html('<img src="<?php echo url::file_loc('img')."media/img/loading_g.gif"; ?>">');
			$.post("<?php echo url::site() . 'admin/messages/send/' ?>", { to_id: id, message: $("#message_" + id).attr("value") },
				function(data){
					if (data.status == 'sent'){
						$('#reply_' + id).hide();
					} else {
						$('#replyerror_' + id).show();
						$('#replyerror_' + id).html(data.message);
						alert('ERROR!');
					}
					$('#' + loader).html('');
			  	}, "json");
		}
		
		function cannedReply(id, field)
		{
			var autoreply;
			$("#" + field).attr("value", "");
			if (id == 1) {
				autoreply = "Thank you for sending a message to Ushahidi. What is the closest town or city for your last message?";
			}else if (id == 2) {
				autoreply = "Thank you for sending a message to Ushahidi. Can you send more information on the incident?"
			};
			$("#" + field).attr("value", autoreply);		
		}

		function messagesAction ( action, confirmAction, message_id )
		{
			var statusMessage;
			if( !isChecked( "message" ) && message_id=='' )
			{ 
				alert('Please select at least one message.');
			} else {
				var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
				if (answer){

					// Set Submit Type
					$("#action").attr("value", action);

					if (message_id != '') 
					{
						// Submit Form For Single Item
						$("#message_single").attr("value", message_id);
						$("#messageMain").submit();
					}
					else
					{
						// Set Hidden form item to 000 so that it doesn't return server side error for blank value
						$("#message_single").attr("value", "000");

						// Submit Form For Multiple Items
						$("#messageMain").submit();
					}

				} else {
				//	return false;
				}
			}
		}
		
		// Preview Message
		function preview ( id ){
			if (id) {
				$('#' + id).toggle(400);
			}
		}
		
