<?php
/**
 * Ui builder for taxonomy plugin
 *
 */
class WPG_Taxonomy_UI_Builder {

	/**
	 * Return an opening `<tr>` tag.
	 *
	 * @return string $value Opening `<tr>` tag with attributes.
	 */
	public function get_tr_start($args=NULL) {
	    $class = (isset($args) && isset($args['wrap_class']))?'class="'.$args['wrap_class'].'"':'';
	    return '<tr valign="top" '.$class.'>';
	}

	/**
	 * Return a closing `</tr>` tag.
	 *
	 * @return string $value Closing `</tr>` tag.
	 */
	public function get_tr_end() {
		return '</tr>';
	}

	/**
	 * Return an opening `<th>` tag.
	 *
	 * @return string $value Opening `<th>` tag with attributes.
	 */
	public function get_th_start() {
		return '<th scope="row">';
	}

	/**
	 * Return a closing `</th>` tag.
	 *
	 * @return string $value Closing `</th>` tag.
	 */
	public function get_th_end() {
		return '</th>';
	}

	/**
	 * Return an opening `<td>` tag.
	 *
	 * @return string $value Opening `<td>` tag.
	 */
	public function get_td_start() {
		return '<td>';
	}

	/**
	 * Return a closing `</td>` tag.
	 *
	 * @return string $value Closing `</td>` tag.
	 */
	public function get_td_end() {
		return '</td>';
	}

	/**
	 * Return an opening `<fieldset>` tag.
	 *
	 * @param array $args Array of arguments.
	 * @return string $value Opening `<fieldset>` tag.
	 */
	public function get_fieldset_start( $args = array() ) {
		$fieldset = '<fieldset';

		if ( ! empty( $args['id'] ) ) {
			$fieldset .= ' id="' . esc_attr( $args['id'] ) . '"';
		}

		if ( ! empty( $args['classes'] ) ) {
			$classes = 'class="' . implode( ' ', $args['classes'] ) . '"';
			$fieldset .= ' ' . $classes;
		}

		if ( ! empty( $args['aria-expanded'] ) ) {
			$fieldset .= ' aria-expanded="' . $args['aria-expanded'] . '"';
		}

		$fieldset .= ' tabindex="0">';

		return $fieldset;
	}

	/**
	 * Return an closing `<fieldset>` tag.
	 *
	 * @return string $value Closing `<fieldset>` tag.
	 */
	public function get_fieldset_end() {
		return '</fieldset>';
	}

	/**
	 * Return an opening `<legend>` tag.
	 *
	 * @return string
	 */
	public function get_legend_start() {
		return '<legend>';
	}

	/**
	 * Return a closing `</legend>` tag.
	 *
	 * @return string
	 */
	public function get_legend_end() {
		return '</legend>';
	}

	/**
	 * Return string wrapped in a `<p>` tag.
	 *
	 * @param string $text Content to wrap in a `<p>` tag.
	 * @return string $value Content wrapped in a `<p>` tag.
	 */
	public function get_p( $text = '' ) {
		return '<p>' . $text . '</p>';
	}

	/**
	 * Return a form <label> with for attribute.
	 *
	 * @param string $label_for  Form input to associate `<label>` with.
	 * @param string $label_text Text to display in the `<label>` tag.
	 * @return string $value `<label>` tag with filled out parts.
	 */
	public function get_label( $label_for = '', $label_text = '' ) {
		return '<label for="' . esc_attr( $label_for ) . '">' . strip_tags( $label_text ) . '</label>';
	}

	/**
	 * Return an html attribute denoting a required field.
	 *
	 * @param bool $required Whether or not the field is required.
	 * @return string `Required` attribute.
	 */
	public function get_required_attribute( $required = false ) {
		$attr = '';
		if ( $required ) {
			$attr .= 'required="true"';
		}
		return $attr;
	}

	/**
	 * Return a `<span>` to indicate required status, with class attribute.
	 *
	 * @return string Span tag.
	 */
	public function get_required_span() {
		return ' <span class="required">*</span>';
	}

	/**
	 * Return an aria-required attribute set to true.
	 *
	 * @param bool $required Whether or not the field is required.
	 * @return string Aria required attribute
	 */
	public function get_aria_required( $required = false ) {
		$attr = ( $required ) ? 'true' : 'false';
		return 'aria-required="' . $attr . '"';
	}

	/**
	 * Return an `<a>` tag with title attribute holding help text.
	 *
	 * @param string $help_text Text to use in the title attribute.
	 * @return string <a> tag with filled out parts.
	 */
	public function get_help( $help_text = '' ) {
		return '<a href="#" class="wpg-help dashicons-before dashicons-editor-help" title="' . esc_attr( $help_text ) . '"></a>';
	}

	/**
	 * Return a `<span>` tag with the help text.
	 *
	 * @param string $help_text Text to display after the input.
	 * @return string
	 */
	public function get_description( $help_text = '' ) {
		return '<span class="wpg-field-description">' . $help_text . '</span>';
	}

	/**
	 * Return a maxlength HTML attribute with a specified length.
	 *
	 * @param string $length How many characters the max length should be set to.
	 * @return string $value Maxlength HTML attribute.
	 */
	public function get_maxlength( $length = '' ) {
		return 'maxlength="' . esc_attr( $length ) . '"';
	}

	/**
	 * Return a onblur HTML attribute for a specified value.
	 *
	 * @param string $text Text to place in the onblur attribute.
	 * @return string $value Onblur HTML attribute.
	 */
	public function get_onblur( $text = '' ) {
		return 'onblur="' . esc_attr( $text ) . '"';
	}

	/**
	 * Return a placeholder HTML attribtue for a specified value.
	 *
	 * @param string $text Text to place in the placeholder attribute.
	 * @return string $value Placeholder HTML attribute.
	 */
	public function get_placeholder( $text = '' ) {
		return 'placeholder="' . esc_attr( $text ) . '"';
	}

	/**
	 * Return a span that will only be visible for screenreaders.
	 *
	 * @param string $text Text to visually hide.
	 * @return string $value Visually hidden text meant for screen readers.
	 */
	public function get_hidden_text( $text = '' ) {
		return '<span class="visuallyhidden">' . $text . '</span>';
	}
	
	private function processAtributes( $array_atribute = array() ) {
	    $attrs = "";
	    if(!empty($array_atribute)){
	        foreach ( $array_atribute as $attr_name=>$attr_value) {
	            $attrs .= " " . $attr_name ."=\"".$attr_value."\"";
	        }
	    }
	    return $attrs;
	}

	public function get_boolean_select_input( $props = array(), $default_value = FALSE) {
	    $props['options']=array(
	        array(
	            'attr' => FALSE,
	            'text' => esc_attr__('False', WPG_TEXT_DOMAIN),
	        ),
	        array(
	            'attr' => TRUE,
	            'text' => esc_attr__('True', WPG_TEXT_DOMAIN),
	        )
	    );	    
	    return $this->get_select_input($props, $default_value);
	}
	/**
	 * Return a populated `<select>` input.
	 *
	 * @param array $args Arguments to use with the `<select>` input.
	 * @return string $value Complete <select> input with options and selected attribute.
	 */
	public function get_select_input( $args = array(), $default_value = NULL) {
		$defaults = $this->get_default_input_parameters();

		$args = wp_parse_args( $args, $defaults );
        $error_messages = array();
		$value = '';
		if ( $args['wrap'] ) {
		    $value  = $this->get_tr_start($args);
			$value .= $this->get_th_start();
			$value .= $this->get_label( $args['name'], $args['labeltext'] );
			if ( $args['required'] ) { $value .= $this->get_required_span(); }
			if ( ! empty( $args['helptext'] ) ) { $value .= $this->get_help( $args['helptext'] ); }
			$value .= $this->get_th_end();
			$value .= $this->get_td_start();
		}
		$attrs = $this->processAtributes((isset($args) && isset($args['attr']))? $args['attr']:NULL);		

		$found = FALSE;
		$value .= '<select id="' . $args['name'] . '" name="' . $args['namearray'] . '[' . $args['name'] . ']"'.$attrs.'>';
		if(isset($args['selections'])){
		    array_push($error_messages,'This element is using the wrong prop to create the element.("selections" is deprecated)');
		    $args['options'] = $args['selections']['options'];
		    $args['selected'] = $args['selections']['selected'];
		    unset($args['selections']);
		}
		
		if ( ! empty( $args['options'] ) && is_array( $args['options'] ) ) {
			foreach ( $args['options'] as $val ) {
				$result = '';				
				if($args['selected'] !== NULL){
    				if($args['selected'] === $val['attr']){
    				    $result = ' selected="selected"';
    				    $found = TRUE;
    				}				
				}
				else if($default_value !== NULL && $val['attr'] === $default_value){
				    $result = ' selected="selected"';
				    $found = TRUE;
				}
				
				$value .= '<option value="' . $val['attr'] . '"' . $result . '>' . $val['text'] . '</option>';
			}
		}
		$value .= '</select>';
		if(!$found){
		    if($args['selected'] !== NULL){
		        array_push($error_messages,'Selected value doesn\'t exists.("'.$args['selected'].'")');
		    }
		    if($args['selected'] === NULL && $default_value !== NULL){
		        array_push($error_messages,'Default value doesn\'t exists.("'.$default_value.'")');
		    }		    
		}
		if(count($error_messages)>0){
		    foreach ($error_messages as $key => $error_message){
		        $value .= '<span style="color:red;">'.$error_message.'</span>'.($key>0?'<br>':'');
		    }
		}

		if ( ! empty( $args['aftertext'] ) ) {
			$value .= ' ' . $this->get_description( $args['aftertext'] );
		}

		if ( $args['wrap'] ) {
			$value .= $this->get_td_end();
			$value .= $this->get_tr_end();
		}

		return $value;
	}

	/**
	 * Return a text input.
	 *
	 *
	 * @param array $args Arguments to use with the text input.
	 * @return string Complete text `<input>` with proper attributes.
	 */
	public function get_text_input( $args = array() ) {
		$defaults = $this->get_default_input_parameters(
			array(
				'maxlength'     => '',
				'onblur'        => '',
			)
		);
		$args = wp_parse_args( $args, $defaults );

		$value = '';
		if ( $args['wrap'] ) {
		    $value .= $this->get_tr_start($args);
			$value .= $this->get_th_start();
			$value .= $this->get_label( $args['name'], $args['labeltext'] );
			if ( $args['required'] ) { $value .= $this->get_required_span(); }
			$value .= $this->get_th_end();
			$value .= $this->get_td_start();
		}
		
		$attrs = $this->processAtributes((isset($args) && isset($args['attr']))? $args['attr']:NULL);

		$value .= '<input type="text" id="' . $args['name'] . '" name="' . $args['namearray'] . '[' . $args['name'] . ']" value="' . $args['textvalue'] . '"';

		if ( $args['maxlength'] ) {
			$value .= ' ' . $this->get_maxlength( $args['maxlength'] );
		}

		if ( $args['onblur'] ) {
			$value .= ' ' . $this->get_onblur( $args['onblur'] );
		}

		$value .= ' ' . $this->get_aria_required( $args['required'] );

		$value .= ' ' . $this->get_required_attribute( $args['required'] );

		if ( ! empty( $args['aftertext'] ) ) {
			if ( $args['placeholder'] ) {
				$value .= ' ' . $this->get_placeholder( $args['aftertext'] );
			}
		}

		$value .= ' '.$attrs.'/>';

		if ( ! empty( $args['aftertext'] ) ) {
			$value .= $this->get_hidden_text( $args['aftertext'] );
		}

		if ( $args['helptext'] ) {
			$value .= '<br/>' . $this->get_description( $args['helptext'] );
		}

		if ( $args['wrap'] ) {
			$value .= $this->get_td_end();
			$value .= $this->get_tr_end();
		}

		return $value;
	}

	/**
	 * Return a `<textarea>` input.
	 *
	 * @param array $args Arguments to use with the textarea input.
	 * @return string $value Complete <textarea> input with proper attributes.
	 */
	public function get_textarea_input( $args = array() ) {
		$defaults = $this->get_default_input_parameters(
			array(
				'rows' => '',
				'cols' => '',
			)
		);
		$args = wp_parse_args( $args, $defaults );

		$value = '';

		if ( $args['wrap'] ) {
			$value .= $this->get_tr_start();
			$value .= $this->get_th_start();
			$value .= $this->get_label( $args['name'], $args['labeltext'] );
			if ( $args['required'] ) { $value .= $this->get_required_span(); }
			$value .= $this->get_th_end();
			$value .= $this->get_td_start();
		}
		
		$attrs = $this->processAtributes((isset($args) && isset($args['attr']))? $args['attr']:NULL);

		$value .= '<textarea id="' . $args['name'] . '" name="' . $args['namearray'] . '[' . $args['name'] . ']" rows="' . $args['rows'] . '" cols="' . $args['cols'] . '" '.$attrs.'>' . $args['textvalue'] . '</textarea>';

		if ( ! empty( $args['aftertext'] ) ) {
			$value .= $args['aftertext'];
		}

		if ( $args['helptext'] ) {
			$value .= '<br/>' . $this->get_description( $args['helptext'] );
		}

		if ( $args['wrap'] ) {
			$value .= $this->get_td_end();
			$value .= $this->get_tr_end();
		}

		return $value;
	}

	/**
	 * Return a checkbox `<input>`.
	 *
	 * @param array $args Arguments to use with the checkbox input.
	 * @return string $value Complete checkbox `<input>` with proper attributes.
	 */
	public function get_check_input( $args = array() ) {
		$defaults = $this->get_default_input_parameters(
			array(
				'checkvalue'        => '',
				'checked'           => 'true',
				'checklisttext'     => '',
				'default'           => false,
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$value = '';
		if ( $args['wrap'] ) {
			$value .= $this->get_tr_start();
			$value .= $this->get_th_start();
			$value .= $args['checklisttext'];
			if ( $args['required'] ) { $value .= $this->get_required_span(); }
			$value .= $this->get_th_end();
			$value .= $this->get_td_start();
		}
		
		$attrs = $this->processAtributes((isset($args) && isset($args['attr']))? $args['attr']:NULL);

		if ( isset( $args['checked'] ) && 'false' === $args['checked'] ) {
			$value .= '<input type="checkbox" id="' . $args['name'] . '" name="' . $args['namearray'] . '[]" value="' . $args['checkvalue'] . '" '.$attrs.'/>';
		} else {
			$value .= '<input type="checkbox" id="' . $args['name'] . '" name="' . $args['namearray'] . '[]" value="' . $args['checkvalue'] . '" checked="checked" '.$attrs.'/>';
		}
		$value .= $this->get_label( $args['name'], $args['labeltext'] );
		$value .= '<br/>';

		if ( $args['wrap'] ) {
			$value .= $this->get_td_end();
			$value .= $this->get_tr_end();
		}

		return $value;
	}

	/**
	 * Return a button `<input>`.
	 *
	 * @param array $args Arguments to use with the button input.
	 * @return string Complete button `<input>`.
	 */
	public function get_button( $args = array() ) {
		$value = '';
		$value .= '<input id="' . $args['id'] . '" class="button" type="button" value="' . $args['textvalue'] . '" />';

		return $value;
	}

	/**
	 * Return some array_merged default arguments for all input types.
	 *
	 * @param array $additions Arguments array to merge with our defaults.
	 * @return array $value Merged arrays for our default parameters.
	 */
	public function get_default_input_parameters( $additions = array() ) {
		return array_merge(
			array(
				'namearray'      => '',
				'name'           => '',
				'textvalue'      => '',
				'labeltext'      => '',
				'aftertext'      => '',
				'helptext'       => '',
				'helptext_after' => false,
				'required'       => false,
				'wrap'           => true,
				'placeholder'    => true,
			),
			(array) $additions
		);
	}
}
