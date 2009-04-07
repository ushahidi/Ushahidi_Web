<?php 
/**
 *  Reports view page.
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
?>

<div id="content">
	<div class="content-bg">
		<!-- start reports block -->
		<div class="big-block">
			<div class="big-block-top">
				<div class="big-block-bottom">
					<h1>Reports <?php echo $pagination_stats; ?></h1>
					<div class="report_rowtitle">
	                	<div class="report_col1">
	                    	<strong>MEDIA</strong>
	                    </div>
	                    <div class="report_col2">
	                    	<strong>REPORT TITLE</strong>
	                    </div>
	                    <div class="report_col3">
	                    	<strong>DATE</strong>
	                    </div>
	                    <div class="report_col4">
	                    	<strong>LOCATION</strong>
	                    </div>
	                    <div class="report_col5">
	                    	<strong>VERIFIED?</strong>
	                    </div>
	                </div>
                    <?php
                   	foreach ($incidents as $incident)
                    {
                        $incident_id = $incident->id;
                        $incident_title = $incident->incident_title;
                        $incident_description = $incident->incident_description;

                        // Trim to 150 characters without cutting words
                        //XXX: Perhaps delcare 150 as constant
						$incident_description = text::limit_chars($incident_description, 150, "...", true);
						
                        $incident_date = date('Y-m-d', strtotime($incident->incident_date));
                        $incident_location = $incident->location->location_name;
                        $incident_verified = $incident->incident_verified;
                        if ($incident_verified)
                        {
                            $incident_verified = "<span class=\"report_yes\">YES</span>";
                        }
                        else
                        {
                            $incident_verified = "<span class=\"report_no\">NO</span>";
                        }
                    
                        echo "<div class=\"report_row1\">";
                        echo "	<div class=\"report_thumb report_col1\">";
                        echo "    	&nbsp;";
                        if(isset($media_icons[$incident_id])){
                        	echo $media_icons[$incident_id];
                        }
                        echo "    </div>";
                        echo "    <div class=\"report_details report_col2\">";
                        echo "    	<h3><a href=\"" . url::base() . "reports/view/" . $incident_id . "\">" . $incident_title . "</a></h3>";
                        echo $incident_description;
                        echo "  	</div>";
                        echo "    <div class=\"report_date report_col3\">";
                        echo $incident_date;
                        echo "    </div>";
                        echo "    <div class=\"report_location report_col4\">";
                        echo $incident_location;
                        echo "    </div>";
                        echo "    <div class=\"report_status report_col5\">";
                        echo $incident_verified;
                        echo "    </div>";
                        echo "</div>";
                    }
                ?>
				<?php echo $pagination; ?>
				</div>
			</div>
		</div>
		<!-- end reports block -->
	</div>
</div>
