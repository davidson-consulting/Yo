<?php
namespace Sab\ReunionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sab\ReunionBundle\Entity\Commentaire;
use Sab\ReunionBundle\Form\CommentaireType;
use Sab\ReunionBundle\Entity\Question;
use Symfony\Component\HttpFoundation\JsonResponse;


class CommentaireController extends Controller
{

	/**
     * add comment (or response to comment) 
     * @param $question_id,$parentCommentId
     * @return renderResponse
     */
	public function addCommentaireAction($question_id,$parentCommentId)
	{	
		$em = $this->getDoctrine()->getManager();

		$question = $this->getQuestion($question_id);

		$commentaireRepository = $em->getRepository("ReunionBundle:Commentaire");

		$countCommentaire = $commentaireRepository->countCommentaireForQuestionLevel1($question_id);

        foreach ($countCommentaire as $count) {
            $nbre_commentaire = $count;
        }


		$commentaire = new Commentaire();

		$form = $this->container->get('form.factory')->create(new CommentaireType(), $commentaire);

		$request = $this->container->get('request');

		if ($request->getMethod() == 'POST')
		{
			$form->bind($request);
		}

		if ($form->isvalid())
		{

			// $em = $this->getDoctrine()->getManager();
			// $em->persist($commentaire);
			$commentaire->setQuestion($question);
			$commentaire->setFlagDeleted(false);
			
			if ($parentCommentId != 0){
				$commentaire->setParentCommentId($parentCommentId);
				$commentaire->setLevel(2);

			$question->addCommentaire($commentaire);
			}else{
				$commentaire->setParentCommentId(0);
				$commentaire->setLevel(1);
			}
			
			$em->persist($commentaire);
			$em->flush();
			$this->addFlash('notice', 'Votre commentaire a été ajouté avec succès');

			$this->subAddCommentaire($commentaire);
			$this->subFayeClientRefresh($question_id);
			$this->subFayeCountCommentaire($nbre_commentaire + 1, $question_id);

			return $this->redirectToRoute('user_dashboard');
			
		}

		return $this->container->get('templating')->renderResponse('ReunionBundle:User:form_commentaire.html.twig', array('form' => $form->createView(), 
			'question_id' => $question_id,
			'parentCommentId' => $parentCommentId
   	));
    
	}
	
 	/**
     * delete commentaire 
     * @param Commentaire $commentaire
     */
	public function deleteCommentaireAction(Commentaire $commentaire)
	{
		$em = $this->getDoctrine()->getManager();

		$data = array('id' => $commentaire->getId());
		$this->fayeClient("/deleteCommentaire", $data);

		$commentaire->setFlagDeleted(True);

		// $em ->remove($commentaire);
		$em ->flush();
	
		return new JSONResponse ("OK");
	}

	/**
     * get question  
     * @param $question_id
     * @return $question
     */
	public function getQuestion($question_id)
	{
		$em = $this->getDoctrine()->getManager();
	
		$question = $em->getRepository('ReunionBundle:Question')->find($question_id);

		if (!$question)
		{
			throw $this->createNotFoundException('Impossible de trouve la question');
		}
		return $question;
	}
	

	public function subAddCommentaire($commentaire)
	{
		$data = array('datas' => array(
			'id' => $commentaire->getId(),
			'texte' => htmlentities($commentaire->getTexte()),
			'auteur' => htmlentities($commentaire->getAuteur()),
			'datePublication' => $commentaire->getDatePublication(),
			'idQuestion' => $commentaire->getQuestion()->getId(),
			'idQuestion' => $commentaire->getQuestion()->getId(),
			'parentCommentId' => $commentaire->getParentCommentId(),
			'level' => $commentaire->getLevel()
			));
	
		$this->fayeClient("/addCommentaire", $data);
		
	}

	public function fayeClient($channel, $data)
	{
      	$faye = $this->container->get('sab.reunion.faye.client');
        $faye->send($channel, $data);
    }

    public function subFayeClientRefresh($idQuestion) {
        $data = array(
            'idQuestion' => $idQuestion
        );
        $this->fayeClient("/refreshCommentaire", $data);
    }

    public function subFayeCountCommentaire($nbre_commentaire, $idQuestion) {
    	$data = array(
            'nbre_commentaire' => $nbre_commentaire,
            'idQuestion' => $idQuestion,
        );
        $this->fayeClient("/updateCountCommentaire", $data);
    }

}