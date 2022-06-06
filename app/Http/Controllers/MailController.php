<?php

namespace App\Http\Controllers;

use App\Exceptions\Mailer\ConnexionFailedException;
use App\Exceptions\Mailer\MailboxEmptyException;
use App\Http\Requests\SendMailRequest;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use PhpImap\Mailbox;
use PhpImap\Exceptions\ConnectionException;

/**
 * @group Mail
 *
 * APIs pour gérer les mails
 */
class MailController extends Controller
{

    /**
     * 
     * Get mails
     * 
     * Retourne un json contenant une liste des mails de l'utilisateur.
     * 
     */

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

        $mails = $this->getMailbox($request)->getMailsInfo($mailsIds);

        return httpSuccess('Derniers mail', $mails);
    }

    /**
     * 
     * Show mail
     * 
     * Retourne un json contenant un mail.
     * 
     * 
     */
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

    /**
     * 
     * Send mail
     * 
     * Envoie un mail 
     * 
     */
    public function send(SendMailRequest $request)
    {
        $mail = $request->validated();
        $user = $request->user();

        Config::set('mail.mailers.smtp.host', "smtp.heig-vd.ch");
        Config::set('mail.mailers.smtp.username', $user->username);
        Config::set('mail.mailers.smtp.password', $user->password);
        Config::set('mail.mailers.smtp.port', "587");
        Config::set('mail.mailers.smtp.encryption', "ssl");
        //Config::set('mail.mailers.smtp.auth_mode', "ntlm");



        /*  Mail::send('mails.default', ["mail" => $mail], function ($message) use ($mail, $user) {
            $message->from($user->username . "@heig-vd.ch", $user->username);
            $message->to($mail['to']);
            $message->subject($mail['subject']);
        }); */


        $to      = $mail['to'];
        $subject = $mail['subject'];
        $message = $mail['message'];
        $headers = array(
            'From' => $user->gaps_user->full_name . ' <' . $user->gaps_user->gaps_username . '@heig-vd.ch>',
            'Reply-To' => $user->gaps_user->gaps_username . '@heig-vd.ch',
            'X-Mailer' => 'PHP/' . phpversion()
        );
        try {
            mail($to, $subject, $message, $headers);
            return httpSuccess('Mail envoyé');
        } catch (\Exception $e) {
            return httpError("Mail not sent");
        }
    }

    private function getMailbox(Request $request)
    {
        $user = $request->user();
        $mailbox = new Mailbox(
            config('imap.connection'), // IMAP server and mailbox folder
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
