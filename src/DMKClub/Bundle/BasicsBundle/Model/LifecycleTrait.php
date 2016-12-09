<?php

namespace DMKClub\Bundle\BasicsBundle\Model;

use Doctrine\ORM\Mapping as ORM;

trait LifecycleTrait {
	use CreatedUpdatedTrait;

	/**
	 * Pre persist event listener
	 *
	 * @ORM\PrePersist
	 */
	public function prePersist()
	{
		$this->createdAt = $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
	}

	/**
	 * Pre update event handler
	 *
	 * @ORM\PreUpdate
	 */
	public function preUpdate()
	{
		$this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
	}

}