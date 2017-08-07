<?php

namespace GS\ApiBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Profil', array(
            'route' => 'my_account',
        ));

        return $menu;
    }

    public function organizerMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Année')->setAttribute('dropdown', true);
        $menu['Année']->addChild('Liste des années', array(
            'route' => 'index_year',
        ));
        $menu['Année']->addChild('Ajouter une année', array(
            'route' => 'add_year',
        ));

        return $menu;
    }

}
