<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Security;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ContactMessage;
use App\Services\RateLimiter;

final class ContactController extends Controller
{
    public function index(): void
    {
        $this->render('contact/index', [], [
            'title' => 'Contact — Brazza Hotel, Brazzaville',
            'description' => 'Contactez Brazza Hotel à Brazzaville : coordonnées, formulaire sécurisé, WhatsApp et localisation. Coordonnées officielles à confirmer.',
            'canonical' => route('contact.index'),
        ]);
    }

    public function store(): void
    {
        verify_csrf();

        if (request()->string('company') !== '') {
            $this->redirectRoute('contact.index');
        }

        if (RateLimiter::tooMany('contact', 4, 900)) {
            Session::flash('error', 'Trop de messages envoyés récemment. Merci de patienter quelques minutes.');
            $this->redirectRoute('contact.index');
        }

        $input = request()->body;
        $data = [
            'name' => request()->string('name'),
            'email' => request()->string('email'),
            'phone' => request()->string('phone'),
            'subject' => request()->string('subject'),
            'message' => request()->string('message'),
            'consent' => request()->string('consent'),
        ];

        $errors = Validator::validate($data, [
            'name' => 'required|min:2|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'phone|max:40',
            'subject' => 'required|min:2|max:160',
            'message' => 'required|min:10|max:4000',
        ]);

        if (($data['consent'] !== '1')) {
            $errors['consent'] = 'Veuillez accepter l’utilisation de vos données pour traiter votre message.';
        }

        if ($errors !== []) {
            $this->backWithErrors($errors, $input, '/contact#formulaire');
        }

        try {
            ContactMessage::create([
                'name' => $data['name'],
                'email' => mb_strtolower($data['email']),
                'phone' => $data['phone'] !== '' ? $data['phone'] : null,
                'subject' => $data['subject'],
                'message' => $data['message'],
                'ip_hash' => Security::hashIp(request()->ip),
            ]);
            RateLimiter::hit('contact', 900);
            Session::flash('success', 'Votre message a bien été transmis. L’équipe vous répondra dans les meilleurs délais.');
        } catch (\Throwable $exception) {
            error_log('[contact] ' . $exception->getMessage());
            Session::flash('error', 'Une erreur est survenue. Merci de réessayer ou de nous contacter par téléphone.');
        }

        $this->redirectRoute('contact.index');
    }
}
