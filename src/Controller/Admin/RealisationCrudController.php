<?php

namespace App\Controller\Admin;

use App\Entity\Realisation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class RealisationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Realisation::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Réalisation')
            ->setEntityLabelInPlural('Réalisations')
            ->setPageTitle('index', 'Réalisations')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
{
    $fields = [
        BooleanField::new('homePage', 'Afficher sur la page d’accueil'),
        TextField::new('titre', 'Titre'),
    ];

    // Description : afficher directement dans la liste, mais WYSIWYG dans les formulaires
    if ($pageName === Crud::PAGE_INDEX) {
        $fields[] = TextField::new('description', 'Description')
            ->formatValue(fn($value) => strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value);
    } else {
        $fields[] = TextEditorField::new('description', 'Description');
    }

    $fields[] = ImageField::new('image', 'Image')
        ->setBasePath('uploads/realisations')
        ->setUploadDir('public/uploads/realisations')
        ->setRequired(false);

    // Date de création à la fin
    $fields[] = DateTimeField::new('createdAt', 'Date de création')->hideOnForm();

    return $fields;
}

}
