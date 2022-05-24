<?php

namespace App\Http\Controllers;

use App\Exceptions\Mailer\ConnexionFailedException;
use App\Exceptions\Mailer\MailboxEmptyException;
use Illuminate\Http\Request;
use PhpImap\Mailbox;
use PhpImap\Exceptions\ConnectionException;


class MailController extends Controller
{

    public function index(Request $request)
    {

        try {
            $mailsIds = $this->getMailsIds($request);
        } catch (ConnexionFailedException $ex) {
            return httpError("IMAP connection failed");
        } catch (MailboxEmptyException $ex) {
            return httpError("Mailbox is empty");
        }

        $mails = [];

        $mailsIds = array_slice($mailsIds, 0, 20, true);

        $mails[] = $this->getMailbox($request)->getMailsInfo($mailsIds);

        return httpSuccess('Derniers mail', $mails);
    }

    public function show(Request $request, $id)
    {
        try {
            $mailbox = $this->getMailbox($request);
        } catch (ConnexionFailedException $ex) {
            return httpError("IMAP connection failed");
        } catch (MailboxEmptyException $ex) {
            return httpError("Mailbox is empty");
        }

        return httpSuccess('Mail', $mailbox->getMail($id));
    }

    private function getMailbox(Request $request)
    {
        $user = $request->user();
        $mailbox = new Mailbox(
            '{webmail.heig-vd.ch:993/imap/ssl}INBOX', // IMAP server and mailbox folder
            $user->username, // Username for the before configured mailbox
            $user->password, // Password for the before configured username
        );

        return $mailbox;
    }

    private function getMailsIds(Request $request): array
    {
        try {
            $mailsIds = array_reverse(($this->getMailbox($request))->searchMailbox('ALL'));
        } catch (ConnectionException $ex) {
            throw new ConnexionFailedException("IMAP connection failed: " . implode(",", $ex->getErrors('all')));
        }

        if (!$mailsIds) {
            throw new MailboxEmptyException();
        }

        return $mailsIds;
    }
}
