<form action="<?php echo site_url('bookings/load') ?>" method="POST" name="bookings_book">
<table>
	<tr>
		<td valign="middle"><label for="chosen_date"><strong>Date:</strong></label></td>
		<td valign="middle">
			<input type="text" name="chosen_date" id="chosen_date" size="10" maxlength="10" value="<?php echo date("d/m/Y", $chosen_date) ?>" onchange="this.form.submit()" onblur="this.form.submit()" />
		</td>
		<td valign="middle">
			<img style="cursor:pointer" align="top" src="webroot/images/ui/cal_day.gif" width="16" height="16" title="Choose date" onclick="displayDatePicker('chosen_date', false);" />
		</td>
		<td> &nbsp; <input type="submit" value=" Load " /></td>
	</tr>
</table>
</form>

<br /> 

<?php echo @field($this->validation->chosen_date_error) ?>
