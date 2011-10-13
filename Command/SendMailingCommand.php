<?php

namespace Wowo\Bundle\NewsletterBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Wowo\Bundle\NewsletterBundle\Exception\NewsletterException;

class SendMailingCommand extends ContainerAwareCommand
{
    const DELAY = 100;
    protected function configure()
    {
        $this
            ->setDescription('Fetches jobs from queue, process them and send as an email')
            ->setHelp(<<<EOT
The <info>newsletter:send</info> fetches jobs from queue, replaces placeholders 
and sends emails to recipients.

<info>php app/console newsletter:send</info>

EOT
            )
            ->setName('newsletter:send')
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');
        $logger = function($message) use ($output, $verbose)
        {
            if ($verbose) {
                $output->writeln($message);
            }
        };

        while (1) {
            try {
                $this->getContainer()->get('wowo_newsletter.newsletter_manager')->processMailing($logger);
            } catch (NewsletterException $e) {
                $logger(sprintf('<error>Newsletter exception (%s) occured, message: %s</error>',
                    get_class($e), $e->getMessage()));
            } catch (\Swift_SwiftException $e) {
                $logger(sprintf('<error>Mailer exception (%s) occured, message: %s</error>',
                    get_class($e), $e->getMessage()));
            } catch (\Exception $e) {
                $logger(sprintf('<error>Unknown exception (%s) occured, message: %s</error>',
                    get_class($e), $e->getMessage()));
            }
            usleep(self::DELAY);
        }
    }
}