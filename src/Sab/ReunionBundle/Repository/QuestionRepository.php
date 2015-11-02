<?php

namespace Sab\ReunionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * QuestionRepository (Requêtes sql de la table Question)
 * 
 */
class QuestionRepository extends EntityRepository {
    
    /**
     * Requête d'update du nombre de "likes" d'une question 
     * @param Question $idQuestion => id de la question
     * @param integer $nbLike => nombre de likes
     * @return String
     */
    public function updateLikeQuestion($idQuestion, $nbLike) {

        $qb = $this->_em->createQueryBuilder("u")
                ->update("ReunionBundle:Question", "q")
                ->set("q.nbLike", $nbLike)
                ->where("q.id = :id")
                ->setParameter("id", $idQuestion)
                ->getQuery();
        $ex = $qb->execute();
        return $ex;
    }
    
    /**
     * Requête d'update du nombre de "Dislikes" d'une question
     * @param Question $idQuestion => id de la question
     * @param integer $nbDisLike => nombre de dislike
     * @return String
     */
    public function updateDisLikeQuestion($idQuestion, $nbDisLike) {
        $qb = $this->_em->createQueryBuilder("u")
                ->update("ReunionBundle:Question", "q")
                ->set("q.nbDislike", $nbDisLike)
                ->where("q.id = :id")
                ->setParameter("id", $idQuestion)
                ->getQuery();
        $ex = $qb->execute();
        return $ex;
    }
    
    
    /**
     * Requête pour compter le nombre de questions posées d'un évenement
     * @param Event $idEvent
     * @return integer
     */
    public function countQuestion($idEvent) {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("count('q')")
                ->from("ReunionBundle:Question", "q")
                ->where("q.event = :id")
                ->setParameter("id", $idEvent);

        return $qb->getQuery()
                        ->getSingleResult();
    }
    
    /**
     * Trier les questions selon les dates de publication des questions posées
     * @param Event $idEvent
     * @return Question
     */
    public function triQuestion($idEvent) {
        $qb = $this->_em->createQueryBuilder("c")
                ->select("q")
                ->from("ReunionBundle:Question", "q")
                ->where("q.event = :id")
                ->setParameter("id", $idEvent)
                ->orderBy('q.datePublication', 'DESC');

        return $qb->getQuery()
                        ->getResult();
    }
    
    /**
     * Mettre à jour la question repondu qui sera afficher à tous les utilisateurs 
     * @param Event $idEvent
     * @param Question $idQuestion
     * @param boolean $data => isFocus = (true || false)
     * @return Question
     */
    public function updateIsFocus($idEvent, $idQuestion, $data){
        $qb = $this->_em->createQueryBuilder("u")
                ->update("ReunionBundle:Question", "q")
                ->set("q.isfocus", $data)
                ->where("q.id = :id")
                ->andWhere("q.event = :idEvent")
                ->setParameter("id", $idQuestion)
                ->setParameter("idEvent", $idEvent)
                ->getQuery();
        $ex = $qb->execute();
        return $ex;
    }
}
