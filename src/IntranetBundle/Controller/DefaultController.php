<?php

/*
 * Dans le parent casdade remove
 */

namespace IntranetBundle\Controller;

use IntranetBundle\Entity\Grade;
use IntranetBundle\Entity\matter;
use IntranetBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


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
    * @Route("assignment", name="assignment")
     */
    public function assignmentAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();

            $matter = $request->request->get("matter");
            $useId = $request->request->get("user");

            $matter = $em->getRepository('IntranetBundle:matter')->find($matter);
            $user = $em->getRepository('IntranetBundle:User')->find($useId);

            $matter->addUser($user);

            try{
                $em->flush();
                $session->getFlashBag()->add('success', 'Prof ajouté a la matiere');
            } catch(\Exception $e){
                $session->getFlashBag()->add('error', 'Une erreur a eu lieux');
            }

                /*
                foreach ($matter->getUsers() as $mat){
                    var_dump($mat->getId() == $matter->getId());
                }
                die;

                if(in_array($user, $matter->getUsers())){
                */
            /*}else{
                $session->getFlashBag()->add('error', 'Une erreur a eu lieux');
            }*/
        }

        $matters = $this->getDoctrine()->getRepository("IntranetBundle:matter")->findAll();
        $users = $this->getDoctrine()->getRepository("IntranetBundle:User")->findByRole("ROLE_ADMIN");
        return $this->render('IntranetBundle:Default:assignment.html.twig', array(
            "users" => $users,
            "matters" => $matters
        ));
    }

    /**
     * @Route("graduation", name="graduationIndex")
     */
    public function graduationIndexAction()
    {
        $matters = $this->getUser()->getMatters();
        return $this->render('IntranetBundle:Default:graduationIndex.html.twig', array(
            "matters" => $matters
        ));
    }

    public function authorisedTeacher($matter, $mattersUser){
        foreach ($mattersUser as $mat){
            if($mat == $matter){
                return true;
            }
        }
        return false;
    }

    /**
     * @Route("graduation/{matter}", name="graduationMatter")
     */
    public function graduationAction(Request $request ,matter $matter)
    {
        $mattersUser = $this->getUser()->getMatters();
        $users = $matter->getUsers();

        if(!$this->authorisedTeacher($matter, $mattersUser))
            return $this->render('IntranetBundle:Default:message.html.twig', array(
                "message" => "Vous n'etes pas le professeur de cette matiere"
            ));

        return $this->render('IntranetBundle:Default:graduationMatter.html.twig', array(
            "users" => $users,
            "matter" => $matter
        ));
    }

    public function registerStudent($user, $users){

        foreach ($users as $student){
            if($student == $user)
                return true;
        }
        return false;
    }

    /**
     * @Route("graduation/{matter}/{user}", name="graduationUser")
     */
    public function graduationUserAction(Request $request ,matter $matter, User $user)
    {
        /*
        var_dump($user);
        exit();
        */

        //$em = $this->getDoctrine()->getManager();

        //$grade = $em->getRepository('IntranetBundle:Grade')->findByUser($user);

        $mattersUser = $this->getUser()->getMatters();
        $users = $matter->getUsers();

        if(!$this->authorisedTeacher($matter, $mattersUser))
            return $this->render('IntranetBundle:Default:message.html.twig', array(
                "message" => "Vous n'etes pas le professeur de cette matiere"
            ));

        if(!$this->authorisedTeacher($user, $users))
            return $this->render('IntranetBundle:Default:message.html.twig', array(
                "message" => "eleve non inscrit à cette matiere"
            ));

        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('IntranetBundle:Grade')
        ;

        $results = $repository->findByUserAndMatter($user, $matter);

        if (!empty($results))
            $grade = $results[0];
        else
            $grade = new Grade();

        /*
        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT * FROM `grade` WHERE  user_id = :user_id and matter_id = :mater_id");
        $statement->bindValue('user_id', $user->getId());
        $statement->bindValue('mater_id', $matter->getId());
        $statement->execute();
        $results = $statement->fetchAll();
        var_dump($results);
        */
        //exit();

        $form = $this->createForm('IntranetBundle\Form\GradeType', $grade);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $session = $request->getSession();

            $grade->setMatter($matter);
            $grade->setUser($user);

            $em->persist($grade);
            $em->flush($grade);

            $session->getFlashBag()->add('message', 'élève noté');

            return $this->redirectToRoute('graduationMatter', array(
                'Request' => $request,
                'matter' => $matter->getId()
            ));
        }

            return $this->render('IntranetBundle:Default:graduationUser.html.twig', array(
            'grade' => $grade,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("student", name="student")
     */
    public function studentAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('IntranetBundle:User')->findAll();

        return $this->render('IntranetBundle:Default:student.html.twig', array(
            'users' => $users
        ));

    }

    /**
     * @Route("teacher", name="teacher")
     */
    public function teacherAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('IntranetBundle:User')->findAll();

        return $this->render('IntranetBundle:Default:teacher.html.twig', array(
            'users' => $users
        ));
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
