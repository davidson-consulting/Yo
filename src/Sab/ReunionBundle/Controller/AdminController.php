<?php

namespace Sab\ReunionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Sab\ReunionBundle\Entity\Event;
use Sab\ReunionBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sab\ReunionBundle\Form\EventType;
use Sab\ReunionBundle\Entity\Question;
use Sab\ReunionBundle\Form\QuestionType;
use Sab\ReunionBundle\Entity\Commentaire;
use Sab\ReunionBundle\Form\CommentaireType;

/**
 * Description of AdminController
 * Gestion du backe-end Admin 
 *
 * @author Sabar Guechoud <guechoudsaby@gmail.com>
 */
class AdminController extends Controller {
    
    /**
     * 
     * Affiher la page principale du BACKEND
     *    
     */
    public function indexAction() {
        return $this->render("ReunionBundle:Admin:index.html.twig");
    }
    
    
    /**
     * Ajouter des événements
     * 
     */
    public function addEventAction() {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $event = new \Sab\ReunionBundle\Entity\Event();
        $formEvent = new \Sab\ReunionBundle\Form\EventType();
        $form = $this->createForm($formEvent, $event);
        $request = $this->getRequest();
        if ($request->getMethod() === "POST") {
            $form->handleRequest($request);
            try {
                if ($form->isValid()) {
                    $password = $form->getData()->getUserUser()->getPassword();
                    $nameEvent = $form->getData()->getUserUser()->getUserName();
                    $event->setUserAdmin($user);
                    $event->getUserUser()->setEnabled(true);
                    $password_crypter = $event->getUserUser()->crypt($password);
                    $event->getUserUser()->setPlainPassword($password);
                    $event->getUserUser()->setEmail($nameEvent . "@event.fr");
                    $event->getUserUser()->setPasswordDecrypte($password_crypter);
                    $event->getTheme()->uploadFile(null, null);
                    $em->persist($event);
                    $em->flush();
                    $this->createFolder($event->getId());
                    $event->getTheme()->uploadFile($event->getId(), "add");
                    $this->addFlash('event_sucess', "Votre événement a été enregistré avec succès");
                    return $this->redirect($this->generateUrl("_list_event"));
                }
            } catch (\Exception $ex) {
                $this->addFlash('event_error', "Vos données sont pas correctes veuillez les vérifier !!!");
                return $this->redirect($this->generateUrl("_list_event"));
            }
        }
        return $this->render("ReunionBundle:Admin:formAddEvent.html.twig", array(
                    'formEvent' => $form->createView()
        ));
    }
    
    /**
     * Creation des dossiers pour les images des événements
     * @param Event $idEvent
     * 
     */
    public function createFolder($idEvent) {
        $fs = new Filesystem();
        $web = $this->getRequest()->getBasePath();
        $docRoot = $this->container->getParameter('root_path_local');
        $docFolder = $docRoot . $web;
        try {
            $fs->mkdir($docFolder . "/doc/event_" . $idEvent, 0777);
            $fs->mkdir($docFolder . "/doc/event_" . $idEvent . "/images", 0777);
            $fs->mkdir($docFolder . "/doc/event_" . $idEvent . "/banners", 0777);
            $fs->mkdir($docFolder . "/doc/event_" . $idEvent . "/logo", 0777);
            $fs->mkdir($docFolder . "/doc/event_" . $idEvent . "/background", 0777);
        } catch (Filesystem\Exception\IOExceptionInterface $ex) {
            $this->addFlash('event_error', "une erreur s'est porduite lors de la création du dossier ".$ex->getPath());
            return $this->redirect($this->generateUrl("_list_event"));
        }
    }
    
    
    /**
     * Affihce tous les événements crées
     * 
     */
    public function listEventAction() {
        $em = $this->getDoctrine()->getManager();

        $events = $em->getRepository("ReunionBundle:Event")->findBy(
                array(), array(
            'id' => 'desc'
                )
        );

        $questionRepository = $em->getRepository("ReunionBundle:Question");
        return $this->render("ReunionBundle:Admin:listEvent.html.twig", array(
                    'events' => $events,
                    'questionRepository' => $questionRepository
        ));
    }

    /**
     * Editer un événement 
     * @param Event $event
     * @throws Exception
     */
    public function editEventAction(Event $event) {
        $em = $this->getDoctrine()->getManager();
        $user = $event->getUserUser();
        $eventRepository = $em->getRepository("ReunionBundle:Event")->find($event->getId());

        if (!$eventRepository) {
            throw $this->createNotFoundException("l'entité n'exites pas");
        }

        $editForm = $this->createForm(new EventType(), $eventRepository);
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        if ($request->getMethod() === "POST") {
            $editForm->handleRequest($request);
            try {
                if ($editForm->isValid()) {
                    $password = $editForm->getData()->getUserUser()->getPassword();
                    $password_crypter = $event->getUserUser()->crypt($password);
                    $event->getUserUser()->setPasswordDecrypte($password_crypter);
                    $user->setPlainPassword($password);
                    $filePictureOlde = $editForm->getData()->getTheme()->getProfilPicture();
                    $fileLogoOlde = $editForm->getData()->getTheme()->getLogo();
                    $fileBackgroundOlde = $editForm->getData()->getTheme()->getBackgroundPicture();
                    $logoOriginal = $editForm->getData()->getTheme()->getFileLogo();
                    $profilOriginal = $editForm->getData()->getTheme()->getFileProfilPicture();
                    $backgroundOriginal = $editForm->getData()->getTheme()->getFileBackground();
                    $fs = new Filesystem();
                    if ($profilOriginal !== null) {
                        $fs->remove($event->getTheme()->getPathFileProfil($event->getId()));
                    }
                    if ($logoOriginal !== null) {
                        $fs->remove($event->getTheme()->getPathFileLogo($event->getId()));
                    }
                    if ($backgroundOriginal !== null) {
                        $fs->remove($event->getTheme()->getPathFileBackground($event->getId()));
                    }

                    if ($logoOriginal !== null || $profilOriginal !== null || $backgroundOriginal !== null) {
                        $event->getTheme()->uploadFile($event->getId(), "edit");
                    }
                    $em->flush();
                    $this->addFlash('event_sucess', "Votre événement a été modifié avec succès");
                    return $this->redirect($this->generateUrl("_list_event"));
                }
            } catch (\Exception $ex) {
                $this->addFlash('event_error', "Identifiant existe déja !");
                return $this->redirect($this->generateUrl("_list_event"));
            }
        }
        return $this->render("ReunionBundle:Admin:editeEvent.html.twig", array(
                    'formEvent' => $editForm->createView(),
                    'event' => $event
                        )
        );
    }
    
    /**
     * Détail d'événement
     * @param Event $event
     * 
     */
    public function eventDetailAction(Event $event) {
        return $this->render("ReunionBundle:Admin:eventDetail.html.twig", array('event' => $event));
    }
    
    /**
     * Afficher les questions d'un évenement
     * @param Event $event
     *
     */
    public function eventListQuestionAction(Event $event) {
        return $this->render("ReunionBundle:Admin:listQuestionAdmin.html.twig", array('event' => $event));
    }

   
    /**
     * Clôturer un événement
     * @param Event $event
     * @return Response OK => si le process s'est bien déroulé
     */
    
    public function cloturerEventAction(Event $event) {
        $em = $this->getDoctrine()->getManager();
        $id_user = $event->getUserUser()->getId();
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em->getRepository("ReunionBundle:User")->updateStatutEvent($id_user, "off");
            return new Response('ok');
        }
    }

    
    
    /**
     * Ouvrire un événement
     * @param Event $event
     * @return Response
     */
    public function ouvrireEventAction(Event $event) {
        $em = $this->getDoctrine()->getManager();
        $id_user = $event->getUserUser()->getId();

        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em->getRepository("ReunionBundle:User")->updateStatutEvent($id_user, "on");
            return new Response('ok');
        }
    }

    
    
    /**
     * Supprimer un événement
     * @param Event $event
     * @return Response OK => si le process est déroullé
     */
    public function deleteEventAction(Event $event) {
        $em = $this->getDoctrine()->getManager();
        $id_user = $event->getUserUser()->getId();

        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em->getRepository("ReunionBundle:User")->deleteEvent($id_user);
            return new Response('ok');
        }
    }

    
    
    /**
     * Chargement des questions en json
     * @param Event $event
     * @return JsonResponse => Tableau de quesions
     */
    public function loadQuestionJsonAction(Event $event) {
        $em = $this->getDoctrine()->getManager();
        $question = $event->getQuestion();
        $question = $em->getRepository("ReunionBundle:Question")->findBy(array('event' => $event->getId() , 'flagDeleted' => false));
        $tabToJson = array();
        $commentaireRepository = $em->getRepository('ReunionBundle:Commentaire');
        foreach ($question as $q) {

            $count_commentaire = $commentaireRepository->countCommentaireForQuestionLevel1($q->getid());
            foreach ($count_commentaire as $count)
            {
                $nbre_commentaire = $count;
            }
            $tab = array(
                'contenu' => htmlentities($q->getContenu()),
                'auteur' => htmlentities($q->getAuteur()),
                'nbLike' => $q->getNbLike(),
                'nbDislike' => $q->getNbDislike(),
                'datePublication' => (string)$q->getDatePublication()->format('d-m-Y H:i:s'),
                'idEvent' => $event->getId(),
                'idQuestion' => $q->getId(),
                'statutFocus' => $q->getIsfocus(),
                'fav' => '<a href="#"><span class="glyphicon glyphicon-star-empty icon_focus_' . $q->getId() . '"></span></a>',
                'delete' => '<a href="#"><span class="glyphicon glyphicon-trash icon_delete_' . $q->getId() . '" onClick="deleteQuestion(' . $q->getId() . ',' . $event->getId() . ')"></span></a>',
                'modifier' => '<a href="#"><span class="glyphicon glyphicon-edit" title="Modifier" onClick="modifierQuestion(' . $q->getId() . ',' . $event->getId() .')"></span></a>',
                'commentaire' => '<a href="#"><span class="glyphicon glyphicon-comment" title="Commentaire" onClick="listerCommentaire(' . $q->getId() .')"></span></a><span> ('.$nbre_commentaire.')</span>'
            );
            $tabToJson[] = $tab;
        }
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($tabToJson);
        }
        return new JsonResponse($tabToJson);
    }
    
    
    /**
     * Vérifier si le login existe déja (formmulaire d'ajout d'événement)
     * @return JsonResponse => Action et la valeur (username, no?yes)
     */
    
    public function ckechErrorsAction() {

        $eventR = $this->getDoctrine()->getManager()->getRepository("ReunionBundle:Event");
        $userR = $this->getDoctrine()->getManager()->getRepository("ReunionBundle:User");
        $val = "";
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $name = $request->request->get('name');
            $valeur = $request->request->get('value');
            if ($name == "Username") {
                $r = $userR->findByUsername($valeur);
                empty($r) ? $val = "no" : $val = "yes";

                $response = array(
                    'val' => $val,
                    'nameAction' => "username"
                );
                return new JsonResponse($response);
            }
        }
    }

    
    
    /**
     * Répondre à une question
     * @return Response boolean
     */
    public function focusQuestionAction() {
        $em = $this->getDoctrine()->getManager();
        $qRepository = $em->getRepository("ReunionBundle:Question");
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {

            $clickStatut = $request->request->get('clickStatut');
            $idEvent = $request->request->get('idEvent');
            $idQuestion = $request->request->get('idQuestion');
            $q = $em->getRepository("ReunionBundle:Question")->findById($idQuestion);

            $data = array(
                'datas' => array(
                    'idEvent' => $idEvent,
                    'idQuestion' => $idQuestion,
                    'contenu' => $q[0]->getContenu(),
                    'auteur' => $q[0]->getAuteur(),
                    'nbLikes' => $q[0]->getNbLike(),
                    'nbDisLikes' => $q[0]->getNbDislike(),
                    'datePublication' => $q[0]->getDatePublication(),
                    'isfocus' => $q[0]->getIsfocus()
            ));
            $clickStatut === "false" ? $qRepository->updateIsFocus($idEvent, $idQuestion, "1") : $qRepository->updateIsFocus($idEvent, $idQuestion, "0");
            $this->subFayeClientFocusQuestion($data);
        }
        return new Response($clickStatut);
    }

    /**
     * Modify question
     * @param Question $question
     */
    public function modificationQuestionAction(Question $question) {

        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
        }
        return new Response($question->getContenu());
    }

    
    /**
     * save modification question
     */
    public function saveModificationQuestionAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $idQuestion = $request->request->get('idQuestion');
            $contenu = $request->request->get('contenu');
            $q = $em->getRepository("ReunionBundle:Question")->findById($idQuestion);
            $q[0]->setContenu($contenu);
            $em->flush();
            
            //appeler fayClient    
            $data = array(
                'datas' => array(
                    'idQuestion' => $idQuestion,
                    'contenu' => $contenu,
            ));
            $this->subFayeClientUpdateContentQuestion($data); 
            
        }
        return new JsonResponse(
                array(
                    'statut' => 'ok'
                )
        );
    }

    //focus question
    public function subFayeClientFocusQuestion($data) {
        $this->fayeClient("/focusQuestion", $data);
    }
    
    /**
    *  tunnel FayeClient update content question 
    *   @param $data 
    *   @return null
    */

    public function subFayeClientUpdateContentQuestion($data){
        $this->fayeClient("/updateContentQuestion", $data);
    }
    
    /**
    *  tunnel FayeClient 
    *   @param $channel, $data 
    *   @return null
    */

    public function fayeClient($channel, $data) {
        $faye = $this->container->get('sab.reunion.faye.client');
        $faye->send($channel, $data);
    }

    /**
    *  Chargement des commentaires pour une question 
    *   @param Question 
    *   @return jsonResponse array
    */
    public function loadCommentaireJsonAction(Question $question)
    {
        $em = $this->getDoctrine()->getManager();
        // $commentaire = $question->getCommentaire();
        $commentaire = $em->getRepository("ReunionBundle:Commentaire")->findBy(array('question' => $question->getId() , 'flagDeleted' => false , 'level' => 1));


        $tabToJson = array();
        foreach ($commentaire as $c) {

            $reponses = $em->getRepository("ReunionBundle:Commentaire")->findBy(array('question' => $question->getId() , 'parentCommentId' => $c->getId() , 'flagDeleted' => false , 'level' => 2));

            //nombres de réponses à un commentaire
            $count_reponses = count($reponses);

           
            $tab = array(
                'texte' => htmlentities($c->getTexte()),
                'auteur' => htmlentities($c->getAuteur()),
                'datePublication' => htmlentities($c->getDatePublication()->format('d-m-Y H:i:s')),
                'idQuestion' => $question->getId(),
                'idCommentaire' => $c->getId(),
                'reponses' => '<a href="#"><span class="glyphicon glyphicon-comment" title="Commentaire" onClick="listerReponses(' . $c->getId() .')"></span></a><span> ('.$count_reponses.')</span>',
                'delete' => '<a href="#"><span class="glyphicon glyphicon-trash icon_delete_' . $c->getId() . '" onClick="deleteCommentaire(' . $c->getId() . ',' . $question->getId() . ')"></span></a>',
                'modifier' => '<a href="#"><span class="glyphicon glyphicon-edit" title="Modifier" onClick="modifierCommentaire(' . $c->getId() . ',' . $question->getId() .')"></span></a>'
            );
            $tabToJson[] = $tab;
        }
        //stats
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($tabToJson);
        }

        return new JsonResponse($tabToJson);
    }

/**
*  Chargement des reponses aux commentaires
*   @param Commentaire 
*   @return jsonResponse array 
*/
    public function loadReponsesJsonAction(Commentaire $commentaire)
    {
        $em = $this->getDoctrine()->getManager();
        // Get les réponses(commentaires de level 2) pour un commentaire
        $reponse = $em->getRepository("ReunionBundle:Commentaire")->findBy(array('flagDeleted' => false , 'level' => 2 , 'parentCommentId' => $commentaire ));


        $tabToJson = array();
        foreach ($reponse as $c) {
            
            $tab = array(
                'texte' => htmlentities($c->getTexte()),
                'auteur' => htmlentities($c->getAuteur()),
                'datePublication' => htmlentities($c->getDatePublication()->format('d-m-Y H:i:s')),
                'idQuestion' => $commentaire->getId(),
                'idCommentaire' => $c->getId(),
                'delete' => '<a href="#"><span class="glyphicon glyphicon-trash icon_delete_' . $c->getId() . '" onClick="deleteCommentaire(' . $c->getId() . ',' . $c->getQuestion()->getId() . ')"></span></a>',
                'modifier' => '<a href="#"><span class="glyphicon glyphicon-edit" title="Modifier" onClick="modifierCommentaire(' . $c->getId() . ',' . $c->getQuestion()->getId() .')"></span></a>'
            );
            $tabToJson[] = $tab;
        }
        //stats
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($tabToJson);
        }

        return new JsonResponse($tabToJson);
    }

/**
*  Page des commentaires d'une question
*   @param Question $question
*   @return render page listCommentaireAdmin
*/
    public function questionListCommentaireAction(Question $question) {
        $event = $question->getEvent();
        return $this->render("ReunionBundle:Admin:listCommentaireAdmin.html.twig", array('event' => $event , 'question' => $question));
    }

/**
*  Page des reponses aux commentaires
*   @param Commentaire $commentaire
*   @return render page listReponsesAdmin
*/
    public function commentaireListReponseAction(Commentaire $commentaire) {
        $question = $commentaire->getQuestion();
        $event = $question->getEvent();
        return $this->render("ReunionBundle:Admin:listReponsesAdmin.html.twig", array('event' => $event , 'question' => $question, 'commentaire' => $commentaire));
    }

     /**
     *modification commentaire
     *   @param Commentaire $commentaire
     */

    public function modificationCommentaireAction(Commentaire $commentaire) {

        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
        }
        return new Response($commentaire->getTexte());
    }

    /**
     * save modification commentaire
     */
    public function saveModificationCommentaireAction() {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $idCommentaire = $request->request->get('idCommentaire');
            $texte = $request->request->get('texte');
            $c = $em->getRepository("ReunionBundle:Commentaire")->findById($idCommentaire);
            $c[0]->setTexte($texte);
            $em->flush();
            //appeler fayeClient
            $data = array(
                'datas' => array(
                'idCommentaire' => $idCommentaire,
                'texte' => $texte,
            ));
            $this->subFayeClientUpdateContentCommentaire($data);     
            
        }
        return new JsonResponse(
                array(
                    'statut' => 'ok'
                )
        );
    }
   /**
    *  tunnel FayeClient update content commentaire 
    *   @param $data 
    */

    public function subFayeClientUpdateContentCommentaire($data){
        $this->fayeClient("/updateContentCommentaire", $data);
    }

}
