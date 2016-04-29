<?php

namespace DMKClub\Bundle\MemberBundle\Accounting;

class ProcessorProvider
{
	/**
	 * @var array
	 */
	protected $processors = [];

	/**
	 * @param ProcessorInterface $processor
	 */
	public function addProcessor(ProcessorInterface $processor)
	{
		$this->processors[$processor->getName()] = $processor;
	}

	/**
	 * @return ProcessorInterface[]
	 */
	public function getProcessors()
	{
		return $this->processors;
	}

	/**
	 * @param string $name
	 * @return ProcessorInterface
	 */
	public function getProcessorByName($name)
	{
		if ($this->hasProcessor($name)) {
			return $this->processors[$name];
		} else {
			throw new \RuntimeException(sprintf('Processor %s is unknown', $name));
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasProcessor($name)
	{
		return isset($this->processors[$name]);
	}

	/**
	 * Scheint für das UI
	 * @return array
	 */
	public function getVisibleProcessorChoices()
	{
		$choices = [];
		foreach ($this->getProcessors() as $processor) {
			$choices[$processor->getName()] = $processor->getLabel();
		}
		return $choices;
	}

}
