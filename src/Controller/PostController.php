<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Tag;
use App\Form\CommentEditType;
use App\Form\CommentType;
use App\Form\PostEditType;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use App\Entity\User;

class PostController extends AbstractController
{
    /**
     * @Route("/post/new_post", name="new_post")
     */
    public function newPost(Request $request)
    {
        $user= $this->getUser();
        //crear nuevo objeto Post
        $post= new Post();
        $post->setUser($user);
        $post->setAuthor($user->getUsername());
        $createat=$post->getCreateAt();
        //crear formulario
        $form=$this->createForm(PostType::class,$post);


        //handle the request
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){//si el formulario es enviado y valiado...
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($post);
            //flush to DB
            $entityManager->flush();
            $this->addFlash('success','Post creado correctamente');
            return $this->redirectToRoute('user_posts',array('id'=>$user->getId()));
        }

        //render the form
        return $this->render('post/post.html.twig', [
            'user'=>$user,'createat'=>$createat->format('Y-m-d H:i:s'),'form' => $form->createView()]);
    }
    /**
     * @Route("/post/ver_posts/{id}", name="user_posts")
     */
    public function Ver_Posts($id,Request $request)
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findBy(array('user'=>$id));
        $max=sizeof($posts);
        $max=round($max/3);
        return $this->render('post/ver_posts.html.twig', [
            'posts' => $posts,'user'=>$id,'length'=>$max]);
    }
    /**
     * @Route("/post/index/{id}", name="ver_post")
     */
    public function Post($id,Request $request)
    {
        $posts= $this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $post = $posts[0];
        $tags=$post->getTags();
        $comments= $this->getDoctrine()->getRepository(Comment::class)->findBy(array('post'=>$post->getId()));
        $comment=new Comment();
        $form=$this->createForm(CommentType::class,$comment);
        //handle the request
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){//si el formulario es enviado y valiado...
            $comment=$comment->setUser($this->getUser());
            $comment=$comment->setPost($post);
            $entityManager=$this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            //flush to DB
            $entityManager->flush();
            $this->addFlash('success','Post creado correctamente');
            return $this->redirectToRoute('ver_post',array('id'=>$post->getId()));
        }
        $max=sizeof($comments);
        $max=round($max/3);
        return $this->render('post/index.html.twig', [
            'post' => $post,'comments'=>$comments,'length'=>$max,'user'=>$this->getUser()->getId(),'tags'=>$tags,'form' => $form->createView()]);
    }
    /**
     * @Route("/post/edit_post/{id}", name="editar_post")
     */
    public function edit_Post($id,Request $request)
    {
        $user= $this->getUser();
        //crear nuevo objeto Post
        $posts= $this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $post = $posts[0];
        $user_post=$post->getUser();
        if($user==$user_post){
            //crear formulario
            $form=$this->createForm(PostEditType::class,$post);
            //handle the request
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //si el formulario es validado...
                $entityManager=$this->getDoctrine()->getManager();
                $entityManager->persist($post);
                //flush to DB
                $entityManager->flush();
                $this->addFlash('success','Post editado correctamente');
                return $this->redirectToRoute('user_posts',array('id'=>$user->getId()));
            }
            return $this->render('post/edit_post.html.twig', [
                'post' => $post, 'user'=>$user,'form' => $form->createView()]);
        }else{
            return $this->redirectToRoute('user_posts',array('id'=>$user->getId()));
        }

    }
    /**
     * @Route("/post/post_del/{id}", name="del_post")
     */
    public function del_Post($id)
    {
        $em=$this->getDoctrine()->getManager();
        $post=$this->getDoctrine()->getRepository(Post::class)->findBy(array('id'=>$id));
        $user= $this->getUser();
        $em->remove($post[0]);
        $em->flush();
        return $this->redirectToRoute('user_posts',array('id'=>$user->getId()));
    }
    /**
     * @Route("/post/edit_comment/{id}", name="editar_comment")
     */
    public function edit_Com($id,Request $request)
    {

        $user= $this->getUser();
        //crear nuevo objeto Post
        $comments= $this->getDoctrine()->getRepository(Comment::class)->findBy(array('id'=>$id));
        $comment = $comments[0];
        $user_com=$comment->getUser();
        if($user==$user_com){
            //crear formulario
            $form=$this->createForm(CommentEditType::class,$comment);
            //handle the request
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                //si el formulario es validado...
                $entityManager=$this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                //flush to DB
                $entityManager->flush();
                $this->addFlash('success','Post editado correctamente');
                return $this->redirectToRoute('ver_post',array('id'=>$comment->getPost()->getId()));
            }
            return $this->render('post/edit_comment.html.twig', [
                'comment' => $comment,'form' => $form->createView()]);
        }else{
            return $this->redirectToRoute('user_posts',array('id'=>$user->getId()));
        }

    }
    /**
     * @Route("/post/comment_del/{id}", name="del_comment")
     */
    public function del_Com($id)
    {
        $em=$this->getDoctrine()->getManager();
        $comment=$this->getDoctrine()->getRepository(Comment::class)->findBy(array('id'=>$id));
        $post=$comment[0]->getPost();
        $em->remove($comment[0]);
        $em->flush();
        return $this->redirectToRoute('ver_post',array('id'=>$post->getId()));
    }
}
