<?php

namespace DMKClub\Bundle\MemberBundle\Controller;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Entity\MemberFeeDiscount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MemberFeeDiscountController extends AbstractController
{
	/**
	 * @Route("/info-block/{id}", name="dmkclub_member_memberfeediscount_infoblock", requirements={"id"="\d+"})
	 * @Template
	 * @AclAncestor("dmkclub_member_view")
	 */
	public function infoBlockAction(Member $contact)
	{
		return array(
			'entity' => $contact,
			'memberfeediscount_edit_acl_resource' => 'dmkclub_member_update'
		);
	}

	/**
	 * @Route(
	 *      "/{memberId}/memberfeediscount-create",
	 *      name="dmkclub_member_memberfeediscount_create",
	 *      requirements={"memberId"="\d+"}
	 * )
	 * @Template("DMKClubMemberBundle:MemberFeeDiscount:update.html.twig")
	 * @AclAncestor("dmkclub_member_create")
	 * @ParamConverter("member", options={"id" = "memberId"})
	 */
	public function createAction(Member $member)
	{
		$discount = new MemberFeeDiscount();
		$member->addMemberFeeDiscount($discount);

		// Update member's modification date when an address is changed
		$member->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

		return $this->update($member, $discount);
	}

	/**
	 * @Route(
	 *      "/{memberId}/memberfeediscount-update/{id}",
	 *      name="dmkclub_member_memberfeediscount_update",
	 *      requirements={"memberId"="\d+","id"="\d+"},defaults={"id"=0}
	 * )
	 * @Template
	 * @AclAncestor("dmkclub_member_update")
	 * @ParamConverter("member", options={"id" = "memberId"})
	 */
	public function updateAction(Member $member, MemberFeeDiscount $discount)
	{
		return $this->update($member, $discount);
	}

	/**
	 * @param Member $member
	 * @param MemberFeeDiscount $discount
	 * @return array
	 * @throws BadRequestHttpException
	 */
	protected function update(Member $member, MemberFeeDiscount $discount)
	{
		$responseData = array(
			'saved' => false,
			'member' => $member
		);


		if ($this->get('dmkclub_member.memberfeediscount.form.handler')->process($discount)) {
			$this->getDoctrine()->getManager()->flush();
			$responseData['entity'] = $discount;
			$responseData['saved'] = true;
		}
		/* @var $form \Symfony\Component\Form\Form */
		$form = $this->get('dmkclub_member.memberfeediscount.form');
//		$form->setData($discount);
		$responseData['form'] = $form->createView();
		return $responseData;
	}
}
