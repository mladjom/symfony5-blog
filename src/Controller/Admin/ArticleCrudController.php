<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // the visible title at the top of the page and the content of the <title> element
            // it can include these placeholders: %entity_id%, %entity_label_singular%, %entity_label_plural%
            ->setPageTitle('index', '%entity_label_plural% listing')
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')

            // the Symfony Security permission needed to manage the entity
            // (none by default, so you can manage all instances of the entity)
            ->setEntityPermission('ROLE_ADMIN')
            ->setDateFormat('d/m/Y')
           // ->setSearchFields(['title', 'content'])
            ->setDefaultSort(['createdAt'=>'DESC'])
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $title = TextField::new('title');
        $slug = SlugField::new('slug', 'Slug')
            ->setTargetFieldName('title');
        $content = TextEditorField::new('content');
        $category = AssociationField::new('category');
        $tags = AssociationField::new('tags');
        $author = AssociationField::new('author');
        $comments = AssociationField::new('comments');
        $created_at = DateTimeField::new('createdAt');
        $updated_at = DateTimeField::new('updatedAt');
        $published = BooleanField::new('published');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $title, $slug, $category, $tags, $author, $comments, $created_at, $updated_at, $published];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $title, $slug, $content, $category, $tags, $author, $comments, $created_at, $updated_at, $published];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$title, $slug, $content, $category, $tags, $author, $published];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$title, $slug, $content, $category, $tags, $author, $created_at, $updated_at, $published];
        }
    }
}
