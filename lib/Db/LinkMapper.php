<?php
declare(strict_types=1);

namespace OCA\DashLink\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @extends QBMapper<Link>
 */
class LinkMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'dashlink_links', Link::class);
	}

	/**
	 * Find all links ordered by position
	 *
	 * @return Link[]
	 */
	public function findAll(): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->orderBy('position', 'ASC');

		return $this->findEntities($qb);
	}

	/**
	 * Find link by ID
	 *
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function findById(int $id): Link {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

		return $this->findEntity($qb);
	}

	/**
	 * Find enabled links ordered by position
	 *
	 * @return Link[]
	 */
	public function findEnabled(): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('enabled', $qb->createNamedParameter(1, IQueryBuilder::PARAM_INT)))
			->orderBy('position', 'ASC');

		return $this->findEntities($qb);
	}

	/**
	 * Find enabled links for specific user groups
	 *
	 * @param array $userGroups Array of group IDs the user belongs to
	 * @return Link[]
	 */
	public function findForUser(array $userGroups): array {
		$allLinks = $this->findEnabled();

		// Filter links based on group membership
		return array_filter($allLinks, function (Link $link) use ($userGroups) {
			$linkGroups = $link->getGroups();

			// If no groups specified, link is visible to all
			if (empty($linkGroups)) {
				return true;
			}

			// Check if user is in any of the link's groups
			return !empty(array_intersect($userGroups, $linkGroups));
		});
	}

	/**
	 * Delete link by ID
	 */
	public function deleteById(int $id): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));

		$qb->executeStatement();
	}

	/**
	 * Update positions for multiple links
	 *
	 * @param array $linkIds Array of link IDs in the desired order
	 */
	public function updatePositions(array $linkIds): void {
		$position = 0;
		foreach ($linkIds as $linkId) {
			$qb = $this->db->getQueryBuilder();

			$qb->update($this->getTableName())
				->set('position', $qb->createNamedParameter($position, IQueryBuilder::PARAM_INT))
				->set('updated_at', $qb->createNamedParameter(new \DateTime(), IQueryBuilder::PARAM_DATE))
				->where($qb->expr()->eq('id', $qb->createNamedParameter($linkId, IQueryBuilder::PARAM_INT)));

			$qb->executeStatement();
			$position++;
		}
	}
}
