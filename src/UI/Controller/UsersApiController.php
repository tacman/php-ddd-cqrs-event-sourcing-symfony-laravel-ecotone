<?php declare(strict_types=1);

namespace App\UI\Controller;

use App\Application\UserService;
use App\Domain\Ticket\Ticket;
use Ecotone\Modelling\CommandBus;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersApiController
{
    public function __construct(private CommandBus $commandBus) {}

    #[Route("/users", methods: ["POST"])]
    public function register(Request $request): Response
    {
        $name = $request->get("name");
        $this->commandBus->sendWithRouting("registerUser", $name);

        return new RedirectResponse("/");
    }

    #[Route("/users/{id}/activate", methods: ["POST"])]
    public function activate(Request $request): Response
    {
        $id = $request->get("id");
        $this->commandBus->sendWithRouting("activateUser", $id);

        return new RedirectResponse("/");
    }
}