<?php

/**
 * Forked from Elliot Condon's radio field
 * License: GPL
 */
class RadioWithOther extends acf_Field
{

	function __construct($parent)
	{
		parent::__construct($parent);

		$this->name = 'radiowithother';
		$this->title = __('Radio With Other','radio-with-other');

	}

	function create_field($field)
	{
		// defaults
		$field['layout'] = isset($field['layout']) ? $field['layout'] : 'vertical';
		$field['choices'] = isset($field['choices']) ? $field['choices'] : array();

		// no choices
		if(empty($field['choices']))
		{
			echo '<p>' . __("No choices to choose from",'acf') . '</p>';
			return false;
		}

		echo '<ul class="radio_list ' . $field['class'] . '" id="' . $field['key'] . '" ' . $field['layout'] . '">';

		$i = 0;
		$other = 'checked="checked" data-checked="checked"';
		foreach($field['choices'] as $key => $value)
		{
			$i++;

			$selected = '';

			if($key == $field['value']['radio'] || $key == $field['value']['text'])
			{
				$selected = 'checked="checked" data-checked="checked"';
				$other = '';
			}

			echo '<li><label><input type="radio" name="' . $field['name'] . '[radio]" class="' . $field['key'] . '" value="' . $key . '" ' . $selected . ' />' . $value . '</label></li>';

		}
		if ( '' != $other && '' != $field['value']['text']) {
			$otherval = esc_attr($field['value']['text']);
		} else {
			$otherval = '';
		}
		$otherattr = 'class="othertext" ';
		$otherlabel = apply_filters('radio_other_otherlabel', __('other','radio-with-other'));
		$otherliformat = '<li><label><input type="radio" name="%1$s[radio]" class="%1$s" value="%2$s" id="otherradio%1$s" %3$s />%4$s</label><input %5$s type="text" name="%1$s[text]" id="othertext%1$s" value="%2$s">';
		echo sprintf($otherliformat,$field['name'],$otherval,$other,$otherlabel,$otherattr);

		if (current_user_can(apply_filters('radio_other_addlabel_privilege', 'edit_plugins'))) {
			$addlabel = apply_filters('radio_other_addlabel', __('Add this answer to the choices.','radio-with-other'));
			$addlabelformat = '<label><input type="checkbox" name="%1$s[othercheck]">%2$s</label>';
			echo sprintf($addlabelformat,$field['name'],$addlabel);
		}
		echo '</li>';

		echo '</ul>';
	}

	function create_options($key, $field)
	{	
		// defaults
		$field['layout'] = isset($field['layout']) ? $field['layout'] : 'vertical';
		$field['default_value'] = isset($field['default_value']) ? $field['default_value'] : '';


		// implode checkboxes so they work in a textarea
		if(isset($field['choices']) && is_array($field['choices']))
		{		
			foreach($field['choices'] as $choice_key => $choice_val)
			{
				$field['choices'][$choice_key] = $choice_key.' : '.$choice_val;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		else
		{
			$field['choices'] = "";
		}

?>


	<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
	<label for=""><?php _e("Choices",'acf'); ?></label>
	<p class="description"><?php _e("Enter your choices one per line",'acf'); ?><br />
	<br />
	<?php _e("Red",'acf'); ?><br />
	<?php _e("Blue",'acf'); ?><br />
	<br />
	<?php _e("red : Red",'acf'); ?><br />
	<?php _e("blue : Blue",'acf'); ?><br />
	</p>
	</td>
	<td>
	<textarea rows="5" name="fields[<?php echo $key; ?>][choices]" id=""><?php echo $field['choices']; ?></textarea>
	</td>
	</tr>
	<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
	<label><?php _e("Default Value",'acf'); ?></label>
	</td>
	<td>
<?php 
		$this->parent->create_field(array(
			'type'	=>	'text',
			'name'	=>	'fields['.$key.'][default_value]',
			'value'	=>	$field['default_value'],
		));
?>
	</td>
	</tr>
	<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
	<label for=""><?php _e("Layout",'acf'); ?></label>
	</td>
	<td>
<?php 
		$this->parent->create_field(array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][layout]',
			'value'	=>	$field['layout'],
			'layout' => 'horizontal', 
			'choices' => array(
				'vertical' => __("Vertical",'acf'), 
				'horizontal' => __("Horizontal",'acf')
			)
		));
?>
	</td>
	</tr>

<?php
	}

	function admin_print_styles()
	{
		$radio_other_admin_css = '<style type="text/css">.othertext {margin-left:10px!important;vertical-align:middle;width:160px!important;}</style>';
		echo apply_filters('radio_other_admin_css', $radio_other_admin_css);
	}

	static function add_choice($field, $value) {
		$add_array = array($value=>$value);
		$newarray = array_merge($field,$add_array);
		$field = $newarray;
		return $field;
	}
	static function add_other(&$value,$key,$fields) {
		if ($value['key'] == $fields['key']) {
			$value['choices'] = self::add_choice($value['choices'],$fields['addvalue']);
		}
	}
	static function add_choice_in_repeat(&$data,$value,$field_key) {
		$fields = array(
			'key' => $field_key,
			'addvalue' => $value,
		);

		array_walk($data['sub_fields'],array('self','add_other'),$fields);
		return $data;
	}

	static function text_to_radio($value,$oldvalues) {
		if ('' != trim($value['text']) && $oldvalues['text'] != trim($value['text'])) {
			$value['radio'] = $value['text'];
		}
		return $value;
	}

	function update_value($post_id, $field, $value)
	{
		// strip slashes
		$value = stripslashes_deep($value);

		// Copy Other text to the radio choice
		// other is not empty, and other is not same as old other
		if (!  $oldvalues = $this->get_value( $post_id, $field )) {
			$oldvalues = array('text'=>'');
		}
		$value = self::text_to_radio($value,$oldvalues);

		// update for choices
		if ('' != $value['othercheck']) {
			$field['choices'] = self::add_choice($field['choices'],$value['text']);
			if ($base_post_id = Acf::get_post_meta_post_id($field['key'])) {
				// not in repeater
				update_post_meta($base_post_id, $field['key'], $field);
			} else {
				$pos = strpos($field['name'],'_');
				$data = substr($field['name'],0,$pos);
				$key =get_post_meta($post_id,'_'.$data,'single');
				$base_post_id = Acf::get_post_meta_post_id($key);
				$array = get_post_meta($base_post_id,$key,'single');
				update_post_meta($post_id, 'test', $array);
				// in repeater
				$newfielddata = self::add_choice_in_repeat($array,$value['text'],$field['key']);
				update_post_meta($base_post_id, $key, $newfielddata);
			}
		wp_cache_delete('acf_get_field_' . $post_id . '_' . $field['name']);
		}

		// if $post_id is a string, then it is used in the everything fields and can be found in the options table
		if( is_numeric($post_id) )
		{
			update_post_meta($post_id, $field['name'], $value);
			update_post_meta($post_id, '_' . $field['name'], $field['key']);
		}
		else
		{
			update_option( $post_id . '_' . $field['name'], $value );
			update_option( '_' . $post_id . '_' . $field['name'], $field['key'] );
		}


		wp_cache_delete('acf_get_field_' . $post_id . '_' . $field['name']);

	}


	function pre_save_field($field)
	{
		// defaults
		$field['choices'] = isset($field['choices']) ? $field['choices'] : '';

		// vars
		$new_choices = array();

		// explode choices from each line
		if(strpos($field['choices'], "\n") !== false)
		{
			// found multiple lines, explode it
			$field['choices'] = explode("\n", $field['choices']);
		}
		else
		{
			// no multiple lines! 
			$field['choices'] = array($field['choices']);
		}

		// key => value
		foreach($field['choices'] as $choice)
		{
			if(strpos($choice, ' : ') !== false)
			{
				$choice = explode(' : ', $choice);
				$new_choices[trim($choice[0])] = trim($choice[1]);
			}
			else
			{
				$new_choices[trim($choice)] = trim($choice);
			}
		}
		// update choices
		$field['choices'] = $new_choices;

		// return updated field
		return $field;

	}

	function get_value_for_api($post_id, $field)
	{
			$values = $this->get_value( $post_id, $field );
			$output = '';
			if ('' != $values['radio'] ) 
			{
				$output = $values['radio'];
			} 
			else if ('' != $values['text'] ) 
			{
				$output = $values['text'];
			} 
			return $output;
	}
}

