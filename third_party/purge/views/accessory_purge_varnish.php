<a id="purge_varnish" href="#">Send purge request to Varnish now.</a>

<script type="text/javascript" charset="utf-8">
	$(document).ready(function()
	{
		$('#purge_varnish').click(function(event)
		{
			event.preventDefault();
			
			$link = $(this);
			$link.html('Sending purge request...');
			
			$.post("<?=$request_url?>",
				{
					XID: EE.XID
				}, function(data)
				{
					$link.html('Request sent. Click to send another purge request.');
				}
			);
		});
	});
	
</script>