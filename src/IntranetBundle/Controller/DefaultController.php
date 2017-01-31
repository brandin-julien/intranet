<?php

/*
 * Dans le parent casdade remove
 */

namespace IntranetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

//use IntranetBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {

        $user = $this->getUser();
        //$em = $this->getDoctrine()->getManager();

        if($user == null) {
            return $this->redirectToRoute("fos_user_security_login");
        }

        $matters = $user->getMatters();
        return $this->render('IntranetBundle:Default:index.html.twig', array(
            'matters' => $matters,
        ));
    }

    /**
     * @Route("promote", name="promote")
     */
    public function promoteAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();

            $role = $request->request->get("role");
            $useId = $request->request->get("user");

            $rolesArray = [
              0 => "ROLE_USER",
              1 => "ROLE_ADMIN",
              2 => "ROLE_SUPER_ADMIN"
            ];

            if(in_array($role, $rolesArray)){
                $user = $em->getRepository('IntranetBundle:User')->find($useId);

                $user->setRoles(array(
                    $role
                ));

                $em->flush();
                $session->getFlashBag()->add('success', 'Les droits de l\'utilisateurs ont bien été modifiées');
            }else{
                $session->getFlashBag()->add('error', 'Une erreur a eu lieux');
            }
        }
        $users = $this->getDoctrine()->getRepository("IntranetBundle:User")->findAll();
        return $this->render('IntranetBundle:Default:promote.html.twig', array("users" => $users));
    }

}
