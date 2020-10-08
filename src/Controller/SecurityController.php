<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Helper\NewEditArticle;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    protected $id;
    protected $nea;
    public function __construct(NewEditArticle $nea)
    {
        $this->nea = $nea;
    }
    // /**
    //  * @Route("/security", name="security")
    //  */
    // // public function index()
    // // {
    // //     return $this->render('security/index.html.twig', [
    // //         'controller_name' => 'SecurityController',
    // //     ]);
    // // }

    /**
     * @Route("/inscription",name = "security_registration")
     * @Route("/{id}/modifier" , name="security_modif")
     */
    public function registration(User $user = null, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder, UserRepository $repo)
    {
        if ($user) {
            // $user = $repo->find($id);
            $form = $this->createForm(RegistrationType::class, $user);
            if (!$user->getImageProfil()) {
                $form->add('image_profil', FileType::class, [
                    'label' => 'votre photo de profil '
                ]);
            }
            // if($user->getImageProfil()){
            //     $form->add('image_profil_update', FileType::class, [
            //         'label' => 'votre photo de profil '
            //     ]);
            // }
        }

        if (!$user) {
            $user = new User();
            $form = $this->createForm(RegistrationType::class, $user);
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            if ($user->getId()) {
                $imageProfil = $user->getImageProfil();
                $imageName = md5(uniqid()) . '.' . $imageProfil->guessExtension();
                $imageProfil->move($this->getParameter('upload_image_user_profil_directory'), $imageName);
                $user->setImageProfil($imageName);
            }
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */

    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/deconnexion" , name="security_logout")
     */
    public function logout()
    {
    }
}
