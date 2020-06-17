<?php
$table_config = ['name' => $name, 'id' => $id, 'cols' => $cols];
?>
<script type="text/javascript">
Q.push(function() {
	var config = <?= json_encode($table_config, JSON_NUMERIC_CHECK) ?>;
	new SortableTable(document.getElementById(config.id), config.cols);
});
</script>
