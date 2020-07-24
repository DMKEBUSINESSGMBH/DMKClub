<?php

namespace DMKClub\Bundle\PaymentBundle\Provider;

use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\PaymentBundle\Model\PaymentInterval;

/**
 *
 * @deprecated
 *
 */
class PaymentIntervalsProvider
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $choices = [
        PaymentInterval::YEARLY  => 'dmkclub.payment_interval.yearly',
        PaymentInterval::HALF_YEARLY  => 'dmkclub.payment_interval.half_yearly',
        PaymentInterval::QUARTERLY  => 'dmkclub.payment_interval.quarterly',
        PaymentInterval::MONTHLY  => 'dmkclub.payment_interval.monthly',
    ];

    /**
     * @var array
     */
    protected $translatedChoices;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        if (null === $this->translatedChoices) {
            $this->translatedChoices = array();
            foreach ($this->choices as $name => $label) {
                $this->translatedChoices[$name] = $this->translator->trans($label);
            }
        }

        return $this->translatedChoices;
    }

    /**
     * @param string $name
     * @return string
     * @throws \LogicException
     */
    public function getLabelByName($name)
    {
        $choices = $this->getChoices();
        if (!isset($choices[$name])) {
            throw new \LogicException(sprintf('Unknown payment interval with name "%s"', $name));
        }

        return $choices[$name];
    }
}
