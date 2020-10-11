<?php

namespace App\Twig;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class SidebarExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('article_sidebar', [$this, 'getArticleSidebar'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function getArticleSidebar(Environment $twig)
    {
//        $articleRepository = $this->container->get(ArticleRepository::class);
//        $archive = $articleRepository->findAllPublishedOrderedByNewest();

        $categoryRepository = $this->container->get(CategoryRepository::class);
        $category = $categoryRepository->findAll();

        $tagRepository = $this->container->get(TagRepository::class);
        $tag = $tagRepository->findAll();

        return $twig->render('/includes/_sidebar.html.twig', [
            //'archive' => $archive,
            'categories' => $category,
            'tags' => $tag,
        ]);
    }
    public static function getSubscribedServices()
    {
        return [
            ArticleRepository::class,
            CategoryRepository::class,
            TagRepository::class,
        ];
    }

}
