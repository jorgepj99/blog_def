<?php
/**
 * Created by PhpStorm.
 * User: linux
 * Date: 23/01/19
 * Time: 17:56
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;

class HomeController extends AbstractController
{
    /**
     * @Route("/",name="app_homepage")
     */
    public function homepage(){
        $user= $this->getUser();
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        $max=sizeof($posts);
        $res=$max%3;
        if($res==0){
            $max=round($max/3);
            $max=$max-1;
        }else{
            $max=round($max/3);
        }

        return $this->render('home/home.html.twig', [
            'posts' => $posts,'user'=>$user,'length'=>$max]);
    }
}