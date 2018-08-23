<?php

namespace Itk\ExchangeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TestCommand
 *
 * @package Itk\ExchangeBundle\Command
 */
class TestCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('os2display:exchange:test')
      ->addArgument('email', InputArgument::REQUIRED, 'The mail to get events from.')
      ->setDescription('Text exchange');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
      $now = time();

      $start = strtotime('-7 days', $now);
      $end = strtotime('+7 days', $now);

      $calendar = $this->getContainer()->get('itk.exchange_service')
          ->getExchangeBookingsForInterval(
              $input->getArgument('email'),
              $start,
              $end
          );

      $output->writeln(json_encode($calendar));
  }
}
