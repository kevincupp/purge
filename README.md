# Purge

Purge is an extension for ExpressionEngine 3 that sends a purge request to the Varnish cache upon entry submission/deletion. There is also an form to manually send a Purge request to purge the entire site or a specific path.

## Installation

Add the `purge` folder inside your `system/user/addons` directory, and then install the add-on.

## Prepare Varnish

Purge assumes your Varnish cache is accessible at your ExpressionEngine installation's `site_url` configuration item and at the current port you are accessing the control panel from. If you want to use a custom port, just specify it in your config.php:

	$config['varnish_port'] = 8080;

Purge will send a `PURGE` request to Varnish. To get Varnish reacting to this request, add this to your VCL file in `vcl_recv`:

	if (req.method == "PURGE") {
		ban("req.url ~ "+req.url);
		return(synth(200, "Purged"));
	}

## Per-channel purging

Purge can send a `PURGE` request to a specific URL path whenever a certain channel is updated or has entries deleted. This will make it so you do not have to Purge your entire site when an entry is updated.

But, note that if you have any of these channel rules set up, only those rules will be followed and Purge will never purge your entire site upon entry save/delete. This behavior is here mostly to be compatible with the EE2 behavior which was added via pull request, but I'm open to changes here.

## Changelog

* **2.0 - August 10, 2017**
	* EE3 release! EE2 version available in `2.x` branch.
* **1.1.1 - March 13, 2015**
	* Added support for MSM for URLs patterns in Purge Addon Admin settings. Can specify manual URLs in accessory.
* **1.1 - February 13, 2015**
	* Changed addon to only purge via url patterns when entries are saved. All cache can still be purged using the accessory.
* **1.0.4 - March 17, 2014**
	* Fixing issue #11 where there were bugs with purging multiple Varnish servers and purging by IP address.
* **1.0.3 - November 2, 2013**
	* Fixing issue #8 where the Purge accessory wasn't working for sites using sessions in the control panel.
* **1.0.2 - November 7, 2012**
	* Fixing issue #6 where output from purge request may be sent to the browser.
* **1.0.1 - May 22, 2012**
	* Added ability for multiple URLs to be purged.
	* Added ``varnish_site_url`` and ``varnish_port`` config overrides.
* **1.0 - December 12, 2011**
	* Initial release!
