<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     * @IsGranted("ROLE_ADMIN", message="Cette page est reservée aux administrateurs")
     */
    public function listAction(CacheInterface $cacheInterface)
    {
        $usersListInCache = $cacheInterface->get('usersList', function(ItemInterface $item) {
            $item->expiresAfter(90);
            return $this->getDoctrine()->getRepository('App:User')->findAll();
        });
        
        return $this->render('user/list.html.twig', ['users' => $usersListInCache]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @IsGranted("CAN_CREATE", message="en tant qu'utilisateur connecté, vous n'avez pas le droit de creer d'autres utilisateurs") 
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
       
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {    
            $form->get('isAdmin')->getData() ? $user->setRoles(["ROLE_ADMIN"]) : $user->setRoles(["ROLE_USER"]);
            $user->setPassword($userPasswordEncoder->encodePassword($user, $form->get('password')->getData()));           
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @IsGranted("CAN_EDIT", message="Vous n'etes pas administrateur, vous n'avez pas le droit de modifier cet utilisateur")
     */
    public function editAction(User $user, Request $request, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $form = $this->createForm(UserType::class, $user);
                
        $form->handleRequest($request);        

        if ($form->isSubmitted() && $form->isValid()) 
        {            
            $form->get('isAdmin')->getData() ? $user->setRoles(["ROLE_ADMIN"]) : $user->setRoles(["ROLE_USER"]) ;
            $user->setPassword($userPasswordEncoder->encodePassword($user, $form->get('password')->getData()));           

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
