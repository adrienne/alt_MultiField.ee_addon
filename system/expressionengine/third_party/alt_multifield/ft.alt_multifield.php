<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

// get the version info from config
require_once PATH_THIRD . 'alt_multifield/alt_multifield_config.php';

/**
 * ALT MultiField Class
 *
 * @author    Adrienne L. Travis
 * @copyright Copyright (c) 2011 Adrienne L. Travis
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 *
 * Thanks to Eli Van Zoeren; I shamelessly cribbed the idea and bits of the original code from his
 * VZ Address fieldtype, located here: https://github.com/elivz/vz_address.ee_addon/
 *
 */

class Alt_multifield_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => ALT_MULTIFIELD_NAME,
        'version'   => ALT_MULTIFIELD_VER
	);

    public $has_array_data = TRUE;
	public $settings_exist = 'y';
    public $settings = array();


	/**
	 * Fieldtype Constructor
	 */
	public function __construct()
	{
        parent::__construct();
        
        $this->EE->load->library('javascript');
        $this->EE->load->library('typography');
        if (!function_exists('json_decode')) {
			$this->load->library('Services_json');
            }
        

        // Create cache
        if (!isset($this->EE->session->cache[__CLASS__])) {
            $this->EE->session->cache[__CLASS__] = array('css_and_js' => FALSE,);
			}
        $this->cache =& $this->EE->session->cache[__CLASS__];

		} // end CONSTRUCTOR
	
	/**
	 * Include the CSS styles, but only once
	 */
	private function _include_css_and_js() {
        if ( !$this->cache['css_and_js'] ) {
			$styling = <<<EOS
			\n\n
			<style type="text/css">
				.alt-multifield-wrapper,
				.alt-multifield-wrapper li {
					margin: 0; padding: 0;
					list-style-type: none;
					}
                .alt-multifield {
					padding-bottom: 0.5em;
					}
                .alt-multifield label {
					display:block;
					}
                .alt-multifield input {
					width:96.5%;
					padding:4px;
					}
				.alt-multifield-meta_keywords-field,
				.alt-multifield-meta_description-field {
					float: left;
					width: 50%;
					}
                .alt-multifield textarea {
					width: 95%;
					}
            </style>\n\n
EOS;
// DO NOT INDENT THE ABOVE LINE!!!

			$scripting = <<<EOJ
			\n\n
			<script type="text/javascript">
				$(document).ready(function() {
					$(".alt-multifield-input-type-date").datepicker( { dateFormat: $.datepicker.W3C + EE.date_obj_time } );
					});
			</script>\n\n
EOJ;
// DO NOT INDENT THE ABOVE LINE!!!
		
            $this->EE->cp->add_to_head($styling);
            $this->EE->cp->add_to_foot($scripting);
        	
        	$this->cache['css_and_js'] = TRUE;
			}
		} // end private function _include_css_and_js()
	
	/**
	 * Display Field Settings
	 */
	function display_settings($data) {
		// load the language file
		$this->EE->lang->loadfile('alt_multifield');
		$this->EE->table->add_row(
			lang('alt_multifield_options', 'alt_multifield_options') . '<br />'
			. lang('alt_option_setting_examples'),
			'<textarea id="alt_multifield_options" name="alt_multifield_options" rows="12">'.$this->_options_setting($data).'</textarea>'
			);
		$this->EE->table->add_row(
			lang('alt_multifield_styles', 'alt_multifield_styles') . '<br />'
			. lang('alt_multifield_styles_examples'),
			'<textarea id="alt_multifield_styles" name="alt_multifield_styles" rows="24">'.$this->_styles_setting($data).'</textarea>'
			);
		} // end function display_settings($data)
	
	/**
	 * Options Setting Value
	 */
	private function _options_setting($settings) {
		$r = '';

		if (isset($settings['options'])) {
			foreach($settings['options'] as $name => $stuff) {
				if ($r !== '') $r .= "\n";
				$r .= $name;
				$r .= ' : '.$stuff['label'];
				$r .= ' : '.$stuff['type'];
				}
			}

		return $r;
		} // end private function _options_setting($settings)
	
	/**
	 * Styles Setting Value
	 */
	private function _styles_setting($settings) {
		$r = '';

		if (isset($settings['styles'])) {
			foreach($settings['styles'] as $key => $value) {
				$r .= $value;
				}
			}

		return $r;
		} // end private function _styles_setting($settings)
	
	/**
	 * Save Field Settings
	 */
	function save_settings($data) {
		$options = $this->EE->input->post('alt_multifield_options');
		$styles = $this->EE->input->post('alt_multifield_styles');

		return $this->_save_settings($options,$styles);
		} // end function save_settings($data)

	/**
	 * Save Settings
	 */
	private function _save_settings($options = '',$styles = '') {
		$r = array('options' => array(),'styles' => array());

		$options = preg_split('/[\r\n]+/', trim($options));
		foreach($options as &$option) {
            $option = trim($option);
			$option_parts = preg_split('/\s:\s/', $option, 3);
			$option_name  = (string) trim($option_parts[0]);
			$option_label = (string) trim($option_parts[1]);
			$option_type = ( isset($option_parts[2]) && (preg_match('/^(textarea|text|tel|email|url|number|date)$/i',trim($option_parts[2])) > 0) )
							? (string) trim($option_parts[2])
							: 'text';

			$r['options'][$option_name] = array('label' => $option_label, 'type' => $option_type);
			}
		
		$styles = preg_split('/\}/', $styles);
		array_pop($styles);
		foreach($styles as $key => $style) {
			if($style != '') {
				$r['styles'][$key] = $style.'}';
				}
			}

		return $r;
		} // end private function _save_settings($options = '',$styles = '')


	// --------------------------------------------------------------------
	
	
	/**
     * Generate the publish page UI
     */
    private function _multi_form($name, $data, $is_cell=FALSE) {
		$this->EE->load->helper('form');
		$this->EE->lang->loadfile('alt_multifield');
		
        $this->_include_css_and_js();
		
        $form = "\n<ol class=\"alt-multifield-wrapper\" id=\"alt-multifield-$name\">";
		$styleblock = "\n\n".'<style type="text/css">';
        $fields = $this->settings['options'];
        $styles = $this->settings['styles'];
		
		// loop through styles and create block
		foreach($styles as $key => $stylevalue) {
			$styleblock .= "#alt-multifield-$name ";
			$styleblock .= $stylevalue;
			$styleblock .= "\n";
			}

        // Set default values
         if (!is_array($data)) {
            $data = (isset($data)) ? $this->pre_process($data) : array();
            }
            
        foreach($fields as $field => $stuff) {
            $form .= '<li class="alt-multifield alt-multifield-'.$field.($is_cell ? '-cell' : '-field').' alt-multifield-box-type-'.$stuff['type'].'">';
            $form .= "\n".form_label($stuff['label'])."\n";
			
			// store field data into an array so it's less ugly in the output tags below
            $mydata = array(
				'name' => $name.'['.$field.']',
				'value' => isset($data[$field]) ? $data[$field] : '',
				'id' => $name."-".$field,
				'class' => "alt-multifield-".$field." alt-multifield-input-type-".$stuff['type'],
				'type' => $stuff['type'],
				);
				
            if ($stuff['type'] == 'textarea') {
				// get rid of 'type' element of array
				array_pop($mydata);
				// add rows & columns
				$mydata['rows'] = '3';
				$mydata['cols'] = '30';
                $form .= form_textarea($mydata);
				$form .= "\n";
				}
            else {
				if ($stuff['type'] == 'date') {
					// get rid of 'type' element of array
					array_pop($mydata);
					}
				$form .= form_input($mydata);
				$form .= "\n";
				}
            $form .= "</li>\n";
			}

		$form .= "</ol>";
		$styleblock .= "</style>\n\n";
		
		$this->EE->cp->add_to_head($styleblock);
		
        return $form;
		} // end private function _multi_form($name, $data, $is_cell=FALSE)

    /**
     * Display Field
     */
    function display_field($field_data) {
        return $this->_multi_form($this->field_name, $field_data);
		} // end function display_field($field_data)
	
	// --------------------------------------------------------------------

    /**
     * Save Field
     */
    function save($data)
    {
    	return $this->EE->javascript->generate_json($data, TRUE);
    }

    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return $this->EE->javascript->generate_json($data, TRUE);
    }

	
	// --------------------------------------------------------------------
	
	/*
	 * Pre-parse to decode from JSON
	 */
    function pre_process($data) {
        return json_decode(htmlspecialchars_decode($data),true);
        } // end pre_process($data)

    /**
     * Display Tag
     */
	
    function replace_tag($multifielddata, $params=array(), $tagdata=FALSE) {
        // Variables
        $output = "";
        $fieldsettings = $this->settings['options'];
        
        // if we have data at all
        if(is_array($multifielddata)) {
        
            // Get parameters
            $mystyle = isset($params['style']) ? $params['style'] : 'table';
            $mymainclass = isset($params['main_class']) ? "multiblock ".$params['main_class'] : "multiblock";
            $myclasses = isset($params['subfield_classes']) ? explode('|',$params['subfield_classes']) : explode('|',"multifield");
            $show_empty = isset($params['show_empty']) ? $params['show_empty'] : 'no';
            $include_wrapper = isset($params['include_wrapper']) ? $params['include_wrapper'] : 'yes';

            // Merge in the labels
            $fieldoutputdata = array();
            foreach($multifielddata as $key=>$row) {
                if(isset($fieldsettings[$key])) { // checks that key still exists in settings
                    if('textarea' == $fieldsettings[$key]['type']) { // if it's a textarea, run it through the typography class
                        $fieldoutputdata[$key] = $this->EE->typography->auto_typography($row,TRUE);
                        }
                    else {
                        $fieldoutputdata[$key] = $row;
                        }
                    $klabel = $key.":label";
                    $ktype = $key.":type";
                    $fieldoutputdata[$klabel] = $fieldsettings[$key]['label'];
                    $fieldoutputdata[$ktype] = $fieldsettings[$key]['type'];
                    }
                }
            
            // Parse the tag
            if (!$tagdata) { // Single tag
                $myclasscopy = $myclasses;
                $output .= ($include_wrapper == "yes") ? "<$mystyle class=\"$mymainclass\">" : "";
                foreach($multifielddata as $key=>$row) {
                    if(isset($fieldsettings[$key]) && ($row != "" || $show_empty == 'yes')) { 	
                    // checks that key still exists in settings, AND that row is not empty (unless show_empty param is yes)
                        $mylabel = $fieldoutputdata[$key.':label'];
                        $mytype = $fieldoutputdata[$key.':type'];
                        $myvalue = $fieldoutputdata[$key];
                        $myclass = array_shift($myclasscopy); // take OFF top class of classes array to use
                        $output .= $this->_make_something($mystyle,$mylabel,$myvalue,$myclass);
                        $myclasscopy[] = $myclass; // put used class back at END of classes array
                        }
                    }
                $output .= ($include_wrapper == "yes") ? "</$mystyle>" : "";
                }
            else { // Tag pair
            
                // Replace the variables
                $output = $this->EE->TMPL->parse_variables($tagdata, array($fieldoutputdata)); 
                
                }
                
            } 
        return $output;
		} // end function replace_tag($multifielddata, $params=array(), $tagdata=FALSE)

	
	private function _make_something($thing,$thinglabel,$thingvalue,$thingclass='',$thingtype='text') {
		$returned = "";
		switch ($thing) {
                case 'table' :
					$returned .= "\n<tr class=\"$thingclass\">\n";
					$returned .= "<th scope=\"row\" class=\"label\">$thinglabel</th>\n";
					$returned .= "<td class=\"value\">$thingvalue</td>\n";
					$returned .= "</tr>\n";
					break;
				case 'dl' :
                    $returned .= "\n";
					$returned .= "<dt class=\"$thingclass label\">$thinglabel</dt>\n";
					$returned .= "<dd class=\"$thingclass value\">$thingvalue</dd>\n";
					break;
				default :
                    break;
				}
	
		return $returned;
		} // end private function _make_someting($item,$rowclass)
	
    
    
}

/* End of file ft.alt_multifield.php */