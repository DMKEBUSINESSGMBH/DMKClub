<?php

namespace DMKClub\Bundle\MemberBundle\Controller\Api\Rest;

use BadMethodCallException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\NamePrefix;

use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;

/**
 * @RouteResource("memberfeediscount")
 * @NamePrefix("dmkclub_api_")
 */
class MemberFeeDiscountController extends RestController
{
    /**
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete fee discounts",
     *      resource=true
     * )
     * FIXME: Auf ACTION-ACL umstellen. Der Discount hat keine Entity-ACL
     * @Acl(
     *      id="dmkclub_member_memberfeediscount_delete",
     *      type="action",
     *      label="Delete fee discounts",
     *      group="dmkclub"
     * )
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
        throw new BadMethodCallException('This method is unsupported.');
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('dmkclub_member.memberfeediscount.manager.api');
    }
}
