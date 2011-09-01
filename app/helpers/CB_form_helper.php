<?php
function set_value($field = '', $default = '')
{
	if (FALSE === ($OBJ =& _get_validation_object()))
	{
		if ( ! isset($_POST[$field]))
		{
			return (!empty($default)) ? $defualt : null;
		}

		return form_prep($_POST[$field], $field);
	}

	return form_prep($OBJ->set_value($field, $default), $field);
}
?>