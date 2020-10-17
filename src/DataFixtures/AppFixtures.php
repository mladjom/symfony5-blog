<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Tag;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Service\UploaderHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class AppFixtures extends Fixture
{
    private $users = [];
    private $tags = [];
    private $categories = [];
    private $articles = [];

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var Generator
     */
    private $faker;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;


    public function __construct(UserPasswordEncoderInterface $passwordEncoder, SluggerInterface $slugger, UploaderHelper $uploaderHelper)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
        $this->faker = Factory::create();
        $this->uploaderHelper = $uploaderHelper;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUserData($manager);
        $this->loadTagData($manager);
        $this->loadCategoryData($manager);
        $this->loadArticleData($manager);
        $this->loadCommentData($manager);

        $manager->flush();
    }

    private function loadUserData(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {

            $user = new User();

            $user->setEmail($this->faker->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'user123'));
            $user->setName($this->faker->firstName());
            $imageFilename = $this->fakeUploadImage();
            $user->setImageFilename($imageFilename);
            $user->setAbout($this->faker->sentences(4, true));
            $user->setcreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $user->setisVerified($this->faker->boolean);
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $this->users[] = $user;
        }
        $user = new User();
        $user->setEmail('admin@site.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin123'));
        $user->setName($this->faker->firstName());
        $imageFilename = $this->fakeUploadImage();
        $user->setImageFilename($imageFilename);
        $user->setAbout($this->faker->sentences(4, true));
        $user->setcreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
        $user->setisVerified(TRUE);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $manager->persist($user);

        $this->users[] = $user;

    }

    private function fakeUploadImage(): string
    {
        $randomImage = $this->faker->randomElement($this->userImages());

        $fs = new Filesystem();
        $targetPath = sys_get_temp_dir().'/'.$randomImage;
        $fs->copy(__DIR__.'/images/'.$randomImage, $targetPath, true);
        return $this->uploaderHelper
            ->uploadImage(new File($targetPath), null);
    }
    
    private function userImages()
    {
       return array ('a.jpg','b.jpg','c.jpg');
    }

    private function loadCategoryData(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {
            $category = new Category();
            $category->setName($this->faker->text(20));
            $category->setSlug($this->slugger->slug(strtolower($category->getName())));
            $manager->persist($category);

            $this->categories[] = $category;
        }
    }

    private function loadTagData(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; ++$i) {
            $tag = new Tag();
            $tag->setName($this->faker->text(20));
            $tag->setSlug($this->slugger->slug(strtolower($tag->getName())));
            $manager->persist($tag);

            $this->tags[] = $tag;
        }
    }

    private function loadArticleData(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; ++$i) {
            $article = new Article();
            $article->setAuthor($this->faker->randomElement($this->users));
            foreach ($this->faker->randomElements($this->tags, mt_rand(1, 5)) as $tag) {
                $article->addTag($tag);
            }
            foreach ($this->faker->randomElements($this->categories) as $category) {
                $article->setCategory($category);
            }
            $article->setTitle($this->faker->text(100));
            $article->setSlug($this->slugger->slug(strtolower($article->getTitle())));
            $article->setContent($this->faker->text(1000));
            $article->setcreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $article->setPublished($this->faker->boolean);
            $manager->persist($article);

            $this->articles[] = $article;
        }
    }


    private function loadCommentData(ObjectManager $manager)
    {
        for ($i = 0; $i < 200; ++$i) {
            $comment = new Comment();
            $comment->setContent(
                $this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
            );
            $comment->setAuthor($this->faker->randomElement($this->users));
            foreach ($this->faker->randomElements($this->articles, mt_rand(1, 5)) as $article) {
                $comment->setArticle($article);
            }
            $comment->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
            $comment->setPublished($this->faker->boolean);
            $manager->persist($comment);
        }
    }
}
