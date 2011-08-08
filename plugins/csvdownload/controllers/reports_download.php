<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Reports Controller.
 * This controller will take care of adding and editing reports in the Admin section.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Reports Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Reports_Download_Controller extends Main_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->template->this_page = 'reports';
    }


    /**
    * Download Reports in CSV format
    */

    function index($page = 1)
    {

		$this->template->header->header_block = $this->themes->header_block();
		$this->template->header->this_page ='reports_download';
        $this->template->content = new View('reports_download');
        $this->template->content->title = Kohana::lang('ui_admin.download_reports');

        $form = array(
            'data_point'   => '',
            'data_include' => '',
            'from_date'    => '',
            'to_date'      => ''
        );
        
        $errors = $form;
        $form_error = FALSE;
        $split_categories = FALSE;

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {

            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
            $post = Validation::factory($_POST);

             //  Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('data_point.*','required','numeric','between[1,4]');
            $post->add_rules('data_include.*','numeric','between[1,6]');
            $post->add_rules('from_date','date_mmddyyyy');
            $post->add_rules('to_date','date_mmddyyyy');

            // Validate the report dates, if included in report filter
            if (!empty($_POST['from_date']) || !empty($_POST['to_date']))
            {
                // Valid FROM Date?
                if (empty($_POST['from_date']) || (strtotime($_POST['from_date']) > strtotime("today"))) {
                    $post->add_error('from_date','range');
                }

                // Valid TO date?
                if (empty($_POST['to_date']) || (strtotime($_POST['to_date']) > strtotime("today"))) {
                    $post->add_error('to_date','range');
                }

                // TO Date not greater than FROM Date?
                if (strtotime($_POST['from_date']) > strtotime($_POST['to_date'])) {
                    $post->add_error('to_date','range_greater');
                }
            }

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                // Add Filters
                $filter = " ( 1=1 AND incident_active = 1";
                // Report Type Filter
                foreach($post->data_point as $item)
                {
                    if ($item == 1) {
                    //    $filter .= " OR incident_active = 1 ";
                    }
                    if ($item == 2) {
                        $filter .= " OR incident_verified = 1 ";
                    }
                    if ($item == 3) {
                        //$filter .= " OR incident_active = 0 ";
                    }
                    if ($item == 4) {
                        $filter .= " OR incident_verified = 0 ";
                    }

                }
                $filter .= ") ";

                // Report Date Filter
                if (!empty($post->from_date) && !empty($post->to_date))
                {
                    $filter .= " AND ( incident_date >= '" . date("Y-m-d H:i:s",strtotime($post->from_date)) . "' AND incident_date <= '" . date("Y-m-d H:i:s",strtotime($post->to_date)) . "' ) ";
                }

                // Retrieve reports
                $incidents = ORM::factory('incident')->where($filter)->orderby('incident_dateadd', 'desc')->find_all();

                // Column Titles
                $report_csv = "#,INCIDENT TITLE,INCIDENT DATE";
                $category_counter = 3;
                $category_location = 0;
                foreach($post->data_include as $item)
                {
                    if ($item == 1) {
                        $report_csv .= ",LOCATION";
                        $category_counter++;
                    }
                    
                    if ($item == 2) {
                        $report_csv .= ",DESCRIPTION";
                        $category_counter++;
                    }
                    
                    if ($item == 3) {
                        $report_csv .= ",CATEGORY";
                        $category_location = $category_counter;
                    }
                    
                    if ($item == 4) {
                        $report_csv .= ",LATITUDE";
                        $category_counter++;
                    }
                    
                    if($item == 5) {
                        $report_csv .= ",LONGITUDE";
                        $category_counter++;
                    }

                    if ($item == 6){
                        $split_categories = TRUE;  
                    }

                }
                $report_csv .= ",APPROVED,VERIFIED";
                $report_csv .= "\n";
               
                foreach ($incidents as $incident)
                {
                    $new_report = array();
                    array_push($new_report,'"'.$incident->id.'"');
                    array_push($new_report,'"'.$this->_csv_text($incident->incident_title).'"');
                    array_push($new_report,'"'.$incident->incident_date.'"');

                    foreach($post->data_include as $item)
                    {
                        switch ($item)
                        {
                            case 1:
                                array_push($new_report,'"'.$this->_csv_text($incident->location->location_name).'"');
                            break;

                            case 2:
                                array_push($new_report,'"'.$this->_csv_text($incident->incident_description).'"');
                            break;

                            case 3:
                                $catstring = '"';
                                $catcnt = 0;        
                                foreach($incident->incident_category as $category)
                                {
                                    if ($catcnt > 0){
                                       $catstring .= ",";
                                    }
                                    if ($category->category->category_title)
                                    {
                                        $catstring .= $this->_csv_text($category->category->category_title);
                                    }
                                    $catcnt++;
                                }
                                $catstring .= '"';
                                array_push($new_report,$catstring);
                            break;
                        
                            case 4:
                                array_push($new_report,'"'.$this->_csv_text($incident->location->latitude).'"');
                            break;
                        
                            case 5:
                                array_push($new_report,'"'.$this->_csv_text($incident->location->longitude).'"');
                            break;
                        }
                    }
                    
                    if ($incident->incident_active)
                    {
                        array_push($new_report,"YES");
                    }
                    else
                    {
                        array_push($new_report,"NO");
                    }
                    
                    if ($incident->incident_verified)
                    {
                        array_push($new_report,"YES");
                    }
                    else
                    {
                        array_push($new_report,"NO");
                    }
                    
                    array_push($new_report,"\n");
                    $catsplit = explode(',',trim($new_report[$category_location],'"'));
                    //$catsplit = explode(',',$new_report[$category_location]);
                    if ($split_categories && count($catsplit) > 1){
                    //die ("ound multi categories:  $new_report[$category_location] - $new_report[0] " . print_r($new_report));
                    //die ("ound multi categories:  $new_report[$category_location] - $new_report[0] ");
                                foreach($catsplit as $cat){
                                       $new = $new_report;
                                       $new[$category_location] = '"' . $cat .'"';
                                       $repcnt = 0;
                                       foreach($new as $col){
                                                if ($repcnt > 0){
                                                        $report_csv .= ",";
                                                }
                                                $report_csv .= $col;
                                                $repcnt++;
                                       }

                        }
                    }else{
                        $repcnt = 0;
                        foreach ($new_report as $column){
                             if ($repcnt > 0){
                                $report_csv .= ",";
                             }
                             $report_csv .= $column;
                             $repcnt++;
                         }
                    }
                }

                $happy_date = date("Y-m-d.H.i.s", time());
                $host =  parse_url(url::site(), PHP_URL_HOST);
                // Output to browser
                header("Content-type: text/x-csv");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                //header("Content-Disposition: attachment; filename=" . time() . ".csv");
                header("Content-Disposition: attachment; filename=" . $host . "." . $happy_date . ".csv");
                header("Content-Length: " . strlen($report_csv));
                echo $report_csv;
                exit;

            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('report'));
                $form_error = TRUE;
            }
        }

        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;

        // Javascript Header
        $this->template->js = new View('reports_download_js');
        $this->template->js->calendar_img = url::base() . "media/img/icon-calendar.gif";
    }


    /* private functions */

    // Return thumbnail photos
    //XXX: This needs to be fixed, it's probably ok to return an empty iterable instead of "0"
    private function _get_thumbnails( $id )
    {
        $incident = ORM::factory('incident', $id);

        if ( $id )
        {
            $incident = ORM::factory('incident', $id);

            return $incident;

        }
        return "0";
    }

    private function _get_categories()
    {
        $categories = ORM::factory('category')
            ->where('category_visible', '1')
            ->where('parent_id', '0')
			->where('category_trusted != 1')
            ->orderby('category_title', 'ASC')
            ->find_all();

        return $categories;
    }

    // Dynamic categories form fields
    private function _new_categories_form_arr()
    {
        return array
        (
            'category_name' => '',
            'category_description' => '',
            'category_color' => '',
        );
    }

    // Time functions
    private function _hour_array()
    {
        for ($i=1; $i <= 12 ; $i++)
        {
            $hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);     // Add Leading Zero
        }
        return $hour_array;
    }

    private function _minute_array()
    {
        for ($j=0; $j <= 59 ; $j++)
        {
            $minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);   // Add Leading Zero
        }

        return $minute_array;
    }

    private function _ampm_array()
    {
        return $ampm_array = array('pm'=>Kohana::lang('ui_admin.pm'),'am'=>Kohana::lang('ui_admin.am'));
    }

    // Javascript functions
     private function _color_picker_js()
    {
     return "<script type=\"text/javascript\">
                $(document).ready(function() {
                $('#category_color').ColorPicker({
                        onSubmit: function(hsb, hex, rgb) {
                            $('#category_color').val(hex);
                        },
                        onChange: function(hsb, hex, rgb) {
                            $('#category_color').val(hex);
                        },
                        onBeforeShow: function () {
                            $(this).ColorPickerSetColor(this.value);
                        }
                    })
                .bind('keyup', function(){
                    $(this).ColorPickerSetColor(this.value);
                });
                });
            </script>";
    }

    private function _date_picker_js()
    {
        return "<script type=\"text/javascript\">
                $(document).ready(function() {
                $(\"#incident_date\").datepicker({
                showOn: \"both\",
                buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\",
                buttonImageOnly: true
                });
                });
            </script>";
    }


    private function _new_category_toggle_js()
    {
        return "<script type=\"text/javascript\">
                $(document).ready(function() {
                $('a#category_toggle').click(function() {
                $('#category_add').toggle(400);
                return false;
                });
                });
            </script>";
    }


    /**
     * Checks if translation for this report & locale exists
     * @param Validation $post $_POST variable with validation rules
     * @param int $iid The unique incident_id of the original report
     */
    public function translate_exists_chk(Validation $post)
    {
        // If add->rules validation found any errors, get me out of here!
        if (array_key_exists('locale', $post->errors()))
            return;

        $iid = $_GET['iid'];
        if (empty($iid)) {
            $iid = 0;
        }
        $translate = ORM::factory('incident_lang')->where('incident_id',$iid)->where('locale',$post->locale)->find();
        if ($translate->loaded == true) {
            $post->add_error( 'locale', 'exists');
        // Not found
        } else {
            return;
        }
    }


    /**
     * Retrieve Custom Form Fields
     * @param bool|int $incident_id The unique incident_id of the original report
     * @param int $form_id The unique form_id. Uses default form (1), if none selected
     * @param bool $field_names_only Whether or not to include just fields names, or field names + data
     * @param bool $data_only Whether or not to include just data
     */
    private function _get_custom_form_fields($incident_id = false, $form_id = 1, $data_only = false)
    {
        $fields_array = array();

        if (!$form_id)
        {
            $form_id = 1;
        }
        $custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');
        foreach ($custom_form->form_field as $custom_formfield)
        {
            if ($data_only)
            { // Return Data Only
                $fields_array[$custom_formfield->id] = '';

                foreach ($custom_formfield->form_response as $form_response)
                {
                    if ($form_response->incident_id == $incident_id)
                    {
                        $fields_array[$custom_formfield->id] = $form_response->form_response;
                    }
                }
            }
            else
            { // Return Field Structure
                $fields_array[$custom_formfield->id] = array(
                    'field_id' => $custom_formfield->id,
                    'field_name' => $custom_formfield->field_name,
                    'field_type' => $custom_formfield->field_type,
                    'field_required' => $custom_formfield->field_required,
                    'field_maxlength' => $custom_formfield->field_maxlength,
                    'field_height' => $custom_formfield->field_height,
                    'field_width' => $custom_formfield->field_width,
                    'field_isdate' => $custom_formfield->field_isdate,
                    'field_response' => ''
                    );
            }
        }

        return $fields_array;
    }


    /**
     * Validate Custom Form Fields
     * @param array $custom_fields Array
     */
    private function _validate_custom_form_fields($custom_fields = array())
    {
        $custom_fields_error = "";

        foreach ($custom_fields as $field_id => $field_response)
        {
            // Get the parameters for this field
            $field_param = ORM::factory('form_field', $field_id);
            if ($field_param->loaded == true)
            {
                // Validate for required
                if ($field_param->field_required == 1 && $field_response == "")
                {
                    return false;
                }

                // Validate for date
                if ($field_param->field_isdate == 1 && $field_response != "")
                {
                    $myvalid = new Valid();
                    return $myvalid->date_mmddyyyy($field_response);
                }
            }
        }
        return true;
    }


    /**
     * Ajax call to update Incident Reporting Form
     */
    public function switch_form()
    {
        $this->template = "";
        $this->auto_render = FALSE;

        isset($_POST['form_id']) ? $form_id = $_POST['form_id'] : $form_id = "1";
        isset($_POST['incident_id']) ? $incident_id = $_POST['incident_id'] : $incident_id = "";

        $html = "";
        $fields_array = array();
        $custom_form = ORM::factory('form', $form_id)->orderby('field_position','asc');

        foreach ($custom_form->form_field as $custom_formfield)
        {
            $fields_array[$custom_formfield->id] = array(
                'field_id' => $custom_formfield->id,
                'field_name' => $custom_formfield->field_name,
                'field_type' => $custom_formfield->field_type,
                'field_required' => $custom_formfield->field_required,
                'field_maxlength' => $custom_formfield->field_maxlength,
                'field_height' => $custom_formfield->field_height,
                'field_width' => $custom_formfield->field_width,
                'field_isdate' => $custom_formfield->field_isdate,
                'field_response' => ''
                );

            // Load Data, if Any
            foreach ($custom_formfield->form_response as $form_response)
            {
                if ($form_response->incident_id = $incident_id)
                {
                    $fields_array[$custom_formfield->id]['field_response'] = $form_response->form_response;
                }
            }
        }

        foreach ($fields_array as $field_property)
        {
            $html .= "<div class=\"row\">";
            $html .= "<h4>" . $field_property['field_name'] . "</h4>";
            if ($field_property['field_type'] == 1)
            { // Text Field
                // Is this a date field?
                if ($field_property['field_isdate'] == 1)
                {
                    $html .= form::input('custom_field['.$field_property['field_id'].']', $field_property['field_response'],
                        ' id="custom_field_'.$field_property['field_id'].'" class="text"');
                    $html .= "<script type=\"text/javascript\">
                            $(document).ready(function() {
                            $(\"#custom_field_".$field_property['field_id']."\").datepicker({
                            showOn: \"both\",
                            buttonImage: \"" . url::base() . "media/img/icon-calendar.gif\",
                            buttonImageOnly: true
                            });
                            });
                        </script>";
                }
                else
                {
                    $html .= form::input('custom_field['.$field_property['field_id'].']', $field_property['field_response'],
                        ' id="custom_field_'.$field_property['field_id'].'" class="text custom_text"');
                }
            }
            elseif ($field_property['field_type'] == 2)
            { // TextArea Field
                $html .= form::textarea('custom_field['.$field_property['field_id'].']',
                    $field_property['field_response'], ' class="custom_text" rows="3"');
            }
            $html .= "</div>";
        }

        echo json_encode(array("status"=>"success", "response"=>$html));
    }

    /**
     * Creates a SQL string from search keywords
     */
    private function _get_searchstring($keyword_raw)
    {
        $or = '';
        $where_string = '';


        // Stop words that we won't search for
        // Add words as needed!!
        $stop_words = array('the', 'and', 'a', 'to', 'of', 'in', 'i', 'is', 'that', 'it',
        'on', 'you', 'this', 'for', 'but', 'with', 'are', 'have', 'be',
        'at', 'or', 'as', 'was', 'so', 'if', 'out', 'not');

        $keywords = explode(' ', $keyword_raw);
        
        if (is_array($keywords) && !empty($keywords))
        {
            array_change_key_case($keywords, CASE_LOWER);
            $i = 0;
            
            foreach($keywords as $value)
            {
                if (!in_array($value,$stop_words) && !empty($value))
                {
                    $chunk = mysql_real_escape_string($value);
                    if ($i > 0) {
                        $or = ' OR ';
                    }
                    $where_string = $where_string.$or."incident_title LIKE '%$chunk%' OR incident_description LIKE '%$chunk%'  OR location_name LIKE '%$chunk%'";
                    $i++;
                }
            }
        }

        if ($where_string)
        {
            return $where_string;
        }
        else
        {
            return "1=1";
        }
    }

    private function _csv_text($text)
    {
        $text = stripslashes(htmlspecialchars($text));
        return $text;
    }
}
