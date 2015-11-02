<?php

namespace Sab\ReunionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Sab\ReunionBundle\Entity\Event;
use Sab\ReunionBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sab\ReunionBundle\Form\EventType;

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
        $question = $event->getQuestion();
        $tabToJson = array();
        foreach ($question as $q) {
            $tab = array(
                'contenu' => $q->getContenu(),
                'auteur' => $q->getAuteur(),
                'nbLike' => $q->getNbLike(),
                'nbDislike' => $q->getNbDislike(),
                'datePublication' => $q->getDatePublication()->format('d-m-Y H:i:s'),
                'idEvent' => $event->getId(),
                'idQuestion' => $q->getId(),
                'statutFocus' => $q->getIsfocus(),
                'fav' => '<a href="#"><span class="glyphicon glyphicon-star-empty icon_focus_' . $q->getId() . '"></span></a>'
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
     * S'abonner au tunnel "/focusQuestion" à fin d'affiher la question répondu pour tous les clients 
     * @param array $data
     */
    public function subFayeClientFocusQuestion($data) {
        $this->fayeClient("/focusQuestion", $data);
    }

    
    /**
     * Récuperer les clients, ensuite envoyer les données au tunnel écouté !! 
     * @param string $channel => "/focusQuestion"
     * @param array $data
     */
    public function fayeClient($channel, $data) {
        $faye = $this->container->get('sab.reunion.faye.client');
        $faye->send($channel, $data);
    }

}
