<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (! defined('FIELDTYPE_VERSION'))
{
    // get the version from config.php
    require PATH_THIRD.'alt_multifield/alt_multifield_config.php';
    define('FIELDTYPE_VERSION', $config['version']);
    define('FIELDTYPE_NAME', $config['name']);
}

/**
 * ALT MultiField Class
 *
 * @author    Adrienne L. Travis
 * @copyright Copyright (c) 2011 Adrienne L. Travis
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 *
 * Credit for much of the code in this fieldtype goes to Eli Van Zoeren; I shamelessly cribbed from his
 * VZ Address fieldtype, located here: https://github.com/elivz/vz_address.ee_addon/
 *
 */
 
class Alt_multifield_ft extends EE_Fieldtype {

    public $info = array(
        'name'      => FIELDTYPE_NAME,
        'version'   => FIELDTYPE_VERSION,
    );
	    
    var $has_array_data = TRUE;
	var $settings_exist = 'y';
    var $settings = array();
    
  
	/**
	 * Fieldtype Constructor
	 */
	function Alt_multifield_ft()
	{
        parent::EE_Fieldtype();

        // Create cache
        if (! isset($this->EE->session->cache[__CLASS__]))
        {
            $this->EE->session->cache[__CLASS__] = array('css_and_js' => FALSE,);
        }
        $this->cache =& $this->EE->session->cache[__CLASS__];

	}
	
	/**
	 * Include the CSS styles, but only once
	 */
	private function _include_css_and_js()
	{
        if ( !$this->cache['css_and_js'] )
        {
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
    }
	
	/**
	 * Display Field Settings
	 */
	function display_settings($data)
	{
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
	}
	
	/**
	 * Options Setting Value
	 */
	private function _options_setting($settings)
	{
		$r = '';

		if (isset($settings['options']))
		{
			foreach($settings['options'] as $name => $stuff)
			{
				if ($r !== '') $r .= "\n";
				$r .= $name;
				$r .= ' : '.$stuff['label'];
				$r .= ' : '.$stuff['type'];
				if (isset($settings['default']) && $settings['default'] == $name) $r .= ' *';
			}
		}

		return $r;
	}
	
	/**
	 * Options Setting Value
	 */
	private function _styles_setting($settings)
	{
		$r = '';

		if (isset($settings['styles']))
		{
			foreach($settings['styles'] as $key => $value)
			{
				$r .= $value;
			}
		}

		return $r;
	}
	
	/**
	 * Save Field Settings
	 */
	function save_settings($data)
	{
		$options = $this->EE->input->post('alt_multifield_options');
		$styles = $this->EE->input->post('alt_multifield_styles');

		return $this->_save_settings($options,$styles);
	}

	/**
	 * Save Settings
	 */
	private function _save_settings($options = '',$styles = '')
	{
		$r = array('options' => array(),'styles' => array());

		$options = preg_split('/[\r\n]+/', $options);
		foreach($options as &$option) {
			// default?
			if ($default = (substr($option, -1) == '*')) $option = substr($option, 0, -1);

			$option_parts = preg_split('/\s:\s/', $option, 3);
			$option_name  = (string) trim($option_parts[0]);
			$option_label = (string) trim($option_parts[1]);
			$option_type = ( isset($option_parts[2]) && (preg_match('/^(textarea|text|tel|email|url|number|date)$/i',trim($option_parts[2])) > 0) )
							? (string) trim($option_parts[2]) 
							: 'text';

			$r['options'][$option_name] = array('label' => $option_label, 'type' => $option_type);
			if ($default) $r['default'] = $option_name;
			}
		
		$styles = preg_split('/\}/', $styles);
		array_pop($styles);
		foreach($styles as $key => $style) {
			if($style != '') {
				$r['styles'][$key] = $style.'}';
				}
			}

		return $r;
	}


	// --------------------------------------------------------------------
	
	
	/**
     * Generate the publish page UI
     */
    private function _multi_form($name, $data, $is_cell=FALSE)
    {
		$this->EE->load->helper('form');
		$this->EE->lang->loadfile('alt_multifield');
		
        $this->_include_css_and_js();
		
        $form = "\n<ol class=\"alt-multifield-wrapper\" id=\"alt-multifield-$name\">";
		$styleblock = '\n\n<style type="text/css">';
        $fields = $this->settings['options'];
        $styles = $this->settings['styles'];
		
		// loop through styles and create block
		foreach($styles as $key => $stylevalue) {
			$styleblock .= "#alt-multifield-$name ";
			$styleblock .= $stylevalue;
			$styleblock .= "\n";
			}
        
        // Set default values
        $data = unserialize(htmlspecialchars_decode($data));
        if (!is_array($data)) $data = array();

        foreach($fields as $field => $stuff)
        {
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
    }
    
    /**
     * Display Field
     */
    function display_field($field_data)
    {
        return $this->_multi_form($this->field_name, $field_data);
    }
	
	// --------------------------------------------------------------------
    
    /**
     * Save Field
     */
    function save($data)
    {
    	return serialize($data);
    }
    
    /**
     * Save Cell
     */
    function save_cell($data)
    {
        return serialize($data);
    }

	
	// --------------------------------------------------------------------
	
	/*
	 * Pre-parse to unserialize
	 */
    function pre_process($data)
    {
        return unserialize(htmlspecialchars_decode($data));
    }

    /**
     * Display Tag
     */
	
    function replace_tag($seodata, $params=array(), $tagdata=FALSE)
    {
        if (!$tagdata) // Single tag
        {
			$output = '';
    	}
    	else // Tag pair
    	{
            // Replace the variables            
            $output = $this->EE->TMPL->parse_variables($tagdata, array($seodata));
    	}
            
        return $output;
    } 

}

/* End of file ft.alt_multifield.php */