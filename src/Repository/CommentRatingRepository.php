<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\CommentRating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CommentRating>
 *
 * @method CommentRating|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentRating|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentRating[]    findAll()
 * @method CommentRating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRatingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentRating::class);
    }

    public function save(CommentRating $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CommentRating $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAverage(Comment $comment): int
    {
        return $this->createQueryBuilder('cr')
            ->select('avg(cr.rating)')
            ->where('cr.comment = :comment')
            ->setParameter('comment', $comment)
            ->getQuery()
            ->getSingleScalarResult();
    }

//    /**
//     * @return CommentRating[] Returns an array of CommentRating objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CommentRating
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
