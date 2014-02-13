<?php

App::uses('Model', 'Model');

class Audit extends Model {

	public $hasMany = [
		'AuditDelta' => [
			'className' => 'AuditLog.AuditDelta'
		]
	];

	public function setupSearchPlugin() {
		$this->order = 'Audit.created DESC';

		$this->filterArgs = [
			'event' 		=> ['type' => 'value'],
			'model' 		=> ['type' => 'value'],
			'source_id' => ['type' => 'value'],
			'entity_id' => ['type' => 'value'],
		];

		$this->Behaviors->load('Search.Searchable');
	}

}
