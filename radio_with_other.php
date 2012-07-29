<?php

/**
 * RadioWithOther 
 * 
 * fork from advanced-custom-field/core/fields/radio.php
 * http://www.advancedcustomfields.com/
 * @copyright 2012 Fumito MIZUNO
 * @license GPL ver.2 or later
 */
class RadioWithOther extends acf_Field
{
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'radiowithother';
		$this->title = __('Radio With Other','acf');
		
   	}
   	
   		

	function create_field($field)
	{
		// defaults
		$field['layout'] = isset($field['layout']) ? $field['layout'] : 'vertical';
		$field['choices'] = isset($field['choices']) ? $field['choices'] : array();
		
		
		echo '<ul class="radio_list ' . $field['class'] . ' ' . $field['layout'] . '">';
		
		$i = 0;
		$other = 'checked="checked" data-checked="checked"';
		foreach($field['choices'] as $key => $value)
		{
			$i++;
			
			// if there is no value and this is the first of the choices and there is no "0" choice, select this on by default
			// the 0 choice would normally match a no value. This needs to remain possible for the create new field to work.
			if(!$field['value'] && $i == 1 && !isset($field['choices']['0']))
			{
				$field['value'] = $key;
			}
			
			$selected = '';
			
			if($key == $field['value'])
			{
				$selected = 'checked="checked" data-checked="checked"';
				$other = '';
			}
			
			echo '<li><label><input type="radio" name="' . $field['name'] . '" value="' . $key . '" ' . $selected . ' />' . $value . '</label></li>';
		}
		if ( '' != $other ) {
			$otherval = esc_attr($field['value']);
		} else {
			$otherval = '';
		}
		echo '<li><label><input type="radio" name="' . $field['name'] . '" value="' . $otherval . '" id="otherradio" ' . $other . ' />' . __('other','acf') . '</label><input type="text" id="othertext" value="'.$otherval.'"></li>';
		
		echo '</ul>';
?><script type="text/javascript">
		jQuery(function() {
			jQuery('#othertext').blur(function(){
				var value = jQuery(this).val();
				jQuery('#otherradio').val(value);
			});
		});
		</script>	
<?php
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
?>
<style type="text/css">
#othertext {
	margin-left : 60px;
	width:160px;
}
</style>
<?php	
	}
	
	function update_value($post_id, $field, $value)
	{
		// strip slashes
		$value = stripslashes_deep($value);
		
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
		
		$field2 = $field;
		$newarray = array('a'=>'b');
		$field2['choices'] = array_merge($field['choices'],$newarray);
		error_log(serialize($field2),0);
		//$base_post_id = Acf::get_post_meta_post_id($field['key']);
		//update_post_meta($post_id, $field['key'], $field2);
		//error_log(Acf::get_post_meta_post_id('field_50133163545a0'),0);
		//clear the cache for this field
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
}

?>
