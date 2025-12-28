<?php
declare(strict_types=1);

namespace OCA\DashLink\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version001Date20241227000000 extends SimpleMigrationStep {
	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('dashlink_links')) {
			$table = $schema->createTable('dashlink_links');

			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('title', Types::STRING, [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('url', Types::STRING, [
				'notnull' => true,
				'length' => 2048,
			]);
			$table->addColumn('description', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('icon_path', Types::STRING, [
				'notnull' => false,
				'length' => 512,
			]);
			$table->addColumn('icon_mime_type', Types::STRING, [
				'notnull' => false,
				'length' => 64,
			]);
			$table->addColumn('target', Types::STRING, [
				'notnull' => true,
				'length' => 10,
				'default' => '_blank',
			]);
			$table->addColumn('groups_json', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('position', Types::INTEGER, [
				'notnull' => true,
				'unsigned' => true,
				'default' => 0,
			]);
			$table->addColumn('enabled', Types::SMALLINT, [
				'notnull' => true,
				'unsigned' => true,
				'length' => 1,
				'default' => 1,
			]);
			$table->addColumn('created_at', Types::DATETIME, [
				'notnull' => true,
			]);
			$table->addColumn('updated_at', Types::DATETIME, [
				'notnull' => true,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['position'], 'idx_position');
			$table->addIndex(['enabled'], 'idx_enabled');
		}

		return $schema;
	}
}
