<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
//            TextField::new('username', 'Nom Utilisateur'),
//            TextField::new('firstname', 'Nom'),
//            TextField::new('lastname', 'Prénom'),
            ArrayField::new('roles'),
            TextField::new('password', 'Mot de passe')
                ->setFormType(PasswordType::class)
                ->hideOnIndex(),
            EmailField::new('email', 'Mail'),
            DateTimeField::new('createdAt')->onlyOnDetail(),
        ];
    }
}
