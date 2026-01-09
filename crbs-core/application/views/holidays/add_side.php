<dl>
	<dt><?= lang('holiday.holidays') ?></dt>
	<dd><?= lang('holiday.help.dates') ?></dd>

	<dt><?= lang('session.session') ?></dt>
	<dd><?= sprintf(lang('holiday.help.session'), html_escape($session->name)) ?>
		<span><?= $session->date_start->format('d/m/Y') ?></span> -
		<span><?= $session->date_end->format('d/m/Y') ?>.</span>
	</dd>

	<dt><?= lang('holiday.help.date_format') ?></dt>
	<dd><?= lang('holiday.help.date_format.text') ?></dd>
</dl>
