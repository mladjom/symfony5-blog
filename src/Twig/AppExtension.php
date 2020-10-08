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

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
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
        $articleRepository = $this->container->get(ArticleRepository::class);
        $archive = $articleRepository->findAllPublishedOrderedByNewest();

        $categoryRepository = $this->container->get(CategoryRepository::class);
        $category = $categoryRepository->findAll();

        $tagRepository = $this->container->get(CategoryRepository::class);
        $tag = $tagRepository->findAll();

        return $twig->render('/includes/_sidebar.twig', [
            'archive' => $archive,
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


//    public function getFilters(): array
//    {
//        return [
//            // If your filter generates SAFE HTML, you should add a third
//            // parameter: ['is_safe' => ['html']]
//            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
//            new TwigFilter('filter_name', [$this, 'doSomething']),
//        ];
//    }
//
//    public function getFunctions(): array
//    {
//        return [
//            new TwigFunction('function_name', [$this, 'doSomething']),
//        ];
//    }
//
//    public function doSomething($value)
//    {
//        // ...
//    }

}
