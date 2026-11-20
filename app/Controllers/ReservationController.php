<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\RateLimiter;
use DateTimeImmutable;

final class ReservationController extends Controller
{
    public function create(): void
    {
        $today = new DateTimeImmutable('today');
        $checkIn = $this->validQueryDate('check_in', $today) ? request()->string('check_in') : '';
        $checkOut = $this->validQueryDate('check_out', $today) ? request()->string('check_out') : '';
        $guests = min(10, max(1, request()->integer('guests', 2)));
        $roomId = max(0, request()->integer('room'));

        $this->render('reservation/create', [
            'rooms' => Room::options(),
            'selectedRoom' => $roomId,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'guests' => $guests,
            'today' => $today->format('Y-m-d'),
            'tomorrow' => $today->modify('+1 day')->format('Y-m-d'),
        ], [
            'title' => 'Demande de réservation — Brazza Hotel',
            'description' => 'Envoyez une demande de réservation au Brazza Hotel. La disponibilité est confirmée par l’équipe, jamais automatiquement.',
            'canonical' => route('reservation.create'),
        ]);
    }

    public function store(): void
    {
        verify_csrf();

        if (request()->string('company') !== '') {
            $this->redirectRoute('reservation.create');
        }

        if (RateLimiter::tooMany('reservation', 5, 1200)) {
            Session::flash('error', 'Trop de demandes envoyées récemment. Merci de patienter avant de réessayer.');
            $this->redirectRoute('reservation.create');
        }

        $input = request()->body;
        $data = [
            'room_id' => request()->string('room_id'),
            'guest_name' => request()->string('guest_name'),
            'phone' => request()->string('phone'),
            'email' => request()->string('email'),
            'check_in' => request()->string('check_in'),
            'check_out' => request()->string('check_out'),
            'guests' => request()->string('guests'),
            'message' => request()->string('message'),
            'consent' => request()->string('consent'),
        ];

        $errors = Validator::validate($data, [
            'room_id' => 'required|max:20',
            'guest_name' => 'required|min:2|max:120',
            'phone' => 'required|phone|max:40',
            'email' => 'email|max:190',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'guests' => 'required|max:2',
            'message' => 'max:2000',
        ]);

        $room = ctype_digit($data['room_id']) ? Room::findActive((int) $data['room_id']) : null;
        if ($room === null) {
            $errors['room_id'] = 'Veuillez sélectionner une catégorie de chambre valide.';
        }

        $guests = filter_var($data['guests'], FILTER_VALIDATE_INT);
        if ($guests === false || $guests < 1 || $guests > 10) {
            $errors['guests'] = 'Le nombre de voyageurs doit être compris entre 1 et 10.';
        }

        $today = new DateTimeImmutable('today');
        $checkIn = DateTimeImmutable::createFromFormat('!Y-m-d', $data['check_in']);
        $checkOut = DateTimeImmutable::createFromFormat('!Y-m-d', $data['check_out']);
        $maxDate = $today->modify('+365 days');

        if (!$checkIn || $checkIn->format('Y-m-d') !== $data['check_in']) {
            $errors['check_in'] = 'Veuillez saisir une date d’arrivée valide.';
        } elseif ($checkIn < $today) {
            $errors['check_in'] = 'La date d’arrivée ne peut pas être dans le passé.';
        } elseif ($checkIn > $maxDate) {
            $errors['check_in'] = 'Les demandes sont ouvertes jusqu’à 12 mois à l’avance.';
        }

        if (!$checkOut || $checkOut->format('Y-m-d') !== $data['check_out']) {
            $errors['check_out'] = 'Veuillez saisir une date de départ valide.';
        } elseif ($checkIn !== false && $checkOut <= $checkIn) {
            $errors['check_out'] = 'La date de départ doit être postérieure à la date d’arrivée.';
        } elseif ($checkIn !== false && $checkOut->diff($checkIn)->days > 30) {
            $errors['check_out'] = 'Pour un séjour de plus de 30 nuits, merci de contacter directement l’hôtel.';
        }

        if ($data['consent'] !== '1') {
            $errors['consent'] = 'Veuillez accepter que vos informations soient utilisées pour traiter votre demande.';
        }

        if ($errors !== []) {
            $this->backWithErrors($errors, $input, '/reservation#demande');
        }

        try {
            $reference = Reservation::create([
                'room_id' => (int) $room['id'],
                'guest_name' => $data['guest_name'],
                'phone' => $data['phone'],
                'email' => $data['email'] !== '' ? mb_strtolower($data['email']) : null,
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'guests' => (int) $guests,
                'message' => $data['message'] !== '' ? $data['message'] : null,
                'source' => 'website',
                'ip_hash' => Security::hashIp(request()->ip),
                'user_agent' => request()->userAgent,
            ]);

            RateLimiter::hit('reservation', 1200);
            Session::put('reservation_reference', $reference);
            Session::flash('success', 'Votre demande a bien été enregistrée.');
            $this->redirectRoute('reservation.confirmation');
        } catch (\Throwable $exception) {
            error_log('[reservation] ' . $exception->getMessage());
            Session::flash('error', 'Une erreur technique est survenue. Merci de réessayer ou de nous joindre par téléphone.');
            $this->redirectRoute('reservation.create');
        }
    }

    public function confirmation(): void
    {
        $reference = Session::get('reservation_reference');
        if (!is_string($reference) || $reference === '') {
            $this->redirectRoute('reservation.create');
        }

        $message = 'Bonjour Brazza Hotel, je viens d’envoyer une demande de réservation sur votre site. Référence : ' . $reference;
        $this->render('reservation/confirmation', [
            'reference' => $reference,
            'whatsappUrl' => whatsapp_href($message),
        ], [
            'title' => 'Demande enregistrée — Brazza Hotel',
            'description' => 'Votre demande de réservation a été enregistrée. Elle reste soumise à la confirmation de l’hôtel.',
            'robots' => 'noindex, nofollow',
            'canonical' => route('reservation.confirmation'),
        ]);
    }

    private function validQueryDate(string $key, DateTimeImmutable $today): bool
    {
        $value = request()->string($key);
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value && $date >= $today;
    }
}
