<?php

namespace App\Controller\Admin;

use App\Entity\Realisation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            ->setDefaultSort([
                'categorie' => 'ASC',
                'position' => 'ASC',
                'createdAt' => 'DESC'
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [

            BooleanField::new(
                'homePage',
                'Afficher sur la page d’accueil'
            ),

            TextField::new(
                'titre',
                'Titre'
            ),

            ChoiceField::new(
                'categorie',
                'Catégorie'
            )
                ->setChoices([
                    'Escaliers métalliques' => 'Escaliers métalliques',
                    'Braseros sur mesure' => 'Braseros sur mesure',
                    'Portails & portillons' => 'Portails & portillons',
                    'Garde-corps & rampes' => 'Garde-corps & rampes',
                    'Mobilier métallique' => 'Mobilier métallique',
                    'Serrurerie sur mesure' => 'Serrurerie sur mesure',
                ])
                ->renderExpanded(false)
                ->renderAsNativeWidget(),

            ChoiceField::new(
                'objectPosition',
                'Focus image'
            )
                ->setChoices([
                    'Centre' => 'center center',
                    'Gauche' => 'left center',
                    'Droite' => 'right center',
                    'Haut' => 'center top',
                    'Bas' => 'center bottom',
                ])
                ->renderExpanded(false)
                ->renderAsNativeWidget(),

        ];

        // ========================= DESCRIPTION =========================

        if ($pageName === Crud::PAGE_INDEX) {

            $fields[] = TextField::new(
                'description',
                'Description'
            )
                ->formatValue(
                    fn($value) =>
                        strlen(strip_tags($value)) > 100
                            ? substr(strip_tags($value), 0, 100) . '...'
                            : strip_tags($value)
                );

        } else {

            $fields[] = TextEditorField::new(
                'description',
                'Description'
            );

        }

        // ========================= IMAGE =========================

        $fields[] = ImageField::new(
            'image',
            'Image'
        )
            ->setBasePath('uploads/realisations')
            ->setUploadDir('public/uploads/realisations')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setRequired(false);

        // ========================= DATE =========================

        $fields[] = DateTimeField::new(
            'createdAt',
            'Date de création'
        )
            ->hideOnForm();

        return $fields;
    }
}