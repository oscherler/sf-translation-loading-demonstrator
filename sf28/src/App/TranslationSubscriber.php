<?php

namespace App;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationSubscriber implements EventSubscriberInterface
{
    private $translator;
    private $truc_dir;
    
    public function __construct( TranslatorInterface $translator, $truc_dir )
    {
    	$this->translator = $translator;
    	$this->truc_dir = $truc_dir;
    }
	
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
    	$file = __DIR__ . '/../../truc/messages.fr.yaml';
        $this->translator->addResource( 'yml', $this->truc_dir . '/messages.fr.yml', 'fr', 'messages' );
    }

    public static function getSubscribedEvents()
    {
        return [
           'console.command' => 'onConsoleCommand',
        ];
    }
}
