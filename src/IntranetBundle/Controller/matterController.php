<?php

namespace IntranetBundle\Controller;

use IntranetBundle\Entity\matter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Matter controller.
 *
 * @Route("matter")
 */
class matterController extends Controller
{
    /**
     * Lists all matter entities.
     *
     * @Route("/", name="matter_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $matters = $em->getRepository('IntranetBundle:matter')->findAll();

        $user = $this->getUser();

        $mattersUser = $user->getMatters();

        return $this->render('matter/index.html.twig', array(
            'matters' => $matters,
            'mattersUser' => $mattersUser,
        ));
    }

    /**
     * Creates a new matter entity.
     *
     * @Route("/new", name="matter_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $matter = new Matter();
        $form = $this->createForm('IntranetBundle\Form\matterType', $matter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($matter);
            $em->flush($matter);

            return $this->redirectToRoute('matter_show', array('id' => $matter->getId()));
        }

        return $this->render('matter/new.html.twig', array(
            'matter' => $matter,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a matter entity.
     *
     * @Route("/{id}", name="matter_show")
     * @Method("GET")
     */
    public function showAction(matter $matter)
    {
        $users = $matter->getUsers();

        //var_dump($users);exit();

        $em = $this->getDoctrine()->getManager();

        $grades = $em->getRepository('IntranetBundle:Grade')->findByMatter($matter);

        $deleteForm = $this->createDeleteForm($matter);

        return $this->render('matter/show.html.twig', array(
            'matter' => $matter,
            'users' => $users,
            'grades' => $grades,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing matter entity.
     *
     * @Route("/{id}/edit", name="matter_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, matter $matter)
    {
        $deleteForm = $this->createDeleteForm($matter);
        $editForm = $this->createForm('IntranetBundle\Form\matterType', $matter);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('matter_edit', array('id' => $matter->getId()));
        }

        return $this->render('matter/edit.html.twig', array(
            'matter' => $matter,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a matter entity.
     *
     * @Route("/{id}", name="matter_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, matter $matter)
    {
        $form = $this->createDeleteForm($matter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($matter);
            $em->flush($matter);
        }

        return $this->redirectToRoute('matter_index');
    }

    /**
     * Creates a form to delete a matter entity.
     *
     * @param matter $matter The matter entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(matter $matter)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('matter_delete', array('id' => $matter->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @Route("/inscription/{id}", name="matter_inscription")
     */
    public function matterInscription(Request $request, matter $matter){

        $matter->addUser($this->getUser());

        $em = $this->getDoctrine()->getManager();

        try{
            $em->flush($matter);
            $message = "Félicitation, vous etes maintenant inscrit à ce cours";
        } catch(\Exception $e){
            $message = "Une erreur a eu lieu, peut-etre déja inscrit à ce cours";
        }
        return $this->render('IntranetBundle:Default:message.html.twig', array(
            'message' => $message,
        ));
    }

}
