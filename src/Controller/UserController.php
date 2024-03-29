<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Provider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Request $request): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * The dashboard interface
     * @Route("/dash", name="dashboard")
     */
    public function dash(Request $request, EntityManagerInterface $em)
    {
        $session = $request->getSession();
        $name = $session->get('name');
        // get some statistics for dashboard widgets
        $categoriesCount = count($em->getRepository(Category::class)->findAll());
        $productsCount = count($em->getRepository(Product::class)->findAll());
        $clientsCount = count($em->getRepository(Client::class)->findAll());
        $providersCount = count($em->getRepository(Provider::class)->findAll());
        $totalStock = $em->getRepository(Product::class)->getTotalStockValue();
        return $this->render('user/dashboard.html.twig', [
            'name' => $name,
            'userId' => $request->getSession()->has("id") ? $request->getSession()->get("id") : 1,
            'categoriesCount' => $categoriesCount,
            'productsCount' => $productsCount,
            'clientsCount' => $clientsCount,
            'providersCount' => $providersCount,
            'totalStock' => $totalStock,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $session = $request->getSession();
        $name = $session->get('name');
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user, 'name' => $name,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="user_login", methods={"GET","POST"})
     */
    public function login(Request $request, UserRepository $userRepository, ProductRepository $productRepository): Response
    {
        $session = $request->getSession();
        $session->clear();
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('login', TextType::class, [
                'attr' => [
                    'placeholder' => 'Taper votre login',
                ],

            ])
            ->add('pwd', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Taper votre Password',
                ],

            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $pwd = $user->getPwd();
            $login = $user->getLogin();
            $user1 = $userRepository->findOneBy(array('login' => $login,
                'pwd' => $pwd));
            if (!$user1) {
                $this->get('session')->getFlashBag()->add('info',
                    'Login Incorrecte Vérifier Votre Login  ....');
            } else {
                if (!$session->has('name')) {
                    $session->set('name', $user1->getUserName());
                    $session->set('id', $user1->getId());
                    $name = $session->get('name');

                    return $this->redirectToRoute("dashboard");
                }
            }
        }

        return $this->render('user/login.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,

        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_login');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'userId' => $user->getId(),
            'name' => $user->getUsername(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
