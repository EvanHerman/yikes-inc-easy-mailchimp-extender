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
				lid	= prompt("Please enter the list id.");
				if(lid !== '')
					{
					$.ajax({
						type:	'POST',
						url:	ajaxurl,
						data: {
									action:					'yks_mailchimp_form',
									form_action:		'list_add',
									list_id:				lid
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
							else
								{
								alert("Looks like this list already exists!");
								}
							}
					});
					}
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
		$('.yks-mailchimp-import').live('click', function(e){
			i	= $(this).attr('rel');
			a	= confirm("Are you sure you want to import the list data from MailChimp?\n\nNOTICE: THE CURRENT LIST DATA WILL BE REPLACED WITH THE FIELD DATA FROM MAILCHIMP!");
			if(a)
				{
				$.ajax({
					type:	'POST',
					url:	ajaxurl,
					data: {
								action:					'yks_mailchimp_form',
								form_action:		'list_import',
								id:							i
								},
					dataType: 'json',
					success: function(MAILCHIMP)
						{
						if(MAILCHIMP != '-1')
							{
							$($('#yks-list-container_'+i)).replaceWith(MAILCHIMP);
							$('#yks-list-container_'+i).yksYellowFade();
							initializeScrollableLists();
							}
						else
							{
							alert("Looks like this list is already up to date!");
							}
						}
				});
				}
			return false;
		});
		$('.yks-notice-close').live('click', function(e){
			$.ajax({
				type:	'POST',
				url:	ajaxurl,
				data: {
							action:					'yks_mailchimp_form',
							form_action:		'notice_hide'
							},
				dataType: 'json',
				success: function(MAILCHIMP)
					{
					if(MAILCHIMP != '-1')
						{
						$('.yks-notice').slideUp('fast');
						}
					}
			});
		return false;
		});
		$('.yks-notice-toggle').live('click', function(e){
			if($('.yks-notice').hasClass('yks-hidden'))
				{
				$('.yks-notice').css('display', 'none');
				$('.yks-notice').removeClass('yks-hidden');
				}
			$('.yks-notice').slideDown('fast');
			return false;
		});
		});
</script>
<div class="wrap">
	<div id="ykseme-icon" class="icon32"><br /></div>
	
	<h2 id="ykseme-page-header">
		Easy Mailchimp Extender
		<a href="#" class="button add-new-h2 yks-mailchimp-list-add">Add New List</a>
		<a href="#" class="button add-new-h2 yks-notice-toggle">Show Notice for Version 1 Users</a>
	</h2>

	<h3>Manage the Mailchimp Lists</h3>
	
	<div class="yks-status" style="display: block;">
		<div class="yks-hidden<?php echo ($_COOKIE['yks-mailchimp-notice-hidden'] == '1' ? ' yks-notice' : ''); ?>">
			<a href="#" class="yks-notice-close">Hide Notice</a>
			<p>
				<strong>Notice:</strong> Version 2 is out! When you add a new list you will now be prompted to put the list id in immediately. We'll contact MailChimp for you and pull in all of your custom fields! <em>Lists created after version 2.0 use a different format for the shortcodes. Instead of a unique id being generated, I now use the MailChimp List Id. The 2.0 update had so many changes in functionality that I had to import the old lists to the new format -- if anything isn't working, please let me know!</em><br />
				<br />
				<strong>Please note -- due to the complexity of this update, all shortcodes have been changed to use the list id from MailChimp, please copy and paste them again! Additionally, if you make any changes on the MailChimp side, you need to click Import Changes on the appropriate list. I'm looking in to a way to automate this in future versions.</strong><br />
				<br />
				If you experience any bugs or have a feature request, please submit them to our <a href="https://github.com/yikesinc/yikes-inc-easy-mailchimp-extender">Github Issue Tracker</a>.
			</p>
		</div>
	</div>
	
	<div id="yks-list-wrapper"><?php echo $this->generateListContainers(); ?></div>
	
</div>

<?php $this->getTrackingGif('lists'); ?>