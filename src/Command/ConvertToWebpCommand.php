<?php

namespace App\Command;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:convert-to-webp',
    description: 'Convertit toutes les images en WebP et met à jour la BDD',
)]
class ConvertToWebpCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Conversion des images en WebP');

        $medias = $this->em->getRepository(Media::class)->findAll();
        $total = count($medias);
        $converted = 0;
        $errors = 0;
        $skipped = 0;

        $io->progressStart($total);

        foreach ($medias as $media) {
            $oldPath = $this->projectDir . '/public/' . $media->getPath();

            // Déjà en WebP
            if (str_ends_with($media->getPath(), '.webp')) {
                $skipped++;
                $io->progressAdvance();
                continue;
            }

            // Fichier inexistant
            if (!file_exists($oldPath)) {
                $errors++;
                $io->progressAdvance();
                continue;
            }

            $newRelativePath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $media->getPath());
            $newPath = $this->projectDir . '/public/' . $newRelativePath;

            try {
                $image = null;
                $extension = strtolower(pathinfo($oldPath, PATHINFO_EXTENSION));

                $image = match ($extension) {
                    'jpg', 'jpeg' => imagecreatefromjpeg($oldPath),
                    'png' => imagecreatefrompng($oldPath),
                    'gif' => imagecreatefromgif($oldPath),
                    default => null
                };

                if ($image === null) {
                    $errors++;
                    $io->progressAdvance();
                    continue;
                }

                imagewebp($image, $newPath, 80);

                // Supprime l'ancien fichier
                unlink($oldPath);

                // Met à jour la BDD
                $media->setPath($newRelativePath);
                $converted++;
            } catch (\Exception $e) {
                $errors++;
            }

            $io->progressAdvance();
        }

        // Flush en une seule fois
        $this->em->flush();

        $io->progressFinish();
        $io->success("Conversion terminée : {$converted} converties, {$skipped} ignorées, {$errors} erreurs.");

        return Command::SUCCESS;
    }
}
