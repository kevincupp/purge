<?php

class Purge_mcp {

	public function __construct()
	{
		$sidebar = ee('CP/Sidebar')->make();

		$header = $sidebar->addHeader(
			lang('purge_cache_menu'),
			ee('CP/URL')->make('addons/settings/purge')
		);
		$header = $sidebar->addHeader(
			lang('purge_channel_rules'),
			ee('CP/URL')->make('addons/settings/purge/rules')
		);
	}

	/**
	 * Module Index Page
	 */
	public function index()
	{
		$base_url = ee('CP/URL')->make('addons/settings/purge');

		if (ee('Request')->method() == 'POST')
		{
			$purge_url = rtrim(ee()->config->item('site_url'), '/');

			if (ee('Request')->post('purge_scope') == 'custom')
			{
				$purge_url = $purge_url . '/' . ltrim(ee('Request')->post('custom_path'), '/');
			}

			$response = ee('purge:Varnish')->purge($purge_url);

			ee('CP/Alert')->makeInline('shared-form')
				->asSuccess()
				->withTitle(lang('purge_request_sent'))
				->addToBody(sprintf(
						lang('purge_request_sent_desc'),
						$purge_url,
						$response
					))
				->defer();

			ee()->functions->redirect($base_url);
		}

		$port = ee('purge:Varnish')->getPort();

		$vars = [
			'cp_page_title' => 'Purge',
			'save_btn_text' => 'Purge',
			'save_btn_text_working' => 'purging',
			'base_url' => $base_url,
			'sections' => [
				[
					[
						'title' => 'purge_cache',
						'desc' => sprintf(lang('purge_cache_desc'), $port, ee()->config->item('site_url')),
						'fields' => [
							'purge_scope' => [
								'type' => 'radio',
								'choices' => [
									'entire_site' => 'Entire site',
									'custom' => 'Custom path on current site'
								],
								'value' => 'entire_site'
							],
							'custom_path' => [
								'type' => 'text',
								'placeholder' => '/path/to/purge'
							]
						]
					]
				]
			]
		];

		return [
			'body' => ee('View')->make('ee:_shared/form_with_box')->render($vars)
		];
	}

	/**
	 * Channel Rules management
	 */
	public function rules()
	{
		$base_url = ee('CP/URL')->make('addons/settings/purge/rules');

		if (ee('Request')->method() == 'POST')
		{
			$this->saveRules();

			ee('CP/Alert')->makeInline('shared-form')
				->asSuccess()
				->withTitle(lang('rules_saved'))
				->defer();

			ee()->functions->redirect($base_url);
		}

		$grid = ee('CP/GridInput', [
			'field_name' => 'rules',
			'reorder'    => FALSE
		]);
		$grid->loadAssets();
		$grid->setColumns([
			'channel',
			'uri_path'
		]);
		$grid->setNoResultsText('no_rules', 'add_rule');

		$channels = ee('Model')->get('Channel')
			->filter('site_id', ee()->config->item('site_id'))
			->all()
			->getDictionary('channel_id', 'channel_title');

		$grid->setBlankRow([
			form_dropdown('channel_id', $channels),
			form_input('pattern', '', 'placeholder="/path/to/purge"')
		]);

		$rules = ee('Model')->get('purge:Rule')
			->filter('site_id', ee()->config->item('site_id'))
			->all();

		$data = [];
		foreach ($rules as $rule)
		{
			$data[] = [
				'attrs' => ['row_id' => $rule->getId()],
				'columns' => [
					form_dropdown('channel_id', $channels, $rule->channel_id),
					form_input('pattern', $rule->pattern)
				]
			];
		}

		$grid->setData($data);

		$vars = [
			'cp_page_title' => 'Purge',
			'save_btn_text' => sprintf(lang('btn_save'), lang('rules')),
			'save_btn_text_working' => 'btn_saving',
			'base_url' => $base_url,
			'sections' => [[[
				'title' => 'purge_channel_rules',
				'desc' => 'purge_channel_rules_desc',
				'wide' => TRUE,
				'grid' => TRUE,
				'fields' => [
					'rules' => [
						'type' => 'html',
						'content' => ee('View')->make('ee:_shared/table')->render($grid->viewData())
					]
				]
			]]]
		];

		return [
			'body' => ee('View')->make('ee:_shared/form_with_box')->render($vars)
		];
	}

	/**
	 * Saves rules from POST
	 */
	private function saveRules()
	{
		$rules = ee('Request')->post('rules');

		$edited_rules = [];
		$new_rules = [];

		if (isset($rules['rows']))
		{
			foreach ($rules['rows'] as $row_id => $columns)
			{
				if (strpos($row_id, 'row_id_') !== FALSE)
				{
					$rule_id = str_replace('row_id_', '', $row_id);
					$edited_rules[$rule_id] = $columns;
				}
				else
				{
					$new_rules[] = array_merge($columns, ['site_id' => ee()->config->item('site_id')]);
				}
			}
		}

		$delete = ee('Model')->get('purge:Rule')
			->filter('site_id', ee()->config->item('site_id'));
		if (count($edited_rules)) $delete->filter('id', 'NOT IN', array_keys($edited_rules));
		$delete->delete();

		foreach ($edited_rules as $rule_id => $columns)
		{
			ee('Model')->get('purge:Rule', $rule_id)->first()->set($columns)->save();
		}

		foreach ($new_rules as $columns)
		{
			ee('Model')->make('purge:Rule', $columns)->save();
		}
	}
}

// EOF
