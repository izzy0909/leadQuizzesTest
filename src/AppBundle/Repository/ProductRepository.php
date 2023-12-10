<?php
declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);

        $this->entityManager = $this->getEntityManager();
    }

    /**
     * @param $product
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /**
     * @param $product
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove($product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * @return Query
     */
    public function findAllProducts(): Query
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ;
    }

    /**
     * @param int $id
     *
     * @return array
     *
     * @throws NonUniqueResultException
     */
    public function getSingleProduct(int $id): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY)
            ;
    }
}
