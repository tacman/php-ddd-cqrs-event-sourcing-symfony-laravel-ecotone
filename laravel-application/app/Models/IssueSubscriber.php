<?php

namespace App\Models;

use App\Infrastructure\EcotoneConfiguration;
use App\Mail\ConfirmReportedIssue;
use App\Models\Event\IssueWasReported;
use Ecotone\Messaging\Attribute\Asynchronous;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\DistributedBus;

class IssueSubscriber
{
    #[Asynchronous(EcotoneConfiguration::NOTIFICATIONS_CHANNEL)]
    #[EventHandler(endpointId: "confirmReceivedIssueNotification")]
    public function sendNotificationToConfirmReceivedIssue(IssueWasReported $event): void
    {
        $issue = Issue::find($event->issueId);

        \Mail::to($issue->email)->send(new ConfirmReportedIssue($event->issueId));
    }

    #[EventHandler]
    public function createTicketInBackofficeService(IssueWasReported $event, DistributedBus $distributedBus): void
    {
        $issue = Issue::find($event->issueId);

        $distributedBus->convertAndSendCommand(
            "backoffice_service",
            "ticket.prepareTicket",
            [
                "ticketId" => $issue->ticketId,
                "ticketType" => "customer-issue",
                "description" => $issue->content
            ]
        );
    }
}
