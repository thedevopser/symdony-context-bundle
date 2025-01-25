<?php

namespace TheDevOpser\SymfonyContextBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use TheDevOpser\SymfonyContextBundle\DependencyInjection\TheDevOpserSymfonyContextExtension;

class SymfonyContextBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new TheDevOpserSymfonyContextExtension();
        }
        return $this->extension;
    }
}