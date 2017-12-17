<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:47
 */

namespace DataBundle\Services\Base;


use DataBundle\Entity\ArtEntityInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use HelperBundle\Services\Slug\SlugServiceInterface;

abstract class BaseArtService extends BaseService implements BaseArtServiceInterface
{
    /** @var string */
    private $entityType;

    /** @var EntityRepository */
    private $repository;

    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * BaseArtworkService constructor.
     * @param EntityManager $entityManager
     * @param SlugServiceInterface $slugService
     * @param string $entityType
     */
    public function __construct(EntityManager $entityManager, SlugServiceInterface $slugService, string $entityType)
    {
        parent::__construct($entityManager);
        $this->entityType = $entityType;
        $this->slugService = $slugService;
    }

    /**
     * Gets all items in the given range filtered by the keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return array
     */
    public function getAll(int $offset = 0, int $count = 12, string $keyword = ''): array
    {
        return $this->getFilteredQueryBuilder($keyword)
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    /**
     * Gets a querybuilder with a keyword filter
     *
     * @param string $keyword
     * @return QueryBuilder
     */
    protected function getFilteredQueryBuilder(string $keyword)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('entity.description', ':keyword'),
                $queryBuilder->expr()->like('entity.name', ':keyword')
            ))
            ->setParameter('keyword', "%$keyword%");
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        if ($this->repository === null) {
            $this->repository = $this->entityManager->getRepository($this->entityType);
        }

        return $this->repository->createQueryBuilder('entity');
    }

    /**
     * @inheritdoc
     */
    public function countAll(string $keyword = ''): int
    {
        $queryBuilder = $this->getFilteredQueryBuilder($keyword);

        return $queryBuilder
            ->select($queryBuilder->expr()->count('entity'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        $this->getQueryBuilder()
            ->delete($this->entityType, 'entity')
            ->where('entity.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $idOrSlug int|string
     * @return mixed
     */
    public function get($idOrSlug)
    {
        if (is_numeric($idOrSlug)) {
            return $this->getById($idOrSlug);
        }
        return $this->getBySlug($idOrSlug);
    }

    /**
     * Gets the entity by id
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return $this->entityManager->find($this->entityType, $id);
    }

    /**
     * Gets the entity by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function getBySlug(string $slug)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where('entity.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param ArtEntityInterface $entity
     * @return mixed
     */
    public function save($entity)
    {
        if ($entity->getSlug() === null) {
            $entity->setSlug($this->slugService->generateSlug($entity->getName()));
        }

        return parent::save($entity);
    }
}