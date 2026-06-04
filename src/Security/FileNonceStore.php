<?php

namespace MethodBus\Security;

use MethodBus\Core\Path;

final class FileNonceStore implements NonceStore
{
    private string $path;

    public function __construct()
    {
        $this->path = Path::root(
            'storage/methodbus/nonces'
        );

        if (!is_dir($this->path)) {
            mkdir(
                $this->path,
                0777,
                true
            );
        }
    }

    public function remember(
        string $key,
        int $ttl
    ): bool {

        $file = $this->file($key);

        if (is_file($file)) {

            $expires = (int)
                file_get_contents($file);

            if (time() <= $expires) {
                return false;
            }

            @unlink($file);
        }

        $handle = @fopen($file, 'x');

        if ($handle === false) {
            return false;
        }

        fwrite(
            $handle,
            (string) (time() + $ttl)
        );

        fclose($handle);

        return true;
    }

    private function file(
        string $key
    ): string {

        return $this->path
            . '/'
            . hash('sha256', $key);
    }
}
