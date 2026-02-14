<?php

declare(strict_types=1);

namespace App\Action;

use Slim\Http\Response;

final class IndexAction extends AbstractAction
{
    public function handleAction(): Response
    {
        return $this->render('index.html.php', [
            'entry' => $this->getManifestFile('main.ts')
        ]);
    }

    private function getManifestFile(string $entryFile): array
    {
        $manifestPath = PUBLIC_PATH . '/dist/manifest.json';
        if (!file_exists($manifestPath)) {
            throw new \RuntimeException("manifest.json file not found in the public directory.");
        }
        
        $manifest = file_get_contents($manifestPath);
        $manifest = json_decode($manifest, true);

        if (!isset($manifest[$entryFile])) {
            throw new \DomainException("No $entryFile found in manifest.json");
        }

        return $manifest[$entryFile];
    }
}