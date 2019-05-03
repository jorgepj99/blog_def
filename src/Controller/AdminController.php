<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\User;
use App\Form\EditType;
/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="app_admin_users")
     */
    public function listUsers()
    {
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();
        return $this->render('admin/users.html.twig',[
            'users'=>$users]);
    }

    /**
     * @Route("/admin/usersdelete/{id}", name="app_admin_users_delete")
     */
    public function deleteUser($id,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $users= $this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        $user=$users[0];
        $em->remove($user);
        $em->flush();
        $this->addFlash(
            'success','El usuario ha sido borrado'
        );
        return $this->redirectToRoute('app_admin_users');
    }
    /**
     * @Route("/admin/edita/{id}", name="app_admin_users_edita")
     */
    public function editar($id,Request $request){
        $users= $this->getDoctrine()->getRepository(User::class)->findBy(array('id'=>$id));
        $user=$users[0];
        $form=$this->createForm(EditType::class,$user);
        $form->handleRequest($request);
        $error=$form->getErrors();
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success','El usuario ha sido editado'
            );
            return  $this->redirectToRoute('app_admin_users');
        }
        return $this->render('admin/edita.html.twig',[
            'user'=>$user,'error'=>$error,'form'=>$form->createView()]);
    }
}
