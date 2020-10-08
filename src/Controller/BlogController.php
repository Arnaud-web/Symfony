<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use App\Repository\CategoryRepository;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request ;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Helper\NewEditArticle;
// use App\Helper\NewEditArticle\NewEditArticle as NewEditArticleNewEditArticle;
// use Doctrine\ORM\Query\Expr\From;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class BlogController extends AbstractController
{
    private $categories;
    protected $id;
    protected  $numberArticle;

    public function  __construct (CategoryRepository $repoCat,NewEditArticle $nea) {
        $this->categories = $repoCat->findAll();
        $this->nea = $nea;

    }
   
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
    /**
     * @Route("/blog/profil/{id}/",name = "blog_profil")
     */
    public function profil($id , ArticleRepository $repo){
        // dd($id);
        $numberArticle = $this->nea->NumberOfMyArticle($id);
        
        $articles = $repo->findByUserId($id);
        // dd($articles);
        return $this->render('blog/profil.html.twig', [
            'articles'=>$articles,
            'id'=> $id,
            'numberArticle'=>$numberArticle
        ]);
    }

    /**
    * @Route("/", name="home")
    * @Route("/{name}-{categorie}/filtre", name="home_category")
    */
    public function home($categorie = null, ArticleRepository $repo){
        $numberArticle = null;

        if($categorie){
            $articles = $repo->findByCategorieId($categorie);
        } else{
            $articles = $repo->findAll();
        }
        return $this->render('blog/home.html.twig',[
            'articles'=>$articles,
            'categories'=>$this->categories,
            'numberArticle'=>$numberArticle

        ]);
    }
    /**
     * @Route("blog/new", name = "blog_create")
     * @Route("blog/{id}/edit", name= "blog_edit")
     */
    public function form (NewEditArticle $save , UserRepository $repo,  Article $article = null , Request $request , ObjectManager $manager ){
        $numberArticle = '';
        if($_GET){
            if(isset($_GET['id'])) $numberArticle = $this->nea->NumberOfMyArticle($_GET['id']);
            if(isset($_GET['id_u'])) $numberArticle = $this->nea->NumberOfMyArticle($_GET['id_u']);
        }
        if (!$article){
            $article = new Article;
            $form = $this->createForm(ArticleType::class, $article);
            $form->add('image', FileType::class, array(
                'label' => 'Votre photo d\'ulustration'
            ));
        }
        else{
            $form = $this->createForm(ArticleType::class, $article);
            $form->add('image_maj', FileType::class, array(
                'label' => 'Changer votre photo d\'ulustration',
                'required' => false,
            ));
        } 
        // $form->handleRequest($request);
       
        // if ($form->isSubmitted() && $form->isValid()){

           $verification = $save->CreateArticle($article,$request,$form);
           
           if($verification){
            return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);

           }
            // dd($article);
        //     if(!$article->getId()){
        //         $image = $article->getImage();
        //         $article->setCreatedAt(new \DateTime());
        //         $imageName = md5(uniqid()).'.'.$image->guessExtension();
        //         $image->move($this->getParameter('upload_image_article_directory'),$imageName);
        //         $article->setImage($imageName);
               
        //     }
        //     if ($article->getId()) {
        //         if($article->image_maj){
        //             $image = $article->image_maj;
        //             $imageName = md5(uniqid()).'.'.$image->guessExtension();
        //             $image->move($this->getParameter('upload_image_article_directory'),$imageName);
        //             $article->setImage($imageName);
        //         }
        //     }
        //     // $image = $article->getImage();
        //     $article->setUser($repo->find($article->authorId));
        //     $manager->persist($article);
        //     $manager->flush();
            // return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);
        // }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null,
            'article' => $article,
            'numberArticle'=> $numberArticle
        ]);
    }

    /**
     * @Route("/blog/{id}", name = "blog_show")
     */
    public function show($id, ArticleRepository $repoA ,UserRepository $repo, Request $request,Article $article, ObjectManager $manager){ // na kou Article $article;
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $nombreArticle = count($repoA->findAll());
        $articleNexts = [];
        for ($i=0; $i <3 ; $i++) { 
            $val = rand(1,$nombreArticle);
            $articleNext = $repoA->find($val);
            $articleNexts[$i] = $articleNext;
        }
        // dd($articleNexts);
        if(!$articleNexts){
            $articleNexts = $repoA->find($nombreArticle);
        }
        // dd($articleNext);
        if ($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article)
                    ->setUser($repo->find($comment->authorId));
                    // dd($comment);
            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id'=>$article->getId()]);
        }
        return $this->render('blog/show.html.twig',[
            'article'=>$article,
            'articleNexts' => $articleNexts,
            'commentForm' => $form->createView(),
            'numberArticle'=> ''  
        ]);
    }
    // public function show(ArticleRepository $repo, $id,){ // na kou Article $article;
    //     $article = $repo->find($id);                    // tsis tsony 
    //     return $this->render('blog/show.html.twig',[
    //         'article'=>$article
    //     ]);
    // }

    /**
     * @Route("/test")
     */
    public function test(SessionInterface $session){
            dd($session);
    }

    
}
