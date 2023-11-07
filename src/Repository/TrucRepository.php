<?php

namespace App\Repository;

use App\Entity\Truc;
use App\Search\SearchableEntitySearch;
use App\Search\TrucSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Truc>
 *
 * @method Truc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Truc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Truc[]    findAll()
 * @method Truc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrucRepository extends ServiceEntityRepository
{

    /** @use SearchableEntityRepositoryTrait<Truc> */
    use SearchableEntityRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Truc::class);
    }

    /**
     * Génère la requête de recherche de cellules (Sans l'exécuter)
     */
    private function getSearchQuery(SearchableEntitySearch $search): QueryBuilder
    {
        if (!($search instanceof TrucSearch)) {
            throw new \Exception("TrucSearch expected (" . __FILE__ . ":" . __LINE__ . ")");
        }

        $query = $this->createQueryBuilder('t');
        $query->leftJoin('t.tags', 'tag')->addSelect('tag');
        $query->leftJoin('t.images', 'image')->addSelect('image');

        if ($search->search) {
            foreach (explode(' ', $search->search) as $word) {
                $query->andWhere('t.nom LIKE :word OR tag.nom LIKE :word')
                    ->setParameter('word', "%{$word}%");
            }
        }

        if ($search->tag) {
            $query->andWhere('tag.slug = :tag')
                ->setParameter('tag', $search->tag);
        }

        if ($search->publie !== null) {
            $query->andWhere('t.publie = :publie')
                ->setParameter('publie', $search->publie);
        }

        if ($search->user) {
            $query->andWhere('t.user = :user')
                ->setParameter('user', $search->user);
        }

        $order = $search->order ?? 'DESC';
        switch ($search->tri) {
            case 'nom':
                $query->orderBy('t.nom', $order);
                break;
            default:
                $query->orderBy('t.id', $order);
                break;
        }

        return $query;
    }

    public function getTagsCpt(): array
    {
        $query = $this->createQueryBuilder('truc')
            ->join('truc.tags', 'tag')
            ->select('tag.nom as nom')
            ->addSelect('tag.slug as slug')
            ->addSelect('count(truc.id) as cpt')
            ->groupBy('tag.id')
            ->orderBy('cpt', 'DESC')
            ->having('cpt > 1')
            ->getQuery();

        $res = $query->getArrayResult();

        return array_map(
            fn ($nom, $slug, $cpt) => compact('nom', 'slug', 'cpt'),
            array_column($res, 'nom'),
            array_column($res, 'slug'),
            array_column($res, 'cpt'),
        );
    }
}
