<?php

namespace App\Service;


use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Asset\Context\RequestStackContext;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploaderHelper
{
    const PUBLIC_FILE = 'files';
    const PRIVATE_FILE = 'users';
    private $requestStackContext;
    private $fileSystem;
    private $logger;
    private $publicAssetBaseUrl;
    private $privateFileSystem;
    private $slugger;
    public function __construct(RequestStackContext $requestStackContext, FilesystemInterface $publicFilesFileSystem, FilesystemInterface $privateFilesFileSystem, LoggerInterface $logger, string $uploadedAssetsBaseUrl, SluggerInterface $slugger)
    {
        $this->requestStackContext = $requestStackContext;
        $this->fileSystem = $publicFilesFileSystem;
        $this->logger = $logger;
        $this->publicAssetBaseUrl = $uploadedAssetsBaseUrl;
        $this->privateFileSystem = $privateFilesFileSystem;
        $this->slugger = $slugger;

    }

    public function uploadPublicFile(File $file, ?string $uploadedFileName, $existingFileName = NULL ): string
    {
        $filename = self::PUBLIC_FILE.'/'.$existingFileName.".".$file->guessExtension();
        if (file_exists($filename)){
            if ($uploadedFileName == $existingFileName){
                try {
                    $result = $this->fileSystem->delete(self::PUBLIC_FILE.'/'.$existingFileName.".".$file->guessExtension());
                    if ($result === false) {
                        throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFileName));
                    }
                } catch (FileNotFoundException $e) {
                    $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFileName));
                }
            }
        }
        return $this->uploadFile($file, self::PUBLIC_FILE, true, $uploadedFileName);
    }

    public function removeFile($existingFileName)
    {
        if ($existingFileName) {
            try {
                $result = $this->fileSystem->delete(self::PUBLIC_FILE.'/'.$existingFileName);
                if ($result === false) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFileName));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFileName));
            }
        } else {
            $result = 'image deleted';
        }

        return $result;

    }

    public function uploadPrivateFile(File $file, ?string $existingFileName): string
    {
        if ($existingFileName) {
            try {
                $result = $this->fileSystem->delete(self::PRIVATE_FILE.'/'.$existingFileName);
                if ($result === false) {
                    throw new \Exception(sprintf('Could not delete old uploaded file "%s"', $existingFileName));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('Old uploaded file "%s" was missing when trying to delete', $existingFileName));
            }
        }
        $newFileName =  $this->uploadFile($file, self::PRIVATE_FILE, false, null);
        return $newFileName;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext->getBasePath().$this->publicAssetBaseUrl.'/'.$path;
    }

    public function uploadFile(File $file, string $directory, bool $isPublic, $uploadFileName): string
    {
        $uuidGenerator = Uuid::uuid4();
        $uid = $uuidGenerator->toString();
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        if($uploadFileName)
        {
            $newFileName = $this->slugger->slug($uploadFileName).'.'.$file->guessExtension();

        } else {
            //     Set the new file name which is unique to our system
            $newFileName = $uid.'-'.uniqid().'.'.$file->guessExtension();

        }
        // Check if the uploaded file is public or private
        $fileSystem = $isPublic ? $this->fileSystem : $this->privateFileSystem;
        $stream = fopen($file->getPathname(), 'r');
        $result = $fileSystem->writeStream(
            $directory.'/'.$newFileName,
            $stream
        );
        if ($result === false) {
            throw new \Exception(sprintf('Could not write uploaded file "%s"', $newFileName));
        }
        if (is_resource($stream)) {
            fclose($stream);
        }
        return $newFileName;
    }

    public function readStream(string $path, bool $isPublic)
    {
        $fileSystem = $isPublic ? $this->fileSystem : $this->privateFileSystem;
        $resource = $fileSystem->readStream($path);
        if ($resource === false) {
            throw new \Exception(sprintf('Error opening stream for "%s"', $path));
        }
        return $resource;
    }
}
