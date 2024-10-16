<?php

namespace Oro\Bundle\SalesBundle\Form\Type;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\SalesBundle\Entity\Opportunity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpportunityStatusSelectType extends AbstractType
{
    /** @var ConfigManager $configManager */
    private $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['probabilities'])) {
            $view->vars['attr']['data-probabilities'] = json_encode($options['probabilities']);
        }
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver)
    {
        $probabilities = $this->configManager->get(Opportunity::PROBABILITIES_CONFIG_KEY);

        // filter out statuses without probability
        $probabilities = array_filter($probabilities, function ($probability) {
            return null !== $probability;
        });

        // expose as percents
        $probabilities = array_map(
            function ($probability) {
                return round($probability * 100);
            },
            $probabilities
        );

        $resolver->setDefaults(['probabilities' => $probabilities]);
    }

    #[\Override]
    public function getParent(): ?string
    {
        return EnumSelectType::class;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'oro_sales_opportunity_status_select';
    }
}
