<?php
// If Caption
if($block['text']):
?>
	
	<div class="wp-caption aligncenter">
		<a href='<?php echo $block['file']['full']; ?>' target=_blank>
			<img src='<?php echo $block['file']['url']; ?>' />
		</a>
		<p class=wp-caption-text><?php echo $block['text']; ?></p>
	</div>

<?php
// If No Caption
else:
?>

	<a href='<?php echo $block['file']['full']; ?>' target=_blank>
		<img src='<?php echo $block['file']['url']; ?>' />
	</a>

<?php endif; ?>