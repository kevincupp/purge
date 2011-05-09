Purge
============================

Purge is an extension for ExpressionEngine 2 that sends a purge request to the Varnish cache upon entry submission/deletion. There is also an accessory option to manually send the request.

Why?
------------
I'd set the TTL of the objects in my Varnish cache to like a week if I could. The only problem is when I or the client needs to update the site, the changes are preferred to show up immediately. So I needed a way to automatically purge the cache whenever an entry is submitted, edited or deleted. And then Purge was born.

Installation
------------
Add the "purge" folder inside your system/expressionengine/third_party directory, and then install the extension and accessory.

Prepare Varnish
------------
Purge assumes your Varnish cache is accessible at $\_SERVER['HTTP\_HOST'] through port 80. To change these settings, edit purge/helpers/varnish\_helper.php (this may be added to a Settings screen in a later update). Purge will send an EE\_PURGE request to Varnish. To get Varnish reacting to this request, add this to your VCL file in vcl_recv:

	if (req.request == "EE_PURGE") {
		purge("req.http.host ~ example.com && req.url ~ ^/.*$");
		error 200 "Purged";
	}

The above example assumes you want to purge everything under the example.com domain. For more examples on how to purge specific objects, see [this nice blog post](http://kristianlyng.wordpress.com/2010/02/02/varnish-purges/).

For now, Purge cannot decide which parts of a site need updating and which can stay the same, nor will it probably ever unless EE provides this data. So for now, it's best to purge the entire site.