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

    public function adminMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $menu->addChild('Admin')->setAttribute('dropdown', true);
        $menu['Admin']->addChild('Ajouter une année', array(
            'route' => 'add_year',
        ));
        $menu['Admin']->addChild('Liste des utilisateurs', array(
            'route' => 'index_user',
        ));
        $menu['Admin']->addChild('Liste des inscriptions', array(
            'route' => 'index_registration',
        ));
        $menu['Admin']
            ->addChild('Email list', array(
                'route' => 'lexik_mailer.email_list',
            ))
            ->setAttribute('divider_prepend', true);
        $menu['Admin']->addChild('Layout list', array(
            'route' => 'lexik_mailer.layout_list',
        ));

        return $menu;
    }

}
