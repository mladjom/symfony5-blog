<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\File;

class UploaderHelper
{
    const USER_IMAGE = 'user_image';

    private $uploadsPath;

    public function __construct(string $uploadsPath, RequestStackContext $requestStackContext)
    {
        $this->uploadsPath = $uploadsPath;
        $this->requestStackContext = $requestStackContext; }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;

    }

    public function uploadImage(File $file): string
    {
        $destination = $this->uploadsPath.'/'.self::USER_IMAGE;

        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }
        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)).'-'.uniqid().'.'.$file->guessExtension();
        
            $file->move(
            $destination,
            $newFilename
        );

        return $newFilename;
    }
}