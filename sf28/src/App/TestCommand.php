<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\TranslatorInterface;

class TestCommand extends Command
{
    protected static $defaultName = 'test';

    private $translator;
    
    public function __construct( TranslatorInterface $translator )
    {
    	$this->translator = $translator;

        parent::__construct();
    }

    protected function configure()
    {
	    $this
    	    ->setName( static::$defaultName );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( $this->translator->trans('foo') );
        $output->writeln( $this->translator->trans('bar') );
    }
}
