<?php

namespace Syw\Front\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use FOS\UserBundle\Model\User;
use Syw\Front\MainBundle\Entity\StatsRegistration;

/**
 *
 */
class CreateRegistrationStatisticsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('syw:statistics:registration')
            ->setDescription('Creates the statistics for registrations')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>syw:statistics:registration</info> command Creates the statistics for registrations.

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = $this->getContainer()->get('doctrine')->getManager();

        $profiles = $db->getRepository('SywFrontMainBundle:UserProfile')->findBy(array(), array('createdAt' => 'ASC'));

        $range[0] = new \DateTime("1970-1-1 00:00:00");
        $range[1] = new \DateTime("1970-1-1 00:00:00");
        foreach ($profiles as $profile) {
            $createdAt = $profile->getCreatedAt();
            if (($createdAt >= $range[0]) && ($createdAt <= $range[1])) {
                $statsReg->setNum($statsReg->getNum()+1);
                continue;
            }
            if (true === isset($statsReg) && true === is_object($statsReg)) {
                $db->persist($statsReg);
                $db->flush();
            }
            unset($statsReg);
            $range = $this->dayRange($createdAt);
            $statsReg = new StatsRegistration();
            $statsReg->setDay($range[0]);
            $statsReg->setNum(1);
            $db->persist($statsReg);
            $db->flush();
        }

        $output->writeln(sprintf('Registration statistics created', ''));
    }

    protected function dayRange($date)
    {
        $dayar = explode(" ", $date->format('Y-m-d H:i:s'));
        $day = $dayar[0];
        return array(new \DateTime($day." 00:00:00"), new \DateTime($day." 23:59:59"));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }
}
