<?php

namespace Backend\Modules\ContentBlocks\Repository;

use Backend\Modules\ContentBlocks\Entity\ContentBlock;
use Backend\Modules\ContentBlocks\ValueObject\ContentBlockStatus;
use Common\Locale;
use Doctrine\ORM\EntityRepository;

class ContentBlockRepository extends EntityRepository
{
    /**
     * @param Locale $locale
     *
     * @return int
     */
    public function getNextIdForLanguage(Locale $locale)
    {
        return (int) $this->getEntityManager()
            ->createQueryBuilder()
            ->select('MAX(cb.id) as id')
            ->from(ContentBlock::class, 'cb')
            ->where('cb.locale = :locale')
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getSingleScalarResult() + 1;
    }

    /**
     * @param int $id
     * @param Locale $locale
     *
     * @return ContentBlock|null
     */
    public function findOneByIdAndLocale($id, Locale $locale)
    {
        return $this->findOneBy(['id' => $id, 'status' => ContentBlockStatus::active(), 'locale' => $locale]);
    }

    /**
     * @param int $revisionId
     * @param Locale $locale
     *
     * @return ContentBlock|null
     */
    public function findOneByRevisionIdAndLocale($revisionId, Locale $locale)
    {
        return $this->findOneBy(
            ['revisionId' => $revisionId, 'status' => ContentBlockStatus::active(), 'locale' => $locale]
        );
    }

    /**
     * @param int $id
     * @param Locale $locale
     */
    public function removeByIdAndLocale($id, Locale $locale)
    {
        array_map(
            function (ContentBlock $contentBlock) {
                $this->getEntityManager()->remove($contentBlock);
            },
            (array) $this->findBy(['id' => $id, 'locale' => $locale])
        );
    }
}
