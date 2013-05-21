<?php
$c=0;
foreach($cols as $col){
	$cols[$c] = '"'.$col.'"';
	$c++;
}
$list = implode(',',$cols);
?>
<script type="text/javascript">
var <?php echo $name ?> = new SortableTable(document.getElementById("<?php echo $id ?>"), [<?php echo $list ?>]);
// restore the class names
<?php echo $name ?>.onsort = function () {
	var rows = <?php echo $name ?>.tBody.rows;
	var l = rows.length;
	for (var i = 0; i < l; i++) {
		removeClassName(rows[i], i % 2 ? "tr0" : "tr1");
		addClassName(rows[i], i % 2 ? "tr1" : "tr0");
	}
};
</script>
