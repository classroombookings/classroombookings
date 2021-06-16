
<form action="<?php echo site_url('bookings/load') ?>" method="POST" name="bookings_book" id="bookings_date">
<table>
	<tr>
		<td valign="middle"><label for="chosen_date"><strong>Date:</strong></label></td>
		<td valign="middle">
			<input type="text" name="chosen_date" id="chosen_date" size="10" maxlength="10" value="<?php echo date("d/m/Y", $chosen_date) ?>" onblur="this.form.submit()" />
		</td>
		<td valign="middle">
			<img style="cursor:pointer" align="top" src="<?= base_url('assets/images/ui/cal_day.png') ?>" width="16" height="16" title="Choose date" onclick="displayDatePicker('chosen_date', false);" />
		</td>
		<td> &nbsp; <input type="submit" value=" Load " /></td>
	</tr>
</table>
</form>

<br />
