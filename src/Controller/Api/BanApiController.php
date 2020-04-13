<?php


namespace App\Controller\Api;

use App\Entity\Lock;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class BanApiController extends AbstractApiController
{
    public const ADMIN_BAN = 'admin.ban';

    /**
     * Ban ip.
     *
     * @example
     *   {
     *      "ip": "10.10.0.1"
     *   }
     *
     * @Route("/ban", name="api_ban_ip", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function ban(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = $this->getPayload();
        $ip = $data['ip'] ?? null;

        // ToDo: validate ip

        $bans = $em->getRepository(Lock::class)->findBy(
            [
                'data' => $ip,
                'name' => self::ADMIN_BAN,
            ]
        );

        if (!empty($bans)) {
            return new JsonResponse([]);
        }

        $lock = new Lock(self::ADMIN_BAN);
        $lock->setData($ip);
        $lock->setExpire(null);
        $lock->setValue(1);
        $em->persist($lock);
        $em->flush();

        return new JsonResponse([]);
    }
}
