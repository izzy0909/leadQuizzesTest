<?php
declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private $entityManager;

    /**
     * CategoryRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Category::class);

        $this->entityManager = $this->getEntityManager();
    }

    /**
     * @param Category $category
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /**
     * @param Category $category
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Category $category): void
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    /**
     * @return Query
     */
    public function findAllCategories(): Query
    {
        return $this->createQueryBuilder('c')
            ->getQuery()
            ;
    }
}
