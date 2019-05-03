<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\EditType;
use App\Form\PassType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/register",name="app_register")
     */

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        $user = new User();
        $user->setRoles(['ROLE_USER']);
        //marca el usuario activo o inactivo
        $user->setIsActive(true);
        $form=$this->createForm(UserType::class,$user);

        $form->handleRequest($request);
        $error=$form->getErrors();

        if($form->isSubmitted() && $form->isValid()){
            //encriptamos el password y lo guardamos como campo
            $password=$passwordEncoder->encodePassword($user,$user->getPlainPassword());
            $user->setPassword($password);//si modifica el campo $user, el que irá a la bd
            //para manejo de las entidades
            $entityManager=$this->getDoctrine()->getManager();
            //entidad-orm-bd
            //persistimso la información del formulario
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash(
                'success','User created'
            );
            return $this->redirectToRoute('app_homepage');

        }
        //renderizar formulario
        return $this->render('user/regform.html.twig',[
            'error'=>$error,
            'form'=>$form->createView()
        ]);

    }

    /**
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @Route("/login",name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authUtils){
        $error=$authUtils->getLastAuthenticationError();//guardaremos el último errore de la autentificación
        //last username
        $lastUsername=$authUtils->getLastUsername();
        return $this->render('user/login.html.twig',[
            'error'=>$error,
            'last_username'=>$lastUsername
        ]);
    }
    /**
     * @Route("/loged", name="app_admin_user")
     */
    public function loged(){
        $user= $this->getUser();
        return $this->render('user/loged.html.twig',[
            'user'=>$user]);
    }
    /**
     * @Route("/editado", name="app_admin_user_editado")
     */
    public function editar(Request $request){
        $user= $this->getUser();
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
            return  $this->redirectToRoute('app_homepage');
        }
        return $this->render('user/editado.html.twig',[
            'user'=>$user,'error'=>$error,'form'=>$form->createView()]);
    }
    /**
     * @Route("/pass", name="app_admin_user_pass")
     */
    public function pass(Request $request){
        $user= $this->getUser();
        $form=$this->createForm(PassType::class,$user);
        $form->handleRequest($request);
        $error=$form->getErrors();
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success','El usuario ha sido editado'
            );
            return $this->redirectToRoute('app_homepage');
        }
        return $this->render('user/editado.html.twig',[
            'user'=>$user,'error'=>$error,'form'=>$form->createView()]);
    }
    /**
     * @Route("/user/usersdelete", name="app_admin_user_delete")
     */
    public function deleteUser()
    {
        $em=$this->getDoctrine()->getManager();
        $user= $this->getUser();
        $em->remove($user);
        $em->flush();
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        //marca el usuario activo o inactivo
        $user->setIsActive(true);
        $form=$this->createForm(UserType::class,$user);;
        return $this->render('user/regform.html.twig',['form'=>$form->createView()]);
        return $this->redirectToRoute('app_register');
    }
    /**
     * @Route("/logout",name="app_logout")
     */
    public function logout(){
        $this->redirectToRoute('app_homepage');
    }
}
