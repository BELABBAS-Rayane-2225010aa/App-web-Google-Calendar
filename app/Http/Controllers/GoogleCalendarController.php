<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Google\Exception;
use Google_Client;
use Google_Service_Calendar;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Spatie\GoogleCalendar\Event;

class GoogleCalendarController extends Controller
{

    /**
     * Fonction qui permet de récupérer les informations de l'API Google Calendar
     *
     * @return Google_Client : Retourne les informations de l'API Google Calendar
     * @throws Exception
     */
    public function fetch(): Google_Client
    {
        $client = new Google_Client();
        $client->setApplicationName('Google Calendar API PHP Quickstart');
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        $client->setAuthConfig(storage_path('app/google-calendar/service-account-credentials.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        return $client;
    }

    /**
     * Fonction qui permet de créer un événement
     *
     * @param Request $request : Requête HTTP
     * @return RedirectResponse : Redirige vers la page précédente
     */
    public function store(Request $request): RedirectResponse
    {
        // enregiste dans les variables $startTime et $endTime les dates et heures de début et de fin de l'événement
        $startTime = Carbon::parse($request->input('start_date') . ' ' . $request->input('start_time'));
        $endTime = Carbon::parse($request->input('end_date') . ' ' . $request->input('end_time'));

        // Validation des données
        $request->validate([
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
        ]);

        // Création de l'événement ici avec le package spatie/laravel-google-calendar
        Event::create([
            'name' => $request->input('name'),
            'startDateTime' => $startTime,
            'endDateTime' => $endTime,
        ]);

        return redirect()->back()->with('message', 'Evénement créé avec succès!');
    }

    /**
     * Fonction qui permet de récupérer les événements
     *
     * @return View
     * @throws Exception | \Google\Service\Exception
     */
    public function displayAllEvents(): View
    {
        require_once base_path('vendor/autoload.php');

        // Permet de récupérer les informations de l'API Google Calendar
        $client = $this->fetch();

        $service = new Google_Service_Calendar($client);
        $calendarId = env('GOOGLE_CALENDAR_ID');
        $optParams = array(
            'maxResults' => 10,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c'),
        );
        $results = $service->events->listEvents($calendarId, $optParams);
        $events = $results->getItems();

        // Si aucun événement n'est trouvé, on affiche un message d'erreur
        if (empty($events)) {
            $events = [];
        }
        // Sinon, on affiche les événements avec les détails de l'événement
        else {
            $eventList = [];
            foreach ($events as $event) {
                $start = $event->start->dateTime;
//                // Si l'événement n'a pas de date et heure de début, on affiche la date d'en ce momment
                if (empty($start)) {
                    $start = $event->start->date;
                }
                // On ajoute les détails de l'événement dans un tableau
                $eventList[] = [
                    'summary' => $event->getSummary(),
                    'start' => $start,
                    'id' => $event->getId(),
                ];
            }
            $events = $eventList;
        }

        // Retourne la vue avec les événements
        return view('allEvents', ['events' => $events]);
    }

    /**
     * Fonction qui permet de récupérer les événements et les afficher dans une vue
     *
     * @param $id : ID de l'événement
     * @return View : Retourne la vue avec les détails de l'événement
     * @throws Exception
     * @throws \Google\Service\Exception
     */
    public function showUpdateForm($id): View
    {
        // Fetch the event from the Google Calendar API using the ID
        $client = $this->fetch();

        $service = new Google_Service_Calendar($client);
        $calendarId = env('GOOGLE_CALENDAR_ID');
        $event = $service->events->get($calendarId, $id);

        // Convertion des objet de l'événement en array
        $eventDetails = [
            'id' => $event->getId(),
            'summary' => $event->getSummary(),
            'start' => $event->start->dateTime ?? $event->start->date,
            'end' => $event->end->dateTime ?? $event->end->date,
        ];

        // Return la view avec tout les details actuel de l'événement
        return view('updateEvent', ['event' => $eventDetails]);
    }

    /**
     * Fonction qui permet de mettre à jour un événement
     *
     * @param Request $request : Requête HTTP
     * @param $id : ID de l'événement
     * @return RedirectResponse : Redirige vers la page précédente
     * @throws Exception
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // Permet de récupérer les informations de l'API Google Calendar
        $client = $this->fetch();

        $service = new Google_Service_Calendar($client);
        $calendarId = env('GOOGLE_CALENDAR_ID');
        $event = $service->events->get($calendarId, $id);

        // Mise à jour des détails de l'événement
        $event->setSummary($request->input('name'));
        $event->start->dateTime = Carbon::parse($request->input('start_date') . ' ' . $request->input('start_time'))->format('c');
        $event->end->dateTime = Carbon::parse($request->input('end_date') . ' ' . $request->input('end_time'))->format('c');

        // Validation des données
        $request->validate([
            'name' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after_or_equal:start_time',
        ]);

        // Sauvegarder les modifications
        $updatedEvent = $service->events->update($calendarId, $event->getId(), $event);

        return redirect()->back()->with('message', 'Evénement modifié avec succès!');
    }

    /**
     * Fonction qui permet de supprimer un événement
     *
     * @param $id : ID de l'événement
     * @return Redirector|RedirectResponse : Redirige vers la page allEvents
     * @throws Exception
     */
    public function delete($id): Redirector| RedirectResponse
    {
        // Fetch the event from the Google Calendar API using the ID
        $client = $this->fetch();

        $service = new Google_Service_Calendar($client);
        $calendarId = env('GOOGLE_CALENDAR_ID');
        $event = $service->events->get($calendarId, $id);

        // Suppresssion de l'événement
        $service->events->delete($calendarId, $event->getId());

        // Redirige vers la page allEvents avec un message de succès
        return redirect('allEvents')->with('message', 'Evénement supprimé avec succès!');
    }

}
