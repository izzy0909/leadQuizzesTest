<?php
declare(strict_types = 1);

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private $entityManager;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);

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
}
