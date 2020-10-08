<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Playground');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('dd/MM/yyyy')
            ->setDateTimeFormat('dd/MM/yyyy HH:mm:ss')
            ->setTimeFormat('HH:mm');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section(),
            MenuItem::linkToRoute('Public website', 'fa fa-home', 'article_index')->setLinkTarget('_blank')->setLinkRel('noreferrer'),
            MenuItem::linkToDashboard('Dashboard', 'fas fa-tachometer-alt'),

            MenuItem::section('Articles'),
            MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Article::class),
            MenuItem::linkToCrud('Categories', 'fas fa-folder-open', Category::class),
            MenuItem::linkToCrud('Tags', 'fas fa-tags', Tag::class),
            MenuItem::linkToCrud('Comments', 'fas fa-comments', Comment::class),

            MenuItem::section('Users'),
            MenuItem::linkToCrud('Users', 'fas fa-users', User::class),

            MenuItem::section(),
            MenuItem::linkToLogout('Logout', 'fas fa-sign-out-alt'),
        ];

    }
}
