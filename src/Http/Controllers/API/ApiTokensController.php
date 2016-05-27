<?php

namespace KodiCMS\API\Http\Controllers\API;

use KodiCMS\API\Exceptions\Exception;
use KodiCMS\API\Exceptions\PermissionException;
use KodiCMS\API\Http\Controllers\System\Controller;
use KodiCMS\API\Repositories\TokenRepository;

class ApiTokensController extends Controller
{

    /**
     * @param TokenRepository $repository
     */
    public function getKeys(TokenRepository $repository)
    {
        $keys = $repository->findAllByUser($this->currentUser);

        $this->setContent($keys);
    }

    /**
     * @param TokenRepository $repository
     */
    public function putKey(TokenRepository $repository)
    {
        $description = $this->getRequiredParameter('description');
        $this->setContent(
            $repository->createForUser($this->currentUser, $description)
        );
    }

    /**
     * @param TokenRepository $repository
     */
    public function deleteKey(TokenRepository $repository)
    {
        $key = $this->getRequiredParameter('key');

        $this->setContent(
            (bool) $repository->deleteForUser($this->currentUser, $key)
        );
    }
}
