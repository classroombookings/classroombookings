<div class="alpha three columns">
	<h6 class="remove-bottom toggle"><?php echo $category ?></h6>
</div>

<div class="omega nine columns">

	<fieldset class="tristates">
		
		<?php
		$find = array('%id%', '%caption%', '%val%');
		
		foreach($options as $opt){
		
			$id = 'permissions[' . $opt[0] . ']';
			$id = str_replace(".", "_", $id);
			$caption = $opt[1];
			$hint = @$opt[2];
			
			$val = $values[str_replace(".", "_", $opt[0])];
			
			$replace = array($id, $caption, $val);
			
			$str = '<label class="tristate" data-id="%id%" data-value="%val%">
				<span>%caption%</span>
				</label>';
			echo str_replace($find, $replace, $str);
			
		}
		?>

	</fielfset>
	
</div>


<hr class="add-half">