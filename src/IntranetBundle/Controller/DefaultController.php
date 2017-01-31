<?php

/*
 * Dans le parent casdade remove
 */

namespace IntranetBundle\Controller;

use IntranetBundle\Entity\Grade;
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
        $em = $this->getDoctrine()->getManager();

        if($user == null) {
            return $this->redirectToRoute("fos_user_security_login");
        }

        $matters = $user->getMatters();
        $grades = $em->getRepository('IntranetBundle:Grade')->findByUser($user);

        $average = $this->getAverage($grades);

        return $this->render('IntranetBundle:Default:index.html.twig', array(
            'matters' => $matters,
            'grades' => $grades,
            'average' => $average,
        ));
    }

    function getAverage($grades){

        if ($grades == null)
            return null;

        $total = 0;
        $numberOfIteration = 0;
        foreach ($grades as $grade){
            $total += $grade->getGrade();
            $numberOfIteration++;
        }
        return $total/$numberOfIteration;
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

    

    /**
     * @Route("test", name="test")
     */
    public function testAction(Request $request)
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $grade = $em->getRepository('IntranetBundle:Grade')->findByUser($user);


        var_dump($grade);

        /*
        $matters = $user->getMatters();

        $matter = $matters[0];

        var_dump($matter->getId());

        $grade = new Grade();
        $grade->setGrade(20);
        $grade->setComment("bravo");

        $grade->setMatter($matter);
        $grade->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($grade);
        $em->flush($grade);

        var_dump($grade);
*/
        var_dump("test");
        exit();
    }
}
