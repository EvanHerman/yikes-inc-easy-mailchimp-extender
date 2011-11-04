<script type="text/javascript">
	jQuery(document).ready(function($)
		{
		function noListsCheck()
			{
			if($('#yks-list-wrapper .yks-list-container').size() <= 0)
				{
				$('#yks-list-wrapper').html('<p>Looks like you don\'t have any lists setup -- why don\'t you try <a href="#" class="yks-mailchimp-list-add">adding one?</a></p>');
				}
			}
		function scrollToElement(e)
			{
			$('html,body').animate({
					scrollTop: $(e).offset().top
				}, 'slow');
			}
		function initializeScrollableLists()
			{
			$('.yks-mailchimp-fields-list').sortable({
				axis:					'y',
				handle: 			'.yks-mailchimp-sorthandle',
				placeholder:	'yks-mailchimp-fields-placeholder',
				update: function(event, ui)
					{
					var i = $(this).attr('rel');
					var newCt	= 0;
					var updateString	= '';
					var fieldCt	= $('#yks-mailchimp-fields-list_'+i+' label').size();
					$('#yks-mailchimp-fields-list_'+i+' label').each(function(e){
						var fid				= $(this).attr('rel');
						updateString	+= fid+':'+newCt;
						if((newCt+1) < fieldCt) updateString	+= ';';
						newCt++;
					});
					// Update the sort orders
					if(updateString !== '')
						{
						$.ajax({
							type:	'POST',
							url:	ajaxurl,
							data: {
										action:					'yks_mailchimp_form',
										list_id:				i,
										update_string:	updateString,
										form_action:		'list_sort'
										},
							dataType: 'json',
							success: function(MAILCHIMP)
								{
								if(MAILCHIMP != '-1')
									{
									$('#yks-list-container_'+i).yksYellowFade();
									}
								else
									{
									
									}
								}
						});
						}
					}
			});
			}
		noListsCheck();
		initializeScrollableLists();
		$('.yks-mailchimp-list-add').live('click', function(e){
			a	= confirm("Are you sure you want to add a new list?");
			if(a)
				{
				$.ajax({
					type:	'POST',
					url:	ajaxurl,
					data: {
								action:					'yks_mailchimp_form',
								form_action:		'list_add'
								},
					dataType: 'json',
					success: function(MAILCHIMP)
						{
						if(MAILCHIMP != '-1')
							{
							if($('#yks-list-wrapper .yks-list-container').size() <= 0)
								{
								$('#yks-list-wrapper').html('');
								}
							$('#yks-list-wrapper').append(MAILCHIMP);
							scrollToElement($('#yks-list-wrapper .yks-list-container').last());
							initializeScrollableLists();
							}
						}
				});
				}
			return false;
		});
		$('.yks-mailchimp-list-update').live('click', function(e){
			i	= $(this).attr('rel');
			f	= '#yks-mailchimp-form_'+i;
			$.ajax({
				type:	'POST',
				url:	ajaxurl,
				data: {
							action:					'yks_mailchimp_form',
							form_action:		'list_update',
							form_data:			$(f).serialize()
							},
				dataType: 'json',
				success: function(MAILCHIMP)
					{
					if(MAILCHIMP != '-1')
						{
						$('#yks-list-container_'+i).yksYellowFade();
						}
					else
						{
						
						}
					}
			});
			return false;
		});
		$('.yks-mailchimp-delete').live('click', function(e){
			i	= $(this).attr('rel');
			a	= confirm("Are you sure you want to delete this list?");
			if(a)
				{
				$.ajax({
					type:	'POST',
					url:	ajaxurl,
					data: {
								action:					'yks_mailchimp_form',
								form_action:		'list_delete',
								id:							i
								},
					dataType: 'json',
					success: function(MAILCHIMP)
						{
						if(MAILCHIMP == '1')
							{
							$('#yks-list-container_'+i).remove();
							noListsCheck();
							scrollToElement($('#yks-list-wrapper'));
							}
						}
				});
				}
			return false;
		});
		});
</script>
<div class="wrap">
	<div id="ykseme-icon" class="icon32"><br /></div>
	
	<h2 id="ykseme-page-header">
		Easy Mailchimp Extender
		<a href="#" class="button add-new-h2 yks-mailchimp-list-add">Add New List</a>
	</h2>

	<h3>Manage the Mailchimp Lists</h3>
	
	<div class="yks-status" style="display: block;">
		<div class="yks-notice">
			<p>
				<strong>Notice:</strong> For the lists to work correctly right now, you need to use specific names for the MERGE VARS. I'm working on a proper fix that will allow you to use custom MERGE VARS, until then, please name them as follows:<br />
				<strong>First Name:</strong> *|FNAME|*<br />
				<strong>Last Name:</strong> *|LNAME|*<br />
				<strong>Address:</strong> *|ADDR1|*
			</p>
		</div>
	</div>
	
	<div id="yks-list-wrapper"><?php echo $this->generateListContainers(); ?></div>
	
</div>