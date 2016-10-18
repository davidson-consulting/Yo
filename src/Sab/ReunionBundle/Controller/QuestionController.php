<?php

namespace Sab\ReunionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sab\ReunionBundle\Entity\Question;
use Sab\ReunionBundle\Form\QuestionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sab\ReunionBundle\Entity\Commentaire;
/**
 * Description of QuestionController (Gestion des questions)
 * 
 *
 * @author Sabar Guechoud <guechoudsaby@gmail.com>
 */
class QuestionController extends Controller {
     
     /**
     * decrement Question like
     * @param Question $question
     */
    public function addQuestionAction() {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $userId = $user->getId();
        $eventRepository = $this->getDoctrine()->getManager()->getRepository("ReunionBundle:Event")->findByUserUser($userId);
        foreach ($eventRepository as $e) {
            $event = $e;
            $eventId = $e->getId();
        }
        $questionRepository = $em->getRepository("ReunionBundle:Question");
        $countQuestion = $questionRepository->countQuestion($eventId);
        foreach ($countQuestion as $count) {
            $coutQuest = $count;
        }
        $dateDebutEvent = $user->getDateDebutEvent();
        $arrayDocs = $e->getDoc();
        $question = new Question();
        $questionType = new QuestionType();
        $form = $this->createForm($questionType, $question);
        if ($form->handleRequest($request)->isValid()) {
            $question->setFlagDeleted(false);
            $em->persist($question);
            $question->setEvent($event);
            $event->addQuestion($question);
            $em->flush();
            $this->addFlash('notice', 'Votre question a été envoyé avec succès');
            $this->subAddQuestion($question);
            $this->subFayeClientQuestionCount($coutQuest + 1, $eventId);
            $this->subFayeClientRefresh($eventId);

            return $this->redirect($this->generateUrl('user_dashboard'));
        }
        $questions = $questionRepository->triQuestion($eventId);
        $commentairesRepository = $em->getRepository('ReunionBundle:Commentaire');

        return $this->render("ReunionBundle:User:index.html.twig", array(
                    'form' => $form->createView(),
                    'questions' => $questions,
                    'event' => $event,
                    'countQuestion' => $countQuestion,
                    'dateDebutEvent' => $dateDebutEvent,
                    'commentairesRepository' => $commentairesRepository,
        ));
    }

    /**
     * Delete Question
     * @param Question $question
     */
    public function deleteQuestionAction(Question $question) {
        $em = $this->getDoctrine()->getManager();
        $commentaires = $em->getRepository("ReunionBundle:Commentaire")->findBy(array('question' => $question->getId() , 'flagDeleted' => false));

        $data = array('id' => $question->getId());
        $this->fayeClient("/deleteQuestion", $data);
        
        foreach ($commentaires as $commentaire) {
            $commentaire->setFlagDeleted(true);
            $em->flush();
        }
        
        $question->setFlagDeleted(True);
        // $em->remove($question);
        $em->flush();
        
        return new JsonResponse("OK");
    }

     /**
     * Mettre à jour le nombre de likes des questions
     * @param Question $question
     * @return Response => nombre questions "Liker"
     */
    public function updateLikeAction(Question $question) {

        $idEvent = $question->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();
        $questionRespository = $em->getRepository('ReunionBundle:Question');
        $questionId = $question->getId();
        $nbLikes = $question->getNbLike();

        $nbLikeNew = $nbLikes + 1;
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $query = $questionRespository->updateLikeQuestion($questionId, $nbLikeNew);
            $this->subLike($question, $nbLikeNew, 0);
            $nbLikeNew = $nbLikes + 1;
            $this->subFayeClientRefresh($idEvent);
            return new Response($nbLikeNew);
        }
    }

    /**
     * Mettre à jour le nombre de dislikes d'une question 
     * @param Question $question
     * @return Response => nombre de question "Disliker"
     */
    public function updateDisLikeAction(Question $question) {

        $idEvent = $question->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();
        $questionRespository = $em->getRepository('ReunionBundle:Question');
        $questionId = $question->getId();

        $nbDisLikes = $question->getNbDislike();
        $nbLikes = $question->getNbLike();
        $nbDisLikeNew = $nbDisLikes + 1;
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {

            $query = $questionRespository->updateDisLikeQuestion($questionId, $nbDisLikeNew);
            $this->subLike($question, $nbDisLikeNew, 1);
            $nbDisLikeNew = $nbDisLikes + 1;
            $this->subFayeClientRefresh($idEvent);
            return new Response($nbDisLikeNew);
        }
    }
    /**
     * Décrementer le nombre de like d'une question
     * @param Question $question
     */
    public function decrementeLikeAction(Question $question) {
        $idEvent = $question->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();
        $questionRespository = $em->getRepository('ReunionBundle:Question');
        $questionId = $question->getId();
        $nbDislike = $question->getNbDislike();

        $request = $this->getRequest();

        $nbDislikeNow = $nbDislike - 1;
        if ($nbDislikeNow >= 0) {
            $questionRespository->updateDisLikeQuestion($questionId, $nbDislikeNow);
        }
        if ($request->isXmlHttpRequest()) {
            if ($nbDislikeNow >= 0) {
                $this->subLike($question, $nbDislikeNow, 1);
                $this->subFayeClientRefresh($idEvent);
            }
            $this->subFayeClientRefresh($idEvent);
        }
        return new Response($nbDislikeNow);
    }

    /**
     * Décrementer le nombre de dislike d'une question
     * @param Question $question
     * @return Response => le nombre de "likes" 
     */
    public function decrementeDislikeAction(Question $question) {

        $idEvent = $question->getEvent()->getId();
        $em = $this->getDoctrine()->getManager();
        $questionRespository = $em->getRepository('ReunionBundle:Question');
        $questionId = $question->getId();
        $nbLikes = $question->getNbLike();
        $request = $this->getRequest();

        $nbLikeNow = $nbLikes - 1;
        if ($nbLikeNow >= 0) {
            $questionRespository->updateLikeQuestion($questionId, $nbLikeNow);
        }
        if ($request->isXmlHttpRequest()) {
            if ($nbLikeNow >= 0) {
                $this->subLike($question, $nbLikeNow, 0);
                $this->subFayeClientRefresh($idEvent);
            }
            $this->subFayeClientRefresh($idEvent);
        }
        return new Response($nbLikeNow);
    }

    
    /**
     * Abonner au tunnel pour mettre à jour les likes des autres utilisateurs  
     * @param type $question => Question
     * @param type $nbLikeNew => le nombre de "likes" mis à jours
     * @param type $etat => permission de "liker"
     */
    public function subLike($question, $nbLikeNew, $etat) {

        if ($etat == 0) {
            $data = array('datas' => array(
                    'id' => $question->getId(),
                    'contenu' => $question->getContenu(),
                    'auteur' => $question->getAuteur(),
                    'nbLikes' => $nbLikeNew,
                    'datePublication' => $question->getDatePublication()
            ));
            $this->fayeClient("/updateLikes", $data);
        } else {
            $data = array('datas' => array(
                    'id' => $question->getId(),
                    'contenu' => $question->getContenu(),
                    'auteur' => $question->getAuteur(),
                    'nbDisLikes' => $nbLikeNew,
                    'datePublication' => $question->getDatePublication()
            ));
            $this->fayeClient("/updateDisLikes", $data);
        }
    }
    
    /**
     * Abonner au tunnel "/addQuestion"
     * @param Question $question
     */
    public function subAddQuestion($question) {
        $data = array('datas' => array(
                'id' => $question->getId(),
                'contenu' => htmlentities($question->getContenu()),
                'auteur' => htmlentities($question->getAuteur()),
                'nbLikes' => $question->getNbLike(),
                'nbDisLikes' => $question->getNbDislike(),
                'datePublication' => $question->getDatePublication(),
                'idEvent' => $question->getEvent()->getId()
        ));
        $this->fayeClient("/addQuestion", $data);
    }

    /**
     * Mettre à jour le nombre de question posées pour les autres utilisateurs 
     * @param integer $count
     * @param Event $idEvent
     */
    public function subFayeClientQuestionCount($count, $idEvent) {
        $data = array(
            'count' => $count,
            'idEvent' => $idEvent
        );
        $this->fayeClient("/updateCountQuestion", $data);
    }
    
    /**
     * Rafraichir les pages des autres utilisateurs après chaque opération.
     * @param Event $idEvent
     */
    public function subFayeClientRefresh($idEvent) {
        $data = array(
            'idEvent' => $idEvent
        );
        $this->fayeClient("/refreshQuestion", $data);
    }
    
    /**
     * Fonction principale pour se communiquer avec le module faye-client de nodejs
     * @param String $channel => le nom du tunnel
     * @param array $data => les données à transmettre
     */
    public function fayeClient($channel, $data) {
        $faye = $this->container->get('sab.reunion.faye.client');
        $faye->send($channel, $data);
    }

}
