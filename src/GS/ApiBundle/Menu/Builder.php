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

        $menu->addChild('Profil')->setAttribute('dropdown', true);
        $menu['Profil']->addChild('Profil', array(
            'route' => 'my_account',
        ));
        $menu['Profil']->addChild('Changer mot de passe', array(
            'route' => 'fos_user_change_password',
        ));

        return $menu;
    }

    public function organizerMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Orga')->setAttribute('dropdown', true);
        $menu['Orga']->addChild('Liste des années', array(
            'route' => 'index_year',
        ));
        $menu['Orga']->addChild('Ajouter une année', array(
            'route' => 'add_year',
        ));

        return $menu;
    }

    public function treasurerMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Trésorier')->setAttribute('dropdown', true);
        $menu['Trésorier']->addChild('Liste des paiements', array(
            'route' => 'index_payment',
        ));
        $menu['Trésorier']->addChild('Ajouter un paiement', array(
            'route' => 'add_payment',
        ));

        return $menu;
    }

}
