<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class ActualiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Actualite::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Actualité')
            ->setEntityLabelInPlural('Actualités')
            ->setPageTitle('index', 'Actualités') 
            ->setDefaultSort(['ordre' => 'ASC', 'createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            BooleanField::new('actif', 'Publié'),
            IntegerField::new('ordre', 'Ordre'),
            DateTimeField::new('createdAt', 'Date de création')
                ->hideOnForm()
                ->setFormat('dd/MM/yyyy à HH:mm'),
        ];

        if ($pageName === 'index') {
            // Affiche un résumé du contenu dans la liste
            $fields[] = TextField::new('contenu', 'Contenu')
                ->formatValue(fn($value) => strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value);
        } else {
            // Pour le formulaire d'ajout/modification, WYSIWYG
            $fields[] = TextEditorField::new('contenu', 'Contenu');
        }

        return $fields;
    }
}
