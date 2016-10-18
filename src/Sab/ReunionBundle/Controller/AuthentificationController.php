<?php

namespace Sab\ReunionBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Description of AuthentificationController
 *
 * @author sabar Guechoud <guechoudsaby@gmail.com>
 */
class AuthentificationController extends QuestionController {
    /**
     * Rediriger vers la bonne url selon le role de l'utilisateur 
     */
    public function checkAction() {

        $response = new Response();
        $request = $this->getRequest();

        $user = $this->container->get('security.context')->getToken()->getUser();

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->redirect($this->generateUrl('_list_event'));
        }

        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            return $this->redirect($this->generateUrl('user_dashboard'));
        }
        return $this->redirect($this->generateUrl('fos_user_security_login'));
    }

  

}
