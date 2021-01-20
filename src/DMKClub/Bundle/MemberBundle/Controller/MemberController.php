<?php
namespace DMKClub\Bundle\MemberBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\AccountBundle\Entity\Account;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Form;

use DMKClub\Bundle\MemberBundle\Entity\Member;
use DMKClub\Bundle\MemberBundle\Form\Handler\MemberHandler;

/**
 *
 * @Route("/member")
 */
class MemberController extends AbstractController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
            TranslatorInterface::class,
            MemberHandler::class,
            'dmkclub_member.member.form' => Form::class,
            UpdateHandlerFacade::class,
        ]);
    }


    /**
     *
     * @Route("/", name="dmkclub_member_index")
     * @AclAncestor("dmkclub_member_view")
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => Member::class,
        ];
    }

    /**
     * Create member form
     *
     * @Route("/create", name="dmkclub_member_create")
     * @Template("DMKClubMemberBundle:Member:update.html.twig")
     * @Acl(
     *      id="dmkclub_member_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKClubMemberBundle:Member"
     * )
     */
    public function createAction()
    {
        return $this->update(new Member());
    }

    /**
     * Update member form
     *
     * @Route("/update/{id}", name="dmkclub_member_update", requirements={"id"="\d+"}, defaults={"id"=0})
     *
     * @Template
     * @Acl(
     *      id="dmkclub_member_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKClubMemberBundle:Member"
     * )
     */
    public function updateAction(Member $entity)
    {
        return $this->update($entity);
    }

    /**
     *
     * @param Member $entity
     *
     * @return array
     */
    protected function update(Member $entity)
    {
        /* @var $handler  \Oro\Bundle\FormBundle\Model\UpdateHandlerFacade */
        $handler = $this->get(UpdateHandlerFacade::class);

        $data = $handler->update(
            $entity,
            $this->get('dmkclub_member.member.form'),
            $this->get(TranslatorInterface::class)->trans('dmkclub.member.message.saved'),
            null,
            $this->get(MemberHandler::class)
        );
        return $data;
    }

    /**
     *
     * @Route("/view/{id}", name="dmkclub_member_view", requirements={"id"="\d+"}))
     * @Acl(
     *      id="dmkclub_member_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKClubMemberBundle:Member"
     * )
     * @Template
     */
    public function viewAction(Member $member)
    {
        return [
            'entity' => $member
        ];
    }

    /**
     *
     * @Route("/widget/info/{id}", name="dmkclub_member_widget_info", requirements={"id"="\d+"}))
     * @AclAncestor("dmkclub_member_view")
     * @Template
     */
    public function infoAction(Member $member)
    {
        return [
            'entity' => $member
        ];
    }

    /**
     *
     * @Route("/widget/additionalinfo/{id}", name="dmkclub_member_widget_additionalinfo", options={"expose"=true}, requirements={"id"="\d+"}))
     * @AclAncestor("dmkclub_member_view")
     * @Template
     */
    public function additionalInfoAction(Member $member)
    {
        return [
            'entity' => $member
        ];
    }

    /**
     * Wird aufgerufen, um im Account einen Abschnitt für die Mitgliedschaft
     * einzublenden.
     * Die Einbindung erfolgt über die placeholder.yml
     * Die Methode stellt die Member-Datensätze des aktuellen Accounts
     * im entsprechenden Channel bereit.
     * Die eigentlichen Datensätze werden dann in der Route
     * dmkclub_member_widget_member_info gerendert.
     *
     * @Route(
     *      "/widget/member-info/account/{accountId}/channel/{channelId}",
     *      name="dmkclub_member_widget_account_member_info",
     *      requirements={"accountId"="\d+", "channelId"="\d+"}
     * )
     * @ParamConverter("account", class="OroAccountBundle:Account", options={"id" = "accountId"})
     * @ParamConverter("channel", class="OroChannelBundle:Channel", options={"id" = "channelId"})
     * @AclAncestor("dmkclub_member_view")
     * @Template
     */
    public function accountMemberInfoAction(Account $account, Channel $channel)
    {
        $entities = $this->getDoctrine()
            ->getRepository('DMKClubMemberBundle:Member')
            ->findBy([
            'account' => $account,
            'dataChannel' => $channel
        ]);

        return [
            'account' => $account,
            'members' => $entities,
            'channel' => $channel
        ];
    }

    /**
     *
     * @Route(
     *        "/widget/member-info/{id}/channel/{channelId}",
     *        name="dmkclub_member_widget_member_info",
     *        requirements={"id"="\d+", "channelId"="\d+"}
     * )
     * @ParamConverter("channel", class="OroChannelBundle:Channel", options={"id" = "channelId"})
     * @AclAncestor("dmkclub_member_view")
     * @Template
     */
    public function memberInfoAction(Member $entity, Channel $channel)
    {
        return [
            'member' => $entity,
            'channel' => $channel
        ];
    }
}
