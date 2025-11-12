<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $settings = Setting::getValues([
            'branding.logo',
            'branding.display_name',
            'branding.slogan',
            'organization.legal_name',
            'organization.trade_name',
            'organization.cnpj',
            'organization.state_registration',
            'organization.municipal_registration',
            'contact.email',
            'contact.phone',
            'contact.website',
            'contact.portal_url',
            'address.postal_code',
            'address.street',
            'address.number',
            'address.complement',
            'address.neighborhood',
            'address.city',
            'address.state',
        ]);

        $logoPath = $settings['branding.logo'] ?? null;
        $logoUrl = $logoPath ? Storage::disk('public')->url($logoPath) : null;
        $postalCode = $settings['address.postal_code'] ?? null;
        $formattedPostalCode = $postalCode ? preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $postalCode) : null;
        $cnpj = $settings['organization.cnpj'] ?? null;
        $formattedCnpj = $cnpj ? preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cnpj) : null;
        $phone = $settings['contact.phone'] ?? null;
        $formattedPhone = $this->formatPhone($phone);

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_admin' => $request->user()->is_admin,
                    'force_password_change' => $request->user()->force_password_change,
                ] : null,
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'branding' => [
                'logo_url' => $logoUrl,
                'display_name' => $settings['branding.display_name'] ?? 'Whisper',
                'slogan' => $settings['branding.slogan'] ?? null,
            ],
            'organization' => [
                'legal_name' => $settings['organization.legal_name'] ?? null,
                'trade_name' => $settings['organization.trade_name'] ?? null,
                'cnpj' => $formattedCnpj,
                'cnpj_raw' => $cnpj,
                'state_registration' => $settings['organization.state_registration'] ?? null,
                'municipal_registration' => $settings['organization.municipal_registration'] ?? null,
            ],
            'contact' => [
                'email' => $settings['contact.email'] ?? null,
                'phone' => $formattedPhone,
                'phone_raw' => $phone,
                'website' => $settings['contact.website'] ?? null,
                'portal_url' => $settings['contact.portal_url'] ?? null,
            ],
            'address' => [
                'postal_code' => $formattedPostalCode,
                'postal_code_raw' => $postalCode,
                'street' => $settings['address.street'] ?? null,
                'number' => $settings['address.number'] ?? null,
                'complement' => $settings['address.complement'] ?? null,
                'neighborhood' => $settings['address.neighborhood'] ?? null,
                'city' => $settings['address.city'] ?? null,
                'state' => $settings['address.state'] ?? null,
            ],
        ];
    }

    protected function formatPhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $phone);

        if ($digits === '') {
            return null;
        }

        if (strlen($digits) === 11) {
            return preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $digits);
        }

        if (strlen($digits) === 10) {
            return preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $digits);
        }

        return $phone;
    }
}
