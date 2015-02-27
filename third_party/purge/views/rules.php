<p>If you want to send specific purge requests to varnish when you save entries in a particular channel, add rows below selecting the channel then specify the url pattern. You could clear a listing page for example /channel_name/ or you could include url title /channel_name/item/{url_title}. You can specify multiple patterns per channel. Only channels with patterns will send purge requests. Be sure to also define in your site's config.php: 'varnish_site_url' => array('http://xx.xx.xx.xx') (you can specify multiple upstream varnish servers in the array) and 'varnish_port' => xxxx.</p>

<?php echo form_open($action_url, array('id' => 'frm-stash-rules'))?>

    <script>
	$( document ).ready(function() {
		var addtemplate = $('#add-template')[0].outerHTML; 
		$('#add-template').remove();
		
		$(document).on('click', '.icon-remove', function() {
			$(this).parents('tr.rulerow').remove();
		});
		
		$(document).on('click', '#add-row a', function() {
			$('table#rules tbody').append(addtemplate);
		});
		
	});
		
	</script>
	
	<style>
	i.icon-remove {
		font-style:normal; 
		font-size: 10px; 
		line-height: 10px;
		padding: 0 5px; 
		cursor: pointer;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
		border-radius: 50%;
		background-color: red; 
		color: white; 
		font-weight: bold; 
	}
	</style>
	<div class="rules">

        <table id="rules" class="mainTable padTable">
            <colgroup>
                <col style="width:15%" />
                <col style="width:38%" />
				<col style="width:1%" />
            </colgroup>
        	<thead>
        		<tr>
        			<th scope="col"><?php echo lang('Channel');?></th>
        			<th scope="col"><?php echo lang('Path');?></th>
					<th scope="col"></th>
        		</tr>
        	</thead>
        	<tbody>

			<?php foreach($rules as $rule): ?>
            <tr class='rulerow'>
                 <td>
                    <select name="rule[]" class="hook">
                        <option value="NULL">-- Please select --</option>
                        <?php foreach($channels as $channel): ?>
                            <option value="<?php echo $channel['channel_id']?>"<?php echo ($channel['channel_id']==$rule['channel_id']) ? 'selected' : '' ?>><?php echo $channel['channel_title']?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                
                <td>
                    <?php echo form_input('pattern[]', $rule['pattern']) ?>
                </td>
				
				<td class="center">
                    <i class="icon-remove">x</i>
                </td>
               
            </tr>
			<?php endforeach; ?>

            <tr id="add-template" class='rulerow'>

                 <td>
                    <select name="rule[]" class="hook">

                        <option value="NULL">-- Please select --</option>

                        <?php foreach($channels as $channel): ?>
                            <option value="<?php echo $channel['channel_id']?>"><?php echo $channel['channel_title']?></option>
                        <?php endforeach; ?>

                    </select>
                </td>
                
                <td>
                    <?php echo form_input('pattern[]') ?>
                </td>
				
				<td class="center">
                    <i class="icon-remove">x</i>
                </td>
               
            </tr>
        </tbody>
        </table>

        <div id="add-row"  class="stash_add_row">
            <i class="icon-plus-sign"></i> <a href="#"><?php echo lang('add_rule');?></a>
        </div> 

    </div>


    <div class="stash_rules_footer">
        <div class="stash_rules_footer-save">
             <input type="submit" value="Save rules" class="submit">
        </div>

    </div>


<?php echo form_close()?>