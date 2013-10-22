<script type="text/javascript">
        jQuery(document).ready(function($)
                {
                function noListsCheck()
                        {
                        if($('#yks-list-wrapper .yks-list-container').size() <= 0)
                                {
                                $('#yks-list-wrapper').html('<p>No forms have been added yet.</p>');
                                }
                        }
                function EnterListID (lid, name)
                        {
                                if(lid !== '')
                                        {
                                        $.ajax({
                                                type:   'POST',
                                                url:    ajaxurl,
                                                data: {
                                                                        action:                 'yks_mailchimp_form',
                                                                        form_action:    'list_add',
                                                                        list_id:                lid,
                                                                        name:                   name,
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
                                                                alert('Oops.. The list ID you entered appears to be incorrect.');
                                                                }
                                                        }
                                        });
                                        }
                                                
                        return false;
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
                                axis:                            'y',
                                handle:                         '.yks-mailchimp-sorthandle',
                                placeholder:    'yks-mailchimp-fields-placeholder',
                                update: function(event, ui)
                                        {
                                        var i = $(this).attr('rel');
                                        var newCt       = 0;
                                        var updateString        = '';
                                        var fieldCt     = $('#yks-mailchimp-fields-list_'+i+' label').size();
                                        $('#yks-mailchimp-fields-list_'+i+' label').each(function(e){
                                                var fid                         = $(this).attr('rel');
                                                updateString    += fid+':'+newCt;
                                                if((newCt+1) < fieldCt) updateString    += ';';
                                                newCt++;
                                        });
                                        // Update the sort orders
                                        if(updateString !== '')
                                                {
                                                $.ajax({
                                                        type:   'POST',
                                                        url:    ajaxurl,
                                                        data: {
                                                                                action:                                 'yks_mailchimp_form',
                                                                                list_id:                                i,
                                                                                update_string:  updateString,
                                                                                form_action:            'list_sort'
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
                $('#yks-lists-dropdown').submit(function(e) {
                        e.preventDefault();
                        var lid = $("select#yks-list-select option:selected").val();
                        var name = $('select#yks-list-select option:selected').html();
                                if (lid)
                                        {
                                                EnterListID (lid, name);
                                                $('#yks-submit-list-add').attr("disabled", true);
                                                $("select#yks-list-select option[value='']").prop('selected',true);
                                                $("select#yks-list-select option[value='" + lid + "']").remove();
                                                setInterval(function()
                                                        {
                                                                $('#yks-submit-list-add').removeAttr("disabled");
                                                        },3000);
                                        }
                                else 
                                        {
                                                alert ('You need to select a Mailchimp list in order to create a form for it');
                                        }
                        return false; 
                });

                $('.yks-mailchimp-list-update').live('click', function(e){
                        var i       = $(this).attr('rel');
                        var f       = '#yks-mailchimp-form_'+i;
                        $.ajax({
                                type:   'POST',
                                url:    ajaxurl,
                                data: {
                                                        action:                 'yks_mailchimp_form',
                                                        form_action:            'list_update',
                                                        form_data:               $(f).serialize()
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
                        var i		= $(this).attr('rel');
                        var title	= $(this).data('title');
                        var a		= confirm("Are you sure you want to delete this form?");
                        $("select#yks-list-select").append('<option value="' + i + '">' + title +'</option>');
                        if(a)
                                {
                                $.ajax({
                                        type:   'POST',
                                        url:    ajaxurl,
                                        data: {
                                                                action:                 'yks_mailchimp_form',
                                                                form_action:            'list_delete',
                                                                id:                                                     i
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
                        var i       = $(this).attr('rel');
                        var a       = confirm("Are you sure you want to re-import your fields from MailChimp?");
                        if(a)
                                {
                                $.ajax({
                                        type:   'POST',
                                        url:    ajaxurl,
                                        data: {
                                                                action:                         'yks_mailchimp_form',
                                                                form_action:                    'list_import',
                                                                id:                              i
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
                                                        alert("Looks like this form is already up to date!");
                                                        }
                                                }
                                });
                                }
                        return false;
                });
                $('.yks-notice-close').live('click', function(e){
                        $.ajax({
                                type:   'POST',
                                url:    ajaxurl,
                                data: {
                                                        action:                 'yks_mailchimp_form',
                                                        form_action:            'notice_hide'
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
    <div id="ykseme-icon" class="icon32"></div>
        <h2 id="ykseme-page-header">
            Easy Mailchimp Forms by YIKES, Inc.
        </h2>
    <?php  if (!$this->optionVal['api-key']) { ?>    
		<p>
            Before you can add MailChimp forms to your site, you need to <a href="admin.php?page=yks-mailchimp-form" class="yks-mailchimp-list-add">go to the MailChimp Settings page</a> and add your API Key.
        </p>
	<?php } else {  //end if statement if no api key ?>
        <h3>Add Forms</h3>
        	<form id="yks-lists-dropdown" name="yks-lists-dropdown">
            	<table class="form-table yks-admin-form">
                	<tbody>            
                        <tr valign="top">
                        	<th scope="row">
                                Your Lists
                            </th>
                        	<td>
                        		<?php $this->getLists(); ?>                                                     
                                <input type="submit" name="submit" class="button-primary" id="yks-submit-list-add" value="Create a Form For This List" >
                            </td>
                        </tr>   
                    </tbody>
                </table>
        	</form>
        <h3>Manage Forms</h3>
        	<div id="yks-list-wrapper">
        		<?php echo $this->generateListContainers(); ?>
        	</div> 
    <?php }  //end else statement if there is an api key ?>         
</div>

<?php $this->getTrackingGif('lists'); ?>