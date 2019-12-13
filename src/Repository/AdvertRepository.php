<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

//@formatter:off
/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit =null, $offset = null)
 */
//@formatter:on

class AdvertRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function findWhereInCurrentYear(QueryBuilder $qbuilder)
    {
        $qbuilder->andWhere('a.date BETWEEN :start AND:stop');
        $parameters = [
          'start' => new \DateTime(date('Y').'-01-01'),
          'end' => new \DateTime(date('Y').'-12-31'),
        ];
        $qbuilder->setParameters($parameters);
    }

    public function getAdvertByCategories(array $categoriesNames)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->innerJoin(
          'a.categories',
          'cat',
          Join::WITH,
          $qb->expr()
            ->in('cat.name', $categoriesNames)
        )
          ->getQuery()
          ->getResult();
    }

    public function getAdverts(int $page, int $nbPerPage) {
        $qb = $this->createQueryBuilder('adv');

        $qb->leftJoin('adv.image', 'img');
        $qb->addSelect('img');
        $qb->leftJoin('adv.categories', 'cat');
        $qb->addSelect('cat');

        $qb->addOrderBy('adv.date', 'DESC');

        $query = $qb->getQuery();
        $query->setFirstResult(($page-1)*$nbPerPage);
        $query->setMaxResults($nbPerPage);

        return new Paginator($query);
    }

    /**
     * @author J. Vap
     * Retourne un objet Advert, mais en prenant soin de remonter l'ensemble
     * des informations liées, pour éviter les multiples requêtes.
     *
     * @param int $id l'identifiant de l'objet Advert
     *
     * @return \App\Entity\Advert|null
     */
    public function getAdvertById(int $id): ?Advert {
        $qb = $this->createQueryBuilder('adv');
        $qb->where('adv.id = '.$id);
        $qb->leftJoin('adv.image', 'img');
        $qb->addSelect('img');
        $qb->leftJoin('adv.categories', 'cat');
        $qb->addSelect('cat');
        $qb->leftJoin('adv.applications', 'appl');
        $qb->addSelect('appl');


        $result = $qb->getQuery()->getResult();
        if (empty($result))
            return null;
        else
            return $result[0];
    }


    // /**
    //  * @return Advert[] Returns an array of Advert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
