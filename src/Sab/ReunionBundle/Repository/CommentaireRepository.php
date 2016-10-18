<?php

namespace Sab\ReunionBundle\Repository;

use Doctrine\ORM\EntityRepository;


class CommentaireRepository extends EntityRepository
{

	public function triCommentaireLevel1($questionId) {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("c")
                ->from("ReunionBundle:Commentaire", "c")
                ->where("c.question = :id")
                ->andWhere("c.flagDeleted = false")
                ->andWhere("c.level = 1")
                ->setParameter("id", $questionId)
                ->orderBy('c.datePublication', 'ASC');

        return $qb->getQuery()
                        ->getResult();
    }

    public function getlastCommentaireLevel1 ($questionId)
    {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("c")
                ->from("ReunionBundle:Commentaire", "c")
                ->where("c.question = :id")
                ->andWhere("c.flagDeleted = false")
                ->andWhere("c.level = 1")
                ->setParameter("id", $questionId)
                ->orderBy('c.datePublication', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()
                        ->getResult();
    }

    public function countCommentaireForQuestionLevel1($questionId)
    {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("count('c')")
                ->from("ReunionBundle:Commentaire", "c")
                ->where("c.question = :id")
                ->andWhere("c.flagDeleted = false")
                ->andWhere("c.level = 1")
                ->setParameter("id", $questionId);

        return $qb->getQuery()
                        ->getSingleResult();
    }


    public function triCommentaireLevel2($questionId,$parentCommentId) {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("c")
                ->from("ReunionBundle:Commentaire", "c")
                ->where("c.question = :id")
                ->andWhere("c.flagDeleted = false")
                ->andWhere("c.level = 2")
                ->andWhere("c.parentCommentId = :parentId")
                ->setParameter("id", $questionId)
                ->setParameter("parentId", $parentCommentId)
                ->orderBy('c.datePublication', 'ASC');

        return $qb->getQuery()
                        ->getResult();
    }

    public function getlastCommentaireLevel2 ($questionId,$parentCommentId)
    {
    	$qb = $this->_em->createQueryBuilder("c")
    			->select("c")
    			->from("ReunionBundle:Commentaire", "c")
    			->where("c.question = :id")
                ->andWhere("c.flagDeleted = false")
                ->andWhere("c.level = 2")
                ->andWhere("c.parentCommentId = :parentId")
                ->setParameter("parentId", $parentCommentId)
    			->setParameter("id", $questionId)
    			->orderBy('c.datePublication', 'ADC');
    	$qb->setMaxResults(1);

    	return $qb->getQuery()
    					->getResult();
    }

    public function countCommentaireForCommentaireLevel2($questionId,$parentCommentId)
    {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("count('c')")
                ->from("ReunionBundle:Commentaire", "c")
                ->where("c.question = :id")
                ->andWhere("c.flagDeleted = false")
                ->andWhere("c.level = 2")
                ->andWhere("c.parentCommentId = :parentId")
                ->setParameter("parentId", $parentCommentId)
                ->setParameter("id", $questionId);

        return $qb->getQuery()
                        ->getSingleResult();
    }
}
