<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * list all categories
     * @Route("/", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) { //always check the user connection (if user connected), we use the name attribute which is set when user login (check login action in user controller)
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('category/index.html.twig', [
                'name' => $name, //get username from session
                'userId' => $session->has('id') ? $session->get('id') : 1,
//                'name' => $name,
                //the repository object allows you to run basic queries
                //findAll() return all objects from the category table (all categories created)
                'categories' => $categoryRepository->findAll(),
            ]);
        }
    }

    /**
     * Create a new category
     * @Route("/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $session = $request->getSession();
        // checking the user connection 
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Connexion error');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            $category = new Category(); //create a new instance/object from Category Entity
            $form = $this->createForm(CategoryType::class, $category); // create Category Form
            $form->handleRequest($request);

            // if the form is submitted and all fields are valid
            if ($form->isSubmitted() && $form->isValid()) {
                // get the entity manager: it’s responsible for saving objects to, and fetching objects from, the database.
                $entityManager = $this->getDoctrine()->getManager();
                // tell Doctrine you want to (eventually) save the Category (no queries yet)
                $entityManager->persist($category);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();

                return $this->redirectToRoute('category_index');
            }

            return $this->render('category/new.html.twig', [
                'category' => $category,
                'name' => $name,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * show page of a category
     * @Route("/{id}", name="category_show", methods={"GET"})
     */
    public function show(Category $category, Request $request): Response
    {
        $session = $request->getSession();
        // checking the user connexion
        if (!$session->has('name')) {
            //$this->get('session')->getFlashBag()->add(....) to display a message for the user
            // first argument is the type of the message (success / info / danger ...)
            // for more infos about the Flash messages check : https://symfony.com/doc/current/components/http_foundation/sessions.html#flash-messages
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('category/show.html.twig', //=> the twig page (ctrl+click to move directly to the twig file [phpstorm trick])
                [
                    //sending variables to use in twig page
                    'name' => $name,
                    'userId' => $session->has('id') ? $session->get('id') : 1,
                    //$category is gotten by the paramConverter annotation => edit(..., Category $category) and route="/{id}/edit"
                    // The converter tries to get a Category object from the request attributes (request attributes comes from route placeholders – here id)
                    'category' => $category,
                ]);
        }
    }

    /**
     * Edit a category
     * @Route("/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $session = $request->getSession();
        // check session (check user is connected or not) : the user variable is set when the user connects =>(check UserController login action )
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name'); //get the username from session
            // create the edit form
            // $category is gotten by the paramConverter annotation => edit(..., Category $category) and route="/{id}/edit"
            // The converter tries to get a Category object from the request attributes (request attributes comes from route placeholders – here id)
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);
            // if the form is submitted (by clicking on save button) and isValid => Returns whether the form and all children are valid.
            if ($form->isSubmitted() && $form->isValid()) {
                // execute the query to update the $category object in the datatabase
                $this->getDoctrine()->getManager()->flush();
                // redirect to category list page
                return $this->redirectToRoute('category_index');
            }
            // $this->render(...) returns a view (twig file) with passing variables to use in the twig file (such as category)
            return $this->render('category/edit.html.twig', ['name' => $name,
                'category' => $category,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(), //to display and use form object we must call createView() function for the form before sending it to the view
            ]);
        }
    }

    /**
     * Delete a category
     * @Route("/{id}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Category $category): Response
    {
        //check the token (this check the validity of the token created when building the form ) to prevent the website from
        // for more infos about csrf token check https://symfony.com/doc/current/security/csrf.html
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            // remove the object from the database : removing a category will remove the related objects such as products under this category
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_index');
    }
}
