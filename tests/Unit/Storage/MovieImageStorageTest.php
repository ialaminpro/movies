<?php

declare(strict_types=1);

namespace App\Tests\Unit\Storage;

use App\Storage\MovieImageStorage;
use App\Storage\StorageException;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class MovieImageStorageTest extends TestCase
{
    private string $directory;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/movie-storage-'.bin2hex(random_bytes(6));
        $this->filesystem = new Filesystem();
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->directory);
    }

    public function testUploadUsesCollisionResistantServerFilenameAndDeleteCleansItUp(): void
    {
        $source = $this->pngFile();
        $storage = new MovieImageStorage($this->directory, $this->filesystem, new NullLogger());

        $path = $storage->upload(new UploadedFile($source, '../../unsafe name.png', 'image/png', null, true));

        self::assertMatchesRegularExpression('#^/uploads/[0-9a-f-]{36}\.png$#', $path);
        self::assertFileExists($this->directory.'/'.basename($path));

        $storage->delete($path);
        self::assertFileDoesNotExist($this->directory.'/'.basename($path));
    }

    public function testExternalImagePathIsNeverDeleted(): void
    {
        $storage = new MovieImageStorage($this->directory, $this->filesystem, new NullLogger());
        $storage->delete('https://example.com/poster.jpg');

        self::assertDirectoryDoesNotExist($this->directory);
    }

    public function testStorageFailureIsWrapped(): void
    {
        $blockingFile = tempnam(sys_get_temp_dir(), 'blocked-upload-');
        self::assertIsString($blockingFile);
        $storage = new MovieImageStorage($blockingFile.'/nested', $this->filesystem, new NullLogger());

        $this->expectException(StorageException::class);
        $storage->upload(new UploadedFile($this->pngFile(), 'poster.png', 'image/png', null, true));
    }

    private function pngFile(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'poster-');
        self::assertIsString($path);
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=', true));

        return $path;
    }
}
