<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * List all clients
     * @Route("/", name="client_index", methods={"GET"})
     */
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('client/index.html.twig', [
                'name' => $name,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                //the repository object allows you to run basic queries
                //findAll() return all objects from the client table (all clients created)
                'clients' => $clientRepository->findAll()
            ]);
        }
    }

    /**
     * Create a new client
     * @Route("/new", name="client_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $session = $request->getSession();
        // checking the user connection
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            //create a new instance/object from Client Entity
            $client = new Client();
            // create Client Form and pass the $client variable (when submitted symfony will update the $client)
            $form = $this->createForm(ClientType::class, $client);
            $form->handleRequest($request);
            // if the form is submitted and all fields are valid
            if ($form->isSubmitted() && $form->isValid()) {
                // get the entity manager: it’s responsible for saving objects to, and fetching objects from, the database.
                $entityManager = $this->getDoctrine()->getManager();
                // tell Doctrine you want to (eventually) save the Category (no queries yet)
                $entityManager->persist($client);
                // actually executes the queries (i.e. the INSERT query)
                $entityManager->flush();
                //redirect to another route
                return $this->redirectToRoute('client_index');
            }

            //render the twig page (the view of this action/function)
            return $this->render('client/new.html.twig', ['name' => $name,
                'client' => $client,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * Show page of a client
     * @Route("/{id}", name="client_show", methods={"GET"})
     */
    public function show(Client $client, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');

            return $this->render('client/show.html.twig', [
                'name' => $name,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                // $client is gotten by the paramConverter annotation => edit(..., Client $client) and route="/{id}/edit"
                // The converter tries to get a Client object from the request attributes (request attributes comes from route placeholders – here id)
                'client' => $client,
            ]);
        }
    }


    /**
     * Edit a client
     * @Route("/{id}/edit", name="client_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Client $client): Response
    {
        $session = $request->getSession();
        // check session (check user is connected or not) : the user variable is set when the user connects =>(check UserController login action )
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            // $this->createForm(...) creates a new FormType (form) these forms are created in /Form
            $form = $this->createForm(ClientType::class, $client);
            //The recommended way of processing Symfony forms is to use the handleRequest() method to detect when the form has been submitted. However, you can also use the submit() method to have better control over when exactly your form is submitted and what data is passed to it:
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('client_index');
            }

            return $this->render('client/edit.html.twig', ['name' => $name,
                'client' => $client,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * Delete a client
     * @Route("/{id}", name="client_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Client $client): Response
    {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            // ->remove() eemoves an object instance.
            // A removed object will be removed from the database as a result of the flush operation.
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_index');
    }
}
