<?php

namespace App\Repository\Currency;

use App\Entity\Currency\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Currency>
 *
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

        public function findOneByCode($code): ?Currency
        {
            return $this->createQueryBuilder('c')
                ->andWhere('c.currencyCode = :code')
                ->setParameter('code', $code)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
