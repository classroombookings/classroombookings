<dl>
	<dt>Holidays</dt>
	<dd>Enter the first and last dates of the holiday itself; do not include teaching days.</dd>

	<dt>Session</dt>
	<dd>The holiday must be in <?= html_escape($session->name) ?>: between
		<span><?= $session->date_start->format('d/m/Y') ?></span> and
		<span><?= $session->date_end->format('d/m/Y') ?>.</span>
	</dd>

	<dt>Date format</dt>
	<dd>Use the <span>DD/MM/YYYY</span> format when entering dates. For example <em>16/04/2018</em>.</dd>
</dl>
