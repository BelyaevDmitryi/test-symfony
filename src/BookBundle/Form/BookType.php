<?php
namespace BookBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', null ,array('label' => 'Автор'))
            ->add('title',  FileType::class, array('label' => 'Обложка'))
            ->add('book', FileType::class, array('label' => 'Книга'))
            ->add('dateRead', DateTimeType::class, array('label' => 'Дата прочтения'))
            ->add('isDownload', null ,array('label' => 'Разрешить скачивание'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BookBundle\Entity\Book'
        ));
    }
}
