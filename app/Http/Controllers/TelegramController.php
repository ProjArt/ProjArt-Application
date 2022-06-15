<?php

namespace App\Http\Controllers;

use App\Http\Services\GapsEventsService;
use App\Http\Services\GapsMarksService;
use App\Models\Absence;
use App\Models\Meal;
use App\Models\TelegramChat;
use App\Models\User;
use DefStudio\Telegraph\Models\TelegraphBot;

class TelegramController extends Controller
{

    private $chat;
    private $command;
    private $message;
    private $params;

    public function handle()
    {
        $content = file_get_contents("php://input");
        $update = json_decode($content, true);
        $chat_id = $update["message"]["chat"]["id"];
        $message = $update["message"]["text"];
        $chat = TelegramChat::where('chat_id', $chat_id)->first();

        if (!$chat) {
            $bot = TelegraphBot::fromToken(env("TELEGRAM_BOT_TOKEN"));
            $chat = $bot->chats()->create([
                'chat_id' => $chat_id,
                "name" => $chat_id
            ]);
        }

        $this->chat = $chat;

        $answer = $this->buildAnswer($message);
        if ($answer) {
            $answer->send();
        }
    }

    private function buildAnswer($message)
    {
        if (str_starts_with($message, "/")) {
            $this->message = $message;
            $arr = explode(" ", $message);
            $this->command = $arr[0];
            $this->params = array_slice($arr, 1);
            switch ($this->command) {
                case "/start":
                    return $this->start();
                case "/gaps":
                    return $this->gaps();
                case "/prochain":
                    return $this->prochain();
                case "/prochains":
                    return $this->prochains();
                case "/notes":
                    return $this->notes();
                case "/absences":
                    return $this->absences();
                case "/menu":
                    return $this->menu();
                case "/moi":
                    return $this->moi();
                case "/supprimer":
                    return $this->supprimer();
            }
        }
        return null;
    }

    private function start()
    {
        return $this->chat->html("Hello! Voici les commandes disponibles : \n/start\n/gaps pour se connecter.\n\nUne fois connecter voici les commandes disponibles : \n/prochain\n/prochains\n/notes\n/moi\n/supprimer");
    }

    private function gaps()
    {
        $user = $this->chat->users()->first();

        if ($this->params) {
            $username = $this->params[0];
            $password = $this->params[1];
            $u = User::whereUsername($username)->first();
            $passwordCorrect = $u?->password == $password;
            if (!($u != null && $passwordCorrect)) {
                return $this->chat->html("Les données entrées sont incorrects");
            }
            $this->chat->users()->attach($u->id);
        } else if (!$user) {
            return $this->chat->html("Veuillez entrer votre nom d'utilisateur GAPS et votre mot de passe : (avec la commande /gaps p.ex : /gaps john.doe password)");
        }
        return $this->chat->html("Vous pouvez à présent utiliser Gaps avec les commandes:\n/prochain\n/prochains 3\n/notes\n/moi\n/supprimer");
    }



    private function prochain()
    {
        $gaps = $this->chat->users()->first();
        if ($gaps) {
            $horaires = new GapsEventsService($gaps);
            return $this->chat->html($horaires->fetchFuturesHoraires(1));
        } else {
            return $this->chat->html("Vous n'êtes pas connecter à Gaps.\n/gaps");
        }
    }

    private function prochains()
    {

        $nbr = isset($this->params[0]) ? $this->params[0] : 3;
        $gaps = $this->chat->users()->first();
        if ($gaps) {
            $horaires = new GapsEventsService($gaps);

            return $this->chat->html($horaires->fetchFuturesHoraires($nbr));
        } else {
            return $this->chat->html("Vous n'êtes pas connecter à Gaps.\n/gaps");
        }
    }

    private function notes()
    {
        $gaps = $this->chat->users()->first();
        if ($gaps) {
            $notes = new GapsMarksService($gaps);

            return $this->chat->html($notes->fetchNotes());
        } else {
            return $this->chat->html("Vous n'êtes pas connecter à Gaps.\n/gaps");
        }
    }

    private function moi()
    {
        $gaps = $this->chat->users()->first();
        if ($gaps) {
            return $this->chat->html("Vous êtes connecté à Gaps avec le nom d'utilisateur : " . $gaps->username);
        } else {
            return $this->chat->html("Vous n'êtes pas connecté à Gaps.\n/gaps");
        }
    }

    private function supprimer()
    {
        $gaps = $this->chat->users()->first();
        if ($gaps) {
            $this->chat->users()->detach($gaps->id);
            $gaps->delete();
            return $this->chat->html("Vous avez été déconnecté de Gaps.");
        }
    }

    private function menu()
    {
        $user = $this->chat->users()->first();
        if ($user) {
            $meals = Meal::today()->get();

            if (!$meals) {
                return $this->chat->html("Aucun repas n'est prévu aujourd'hui.");
            }

            $s = "";

            foreach ($meals as $meal) {
                $s .= $meal->entry . "\n";
                $s .= $meal->plate . "\n";
                $s .= $meal->dessert . "\n";
                $s .= "\n";
            }

            return $this->chat->html($s);
        }
        return $this->chat->html("Vous n'êtes pas connecté à Gaps.\n/gaps");
    }

    private function absences()
    {
        $user = $this->chat->users()->first();

        if ($user) {
            $absences = $user->absences;

            if (!$absences) {
                return $this->chat->html("Vous n'avez pas d'absence.");
            }

            $s = "";

            foreach ($absences as $absence) {
                $s .= $absence->unity . " : <b>" . $absence->absolute_rate . " %</b>";
                $s .= "\n";
            }

            return $this->chat->html($s);
        }
    }
}
