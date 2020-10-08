<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\RegistrationType;
use App\Helper\NewEditArticle;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    protected $nea;
    protected $security;

    public function __construct(NewEditArticle $nea ,Security $security )
    {
        $this->security = $security ;
        $this->nea = $nea;
    }
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
     * @Route("/{id}myprofil", name = "user_profil")
     * @Route("/{option}{id}/profil/update", name = "user_image_update")
     */

     public function UserProfil($id,$option = false , UserRepository $repo, Request $request, ObjectManager $manager,ArticleRepository $repoArticle){
        dd($this->security->getUser());
        $articles = $repoArticle->findByUserId($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $image_update = "image_update";
        $image_couverture = "image_couverture";
        $numberArticle = $this->nea->NumberOfMyArticle($id);
        // dd($numberArticle);
        // dd($request);
         
                $formImageProfil = $this->createFormBuilder([
                    'method'=>'POST'
                ])
                    ->add('image_profil_update', FileType::class , [
                        'label'=>'Changer le photo de profil'
                    ])
                    ->add('option', HiddenType::class)
                    ->add('id', HiddenType::class)

                    ->getForm();
                
            
                $formImageProfil->handleRequest($request);
                if ($formImageProfil->isSubmitted() && $formImageProfil->isValid()) {
                    $data = $formImageProfil->getData();
                    $imageProfil = $data['image_profil_update'];
                    // dd($data['id']);
                    $user = $repo->find($data['id']);
                    $imageName = md5(uniqid()) . '.' . $imageProfil->guessExtension();
                    $imageProfil->move($this->getParameter('upload_image_user_profil_directory'), $imageName);
                    $user->setImageProfil($imageName);
                    $numberArticle = $this->nea->NumberOfMyArticle($user->getId());
                    // dd($numberArticle);
                    $manager->persist($user);
                    $manager->flush();


                    return $this->render('user/myprofil.html.twig', [
                        'formImageProfil' =>  false,
                        'formImageCouverture'=> null,
                        'numberArticle' => $numberArticle
                    ]);
                }
                if($option == $image_update ){
                    return $this->render('user/myprofil.html.twig', [
                        'formImageProfil' =>  $formImageProfil->createView(),
                        'formImageCouverture'=> null,
                        'option'=> $image_update ,
                        'numberArticle' => $numberArticle
                    ]);
                    }
                

                $formImageCouverture = $this->createFormBuilder([
                    'method'=>'POST'
                ])
                    ->add('image_couverture', FileType::class , [
                        'label'=>'Changer le Photo de couverture'
                    ])
                    ->add('id', HiddenType::class)
                    ->getForm();

                $formImageCouverture->handleRequest($request);
                if ($formImageCouverture->isSubmitted() && $formImageCouverture->isValid()) {
                    $data = $formImageCouverture->getData();
                    $imageCouverture = $data['image_couverture'];
                    // dd($data['id']);
                    $user = $repo->find($data['id']);
                    $imageName = md5(uniqid()) . '.' . $imageCouverture->guessExtension();
                    $imageCouverture->move($this->getParameter('upload_image_user_couverture_directory'), $imageName);
                    $user->setImageCouverture($imageName);
                    $numberArticle = $this->nea->NumberOfMyArticle($user->getId());
                    // dd($numberArticle);
                    $manager->persist($user);
                    $manager->flush();


                    return $this->render('user/myprofil.html.twig', [
                        'articles'=>$articles,
                        'commentForm' =>$form->createView(),
                        'formImageProfil' =>  false,
                        'formImageCouverture'=> null,
                        'numberArticle' => $numberArticle
                    ]);
                }
                if($option == $image_couverture ){
                    return $this->render('user/myprofil.html.twig', [
                        'articles'=>$articles,
                        'commentForm' =>$form->createView(),
                        'formImageCouverture' =>  $formImageCouverture->createView(),                        'formImageProfil' =>  false,
                        'formImageProfil' =>  false,
                        'option'=> $image_couverture ,
                        'numberArticle' => $numberArticle
                    ]);

            }

         

       
        return $this->render('user/myprofil.html.twig', [
            'articles'=>$articles,
            'commentForm' =>$form->createView(),
            'formImageProfil' =>  false,
            'formImageCouverture'=> null,
            'numberArticle' => $numberArticle
        ]);
     }




      /**
     * @Route("/{id}/modifier/profil" , name="security_modif")
     */
    public function ProfilModif($id,UserRepository $repo) {
        $user = $repo->find($id);
        // dd($user);
        $numberArticle = $this->nea->NumberOfMyArticle($id);
        $form = $this->createForm(RegistrationType::class , $user);
        // dd($numberArticle);
        return $this->render('security/registration.html.twig',[
            'form'=> $form->createView(),
            'numberArticle' => $numberArticle
        ]);

    }


   


}
