<?php

namespace App\Controller;

use App\Entity\Provider;
use App\Form\ProviderType;
use App\Repository\ProviderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/provider")
 */
class ProviderController extends AbstractController
{
    /**
     * @Route("/", name="provider_index", methods={"GET"})
     */
    public function index(ProviderRepository $providerRepository, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('provider/index.html.twig', ['name' => $name,
                'providers' => $providerRepository->findAll(),
                'userId' => $session->has('id') ? $session->get('id') : 1,
            ]);
        }
    }

    /**
     * @Route("/new", name="provider_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            $provider = new Provider();
            $form = $this->createForm(ProviderType::class, $provider);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($provider);
                $entityManager->flush();

                return $this->redirectToRoute('provider_index');
            }

            return $this->render('provider/new.html.twig',
                [
                    'name' => $name,
                    'provider' => $provider,
                    'userId' => $session->has('id') ? $session->get('id') : 1,
                    'form' => $form->createView(),
                ]);
        }
    }

    /**
     * @Route("/{id}", name="provider_show", methods={"GET"})
     */
    public function show(Provider $provider, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('provider/show.html.twig', ['name' => $name,
                'provider' => $provider,
                'userId' => $session->has('id') ? $session->get('id') : 1,
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="provider_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Provider $provider): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            $form = $this->createForm(ProviderType::class, $provider);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('provider_index');
            }

            return $this->render('provider/edit.html.twig', [
                'provider' => $provider,
                'name' => $name,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{id}", name="provider_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Provider $provider): Response
    {
        if ($this->isCsrfTokenValid('delete' . $provider->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($provider);
            $entityManager->flush();
        }

        return $this->redirectToRoute('provider_index');
    }
}
