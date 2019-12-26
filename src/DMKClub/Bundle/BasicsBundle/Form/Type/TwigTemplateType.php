<?php
namespace DMKClub\Bundle\BasicsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

use DMKClub\Bundle\BasicsBundle\PDF\Manager;

class TwigTemplateType extends AbstractType
{

    /** @var TranslatorInterface */
    protected $translator;

    protected $pdfManager;

    /**
     *
     * @param TranslatorInterface $translator
     * @param Manager $pdfManager
     */
    public function __construct(TranslatorInterface $translator, Manager $pdfManager)
    {
        $this->translator = $translator;
        $this->pdfManager = $pdfManager;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildPlainFields($builder, $options);
        $this->buildRelationFields($builder, $options);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                'required' => true,
                'label' => 'dmkclub.basics.twigtemplate.name.label'
            ])
            ->add('template', TextareaType::class, [
                'required' => false,
                'label' => 'dmkclub.basics.twigtemplate.template.label',
                'attr' => [
                    'class' => 'template-editor',
                    'style' => 'width:100%;min-height:300px'
                    // 'data-wysiwyg-enabled' => true,
                ]
                // 'wysiwyg_options' => [
                // 'height' => '250px'
                // ]
            ])
            ->add('generator', ChoiceType::class, [
                'required' => false,
                'label' => 'dmkclub.basics.twigtemplate.generator.label',
                'choices' => $this->pdfManager->getVisibleGeneratorChoices(),
                'placeholder' => 'dmkclub.form.choose'
            ])
            ->add('orientation', ChoiceType::class, [
                'label' => 'dmkclub.basics.twigtemplate.orientation.label',
                'choices' => [
                    'P' => 'Portrait',
                    'L' => 'Landscape'
                ]
            ])
            ->add('pageFormat', TextareaType::class, [
                'required' => true,
                'label' => 'dmkclub.basics.twigtemplate.page_format.label'
            ]);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {}

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate',
            'cascade_validation' => true
        ]);
    }
}
