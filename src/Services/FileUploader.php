<?php

namespace App\Services;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

class FileUploader
{
	private $container;

	private $filesystem;


	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
		$this->filesystem = new Filesystem();
	}

	public function uploadFile(UploadedFile $file)
	{
        $filename = time() . '_' . uniqid() . '.' . $file->guessClientExtension();

        $file->move(
            $this->container->getParameter('avatar_path'),
            $filename,
        );

        return $filename;
	}

	public function deleteFile(string $path, bool $isPublic)
    {
    	global $kernel;

    	$fullPath = $kernel->getProjectDir().$path;
    	
    	if (!$this->filesystem->exists($fullPath)) {
    		return;
    	}

        $result = $this->filesystem->remove($fullPath);

        if ($result === false) {
            throw new \Exception(sprintf('Error deleting "%s"', $path));
        }
    }
}