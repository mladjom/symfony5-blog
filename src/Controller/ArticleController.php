<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods="GET", name="article_index")
     * @Route("/rss.xml", defaults={"page": "1", "_format"="xml"}, methods="GET", name="article_rss")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods="GET", name="article_index_paginated")
     */
    public function index(Request $request, ArticleRepository $articleRepository, string $_format, int $page): Response
    {
        $articles = $articleRepository->findAllPublishedOrderedByNewest($page);
        return $this->render('article/index.' . $_format . '.twig', [
            'paginator' => $articles,
        ]);
    }

    /**
     * @Route("/article/search", methods="GET", name="article_search")
     */
    public function search(Request $request, ArticleRepository $article): Response
    {
        $query = $request->query->get('q', '');
        $limit = $request->query->get('l', 10);

        if (!$request->isXmlHttpRequest()) {
            return $this->render('article/search.html.twig', ['query' => $query]);
        }

        $foundArticles = $article->findBySearchQuery($query, $limit);

        $results = [];
        foreach ($foundArticles as $article) {
            $content = strip_tags($article->getContent());
            $results[] = [
                'title' => htmlspecialchars($article->getTitle(), ENT_COMPAT | ENT_HTML5),
                'date' => $article->getCreatedAt()->format('M d, Y'),
                'published' => $article->getPublished(),
                'author' => htmlspecialchars($article->getAuthor()->getName(), ENT_COMPAT | ENT_HTML5),
                'content' => htmlspecialchars($content, ENT_COMPAT | ENT_HTML5),
                'url' => $this->generateUrl('article_show', ['slug' => $article->getSlug()]),
            ];
        }

        return $this->json($results);
    }


    /**
     * @Route("/article/{slug}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
//    public function delete(Request $request, Article $article): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->remove($article);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('article_index');
//    }
}
