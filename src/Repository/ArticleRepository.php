<?php

namespace App\Repository;

use App\Entity\Article;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use function Symfony\Component\String\u;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private const NUM_ITEMS = 4;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null)
    {
        return $qb ?: $this->createQueryBuilder('a');
    }

    private function addIsPublishedQueryBuilder(QueryBuilder $qb = null)
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('a.published =true');
    }

    public function findAllPublishedOrderedByNewest(int $page = 1): Paginator
    {
        $qb = $this->addIsPublishedQueryBuilder()
            ->orderBy('a.createdAt', 'DESC')
        ;
        return (new Paginator($qb))->paginate($page);
    }

    /**
     * @return Article[]
     */
    public function findBySearchQuery(string $qb, int $limit = self::NUM_ITEMS): array
    {
        $searchTerms = $this->extractSearchTerms($qb);

        if (0 === \count($searchTerms)) {
            return [];
        }

        $qb = $this->getOrCreateQueryBuilder();

        foreach ($searchTerms as $key => $term) {
            $qb
                ->orWhere('a.title LIKE :t_'.$key)
                ->orWhere('a.content LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $qb
            ->orderBy('a.createdAt', 'DESC')
            ->andWhere('a.published =true')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    /**
     * Transforms the search string into an array of search terms.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
        $terms = array_unique($searchQuery->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, function ($term) {
            return 3 <=  u($term)->length();
        });
    }

    public function findAllPublishedOrderedByMonth(int $page = 1): Paginator
    {
        $qb = $this->addIsPublishedQueryBuilder()
            ->orderBy('a.createdAt', 'DESC')
        ;
        return (new Paginator($qb))->paginate($page);
    }

}
