<?php

namespace KevinCupp\Addons\Purge\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Channel Rule Model
 */
class Rule extends Model {

	protected static $_primary_key = 'id';
	protected static $_table_name = 'purge_rules';

	protected static $_typed_columns = [
		'id'         => 'int',
		'site_id'    => 'int',
		'channel_id' => 'int'
	];

	protected static $_relationships = [
		'Channel' => [
			'type'     => 'belongsTo',
			'model'    => 'ee:Channel'
		]
	];

	protected $id;
	protected $site_id;
	protected $channel_id;
	protected $pattern;

}

// EOF
