<?php
/**
 * Created by PhpStorm.
 * User: kulin
 * Date: 30.08.2018
 * Time: 16:42
 */

namespace AppBundle\Command;

use AppBundle\Entity\Meme;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMemesStatusCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UpdateMemesStatusCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    /**
     * (@inheritdoc)
     */
    public function configure()
    {
        $this
            ->setName("app:update_memes_status")
            ->setDescription("Komenda aktualizująca status memów");
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = 0;

        $wrongStatus = $this
            ->entityManager
            ->getRepository(Meme::class)
            ->findWrongStatus();

        $output->writeln(sprintf("Fresh2Hot: <info>%d</info>", count($wrongStatus['freshToHot'])));
        $output->writeln(sprintf("Fresh2Archive: <info>%d</info>", count($wrongStatus['freshExpired'])));
        $output->writeln(sprintf("FreshLowRate: <info>%d</info>", count($wrongStatus['freshLowRate'])));


        foreach ($wrongStatus['freshToHot'] as $meme) {
            $meme->setStatus(Meme::STATUS_HOT);
            $this->entityManager->persist($meme);
            $this->entityManager->flush();
            $counter++;
        }

        foreach ($wrongStatus['freshExpired'] as $meme) {
            $meme->setStatus(Meme::STATUS_ARCHIVE);
            $this->entityManager->persist($meme);
            $this->entityManager->flush();
            $counter++;
        }

        foreach ($wrongStatus['freshLowRate'] as $meme) {
            $meme->setStatus(Meme::STATUS_ARCHIVE);
            $this->entityManager->persist($meme);
            $this->entityManager->flush();
            $counter++;
        }


        $output->writeln(sprintf("Updated: <info>%d</info>", $counter));
    }
}