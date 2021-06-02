<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * List all products
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('product/index.html.twig', ['name' => $name,
                'products' => $productRepository->findAll(),
                'userId' => $session->has('id') ? $session->get('id') : 1,
            ]);
        }
    }

    /**
     * new product
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            $product = new product();
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // handle image upload (under public/uploads/images)
                $file = $product->getImage();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $entityManager = $this->getDoctrine()->getManager();
                $product->setImage($fileName);
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('product_index');
            }

            return $this->render('product/new.html.twig', [
                'product' => $product,
                'name' => $name,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * show page of a product
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(product $product, Request $request): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            return $this->render('product/show.html.twig', ['name' => $name,
                'product' => $product,
                'userId' => $session->has('id') ? $session->get('id') : 1,
            ]);
        }
    }

    /**
     * edit a product
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, product $product): Response
    {
        $session = $request->getSession();
        if (!$session->has('name')) {
            $this->get('session')->getFlashBag()->add('info', 'Erreur de  Connection veuillez se connecter .... ....');
            return $this->redirectToRoute('user_login');
        } else {
            $name = $session->get('name');
            // get file if exists because formType expects a File Type and not String (else create new one)
            $product->setImage(
                file_exists($this->getParameter('images_directory') . "/" . $product->getImage()) ?
                new File($this->getParameter('images_directory') . "/" . $product->getImage()) : null
            );
            $form = $this->createForm(ProductType::class, $product);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                //handle image upload
                $file = $product->getImage();
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $entityManager = $this->getDoctrine()->getManager();
                $product->setImage($fileName);
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('product_index');
            }

            return $this->render('product/edit.html.twig', [
                'product' => $product,
                'name' => $name,
                'userId' => $session->has('id') ? $session->get('id') : 1,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * delete a product
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, product $product): Response
    {


        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
}
