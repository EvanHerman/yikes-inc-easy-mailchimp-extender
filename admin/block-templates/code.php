<pre>
	<?php echo $block['text']; ?>
</pre>

<?php
// If Caption
if($block['caption']):
?>

	<p class=wp-caption><?php echo $block['text']; ?></p>

<?php endif; ?>