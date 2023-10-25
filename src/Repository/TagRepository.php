<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Truc;
use App\Search\SearchableEntitySearch;
use App\Search\TagSearch;
use App\Search\TrucSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    /** @use SearchableEntityRepositoryTrait<Truc> */
    use SearchableEntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Génère la requête de recherche de cellules (Sans l'exécuter)
     */
    private function getSearchQuery(SearchableEntitySearch $search): QueryBuilder
    {
        if (!($search instanceof TagSearch)) {
            throw new \Exception("TagSearch expected (" . __FILE__ . ":" . __LINE__ . ")");
        }

        $query = $this->createQueryBuilder('t');

        if ($search->search) {
            $query->andWhere('t.nom LIKE :word')
                ->setParameter('word', "%{$search->search}%");
        }
        $query->orderBy('t.nom', 'ASC');

        return $query;
    }


}
