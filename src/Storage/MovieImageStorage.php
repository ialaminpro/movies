<?php

declare(strict_types=1);

namespace App\Storage;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final readonly class MovieImageStorage
{
    private const PUBLIC_PREFIX = '/uploads/';

    public function __construct(
        private string $uploadDirectory,
        private Filesystem $filesystem,
        private LoggerInterface $logger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $extension = $file->guessExtension() ?: 'bin';
        $filename = Uuid::v7()->toRfc4122().'.'.$extension;

        try {
            $this->filesystem->mkdir($this->uploadDirectory);
            $file->move($this->uploadDirectory, $filename);
        } catch (FileException|\RuntimeException $exception) {
            $this->logger->error('Movie image upload failed.', ['exception' => $exception]);

            throw new StorageException('Unable to store movie image.', 0, $exception);
        }

        return self::PUBLIC_PREFIX.$filename;
    }

    public function delete(?string $publicPath): void
    {
        if (null === $publicPath || !str_starts_with($publicPath, self::PUBLIC_PREFIX)) {
            return;
        }

        try {
            $this->filesystem->remove($this->uploadDirectory.'/'.basename($publicPath));
        } catch (\RuntimeException $exception) {
            $this->logger->warning('Movie image cleanup failed.', [
                'exception' => $exception,
                'path' => $publicPath,
            ]);
        }
    }
}
