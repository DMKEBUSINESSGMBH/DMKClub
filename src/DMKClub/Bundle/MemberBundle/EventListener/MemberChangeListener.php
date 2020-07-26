<?php

namespace DMKClub\Bundle\MemberBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberPrivacy;

class MemberChangeListener
{

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if ($entity instanceof Member) {
            if (!$entity->getPrivacy()) {
                $privacy = new MemberPrivacy();
                $em->persist($privacy);
                $privacy->setMember($entity);
                $entity->setPrivacy($privacy);
            }
        }
    }
}
