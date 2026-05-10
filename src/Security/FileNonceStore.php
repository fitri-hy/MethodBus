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

    public function has(
        string $nonce
    ): bool {

        $file = $this->file($nonce);

        if (!file_exists($file)) {
            return false;
        }

        $expires = (int)
            file_get_contents($file);

        if (time() > $expires) {

            unlink($file);

            return false;
        }

        return true;
    }

    public function store(
        string $nonce,
        int $ttl
    ): void {

        file_put_contents(
            $this->file($nonce),
            time() + $ttl
        );
    }

    private function file(
        string $nonce
    ): string {

        return $this->path
            . '/'
            . sha1($nonce);
    }
}