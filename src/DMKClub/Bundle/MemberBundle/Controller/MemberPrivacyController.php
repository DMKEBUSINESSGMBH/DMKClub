<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;
use DMKClub\Bundle\MemberBundle\Entity\MemberPrivacy;

class MemberPrivacyController extends Controller
{

	/**
	 * @Route("/widget/privacyinfo/{id}", name="dmkclub_member_memberprivacy_widget_infoblock", options={"expose"=true}, requirements={"id"="\d+"}))
	 * @AclAncestor("dmkclub_member_view")
	 * @Template
	 */
	public function privacyInfoAction(Member $member)
	{
	    return ['entity' => $member->getPrivacy()];
	}

	/**
	 * @Route(
	 *      "/{memberId}/memberprivacy-update",
	 *      options={"expose"=true},
	 *      name="dmkclub_member_memberprivacy_update",
	 *      requirements={"memberId"="\d+","id"="\d+"}
	 * )
	 * @Template
	 * @AclAncestor("dmkclub_member_update")
	 * @ParamConverter("member", options={"id" = "memberId" })
	 */
	public function updateAction(Member $member, MemberPrivacy $privacy)
	{
	    return $this->update($member, $privacy);
	}

	/**
	 * @param Member $member
	 * @param MemberPrivacy $privacy
	 * @return array
	 * @throws BadRequestHttpException
	 */
	protected function update(Member $member, MemberPrivacy $privacy)
	{
		$responseData = [
			'saved' => false,
			'member' => $member
		];

		if ($this->get('dmkclub_member.memberprivacy.form.handler')->process($privacy)) {
			$this->getDoctrine()->getManager()->flush();
			$responseData['entity'] = $privacy;
			$responseData['saved'] = true;
		}
		/* @var $form \Symfony\Component\Form\Form */
		$form = $this->get('dmkclub_member.member_privacy.form');
		$form->setData($privacy);
		$responseData['form'] = $form->createView();
		return $responseData;
	}
}
