<?php

namespace Sab\ReunionBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserRepositoty (Requêtes sql de la table User)
 *
 * @author Sabar Guechoud <guechoudsaby@gmail.com>
 */
class UserRepository extends EntityRepository {
    
    /**
     * Changer le statut de l'événément (ouvert ou fermer)
     * 
     * @param User $idUser
     * @param string $statut
     * @return string => ("On" || "Off")
     */
    public function updateStatutEvent($idUser, $statut) {
        if ($statut == "on") {
            $qb = $this->_em->createQueryBuilder("u")
                    ->update("ReunionBundle:User", "u")
                    ->set("u.enabled", 0)
                    ->where("u.id = :id")
                    ->setParameter("id", $idUser)
                    ->getQuery();
            $ex = $qb->execute();
            return $ex;
        }
        if ($statut == "off") {
            $qb = $this->_em->createQueryBuilder()
                    ->update("ReunionBundle:User", "u")
                    ->set("u.enabled", 1)
                    ->where("u.id = :id")
                    ->setParameter("id", $idUser)
                    ->getQuery();
            $ex = $qb->execute();
            return $ex;
        }
    }

    /**
     * Supprimer un événement
     * @param User $idUser
     * @return void
     */
    public function deleteEvent($idUser) {
        $qb = $this->_em->createQueryBuilder("u")
                ->update("ReunionBundle:User", "u")
                ->set("u.locked", 1)
                ->set("u.enabled", 0)
                ->where("u.id = :id")
                ->setParameter("id", $idUser)
                ->getQuery();
        $ex = $qb->execute();
        return $ex;
    }

}
