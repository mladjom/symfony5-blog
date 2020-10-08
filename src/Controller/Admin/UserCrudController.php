<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder){
        $this->passwordEncoder = $passwordEncoder;
    }


//    public function updateEntity(EntityManagerInterface $entityManager, $user): void
//    {
//        parent::updateEntity($entityManager, $user);
//    }
//
//    public function persistEntity(EntityManagerInterface $entityManager, $user): void
//    {
//        $this->encodePassword($user);
//        parent::persistEntity($entityManager, $user);
//    }
//
//    private function encodePassword($user): void
//    {
//        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
//    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', '%entity_label_plural% listing')
            ->setEntityLabelInPlural('Users')
            ->setEntityLabelInSingular('User')
            ->setEntityPermission('ROLE_ADMIN')
            ->setSearchFields(['name', 'email', 'roles', 'id']);    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $email = EmailField::new('email');
        $roles = ArrayField::new('roles');
        $password = TextField::new('password');
        $articles = AssociationField::new('articles');
        $comments = AssociationField::new('comments');
        $is_verified = BooleanField::new('is_verified');
        $createdAt = DateTimeField::new('createdAt');
//        $updatedAt = DateTimeField::new('updatedAt');
//        $deletedAt = DateTimeField::new('deletedAt');


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $email, $roles, $is_verified, $articles, $comments, $createdAt];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $email,  $roles, $password, $is_verified, $createdAt];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $email, $password, $roles, $is_verified];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $email, $password, $roles, $is_verified];
        }
    }
}
