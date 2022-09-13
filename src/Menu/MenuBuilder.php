<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    public function __construct(
        private readonly FactoryInterface $factory
    ) {}

    public function createMainMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'app_default']);
        $menu->addChild('Projects', ['route' => 'app_project_index']);

        return $menu;
    }
}