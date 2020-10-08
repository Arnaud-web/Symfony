<?php 

namespace App\Helper;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class NewEditArticle extends AbstractController{

    protected $article;
    protected $form;
    protected $manager;
    protected $repo;

    public function __construct( ObjectManager $manager,UserRepository $repo , ArticleRepository $repoArticle){
       $this->manager = $manager;
       $this->repo = $repo;
       $this->repoArticle = $repoArticle;
    }
    

    public function CreateArticle($article,$request,$form){ 
        $this->article = $article;
        $manager = $this->manager;
        $repo = $this->repo;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $image = $article->getImage();
                $article->setCreatedAt(new \DateTime());
                $imageName = md5(uniqid()).'.'.$image->guessExtension();
                $image->move($this->getParameter('upload_image_article_directory'),$imageName);
                $article->setImage($imageName);
               
            }
            if ($article->getId()) {
                if($article->image_maj){
                    $image = $article->image_maj;
                    $imageName = md5(uniqid()).'.'.$image->guessExtension();
                    $image->move($this->getParameter('upload_image_article_directory'),$imageName);
                    $article->setImage($imageName);
                }
            }
            // $image = $article->getImage();
            $article->setUser($repo->find($article->authorId));
            $manager->persist($article);
            $manager->flush();

            return true;

        }
          
    }
    public function NumberOfMyArticle($id){
        $articles = $this->repoArticle->findByUserId($id);
        // dd( $id);
        $countArticles = count($articles);
        return $countArticles;
 }
}