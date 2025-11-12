<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * Exibe a tela de configurações.
     */
    public function index(Request $request): Response
    {
        $settings = $this->getSettingsSnapshot();

        return Inertia::render('Admin/Settings/Index', [
            'brandingSettings' => $settings['branding'],
            'organizationSettings' => $settings['organization'],
            'contactSettings' => $settings['contact'],
            'addressSettings' => $settings['address'],
        ]);
    }

    /**
     * Atualiza todas as configurações em uma única submissão.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branding_display_name' => ['nullable', 'string', 'max:120'],
            'branding_slogan' => ['nullable', 'string', 'max:180'],
            'branding_logo' => ['nullable', 'image', 'max:5120'],
            'branding_remove_logo' => ['nullable', 'boolean'],

            'organization_legal_name' => ['nullable', 'string', 'max:180'],
            'organization_trade_name' => ['nullable', 'string', 'max:140'],
            'organization_cnpj' => ['nullable', 'string', 'max:18'],
            'organization_state_registration' => ['nullable', 'string', 'max:60'],
            'organization_municipal_registration' => ['nullable', 'string', 'max:60'],

            'contact_email' => ['nullable', 'email', 'max:140'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_website' => ['nullable', 'url', 'max:200'],
            'contact_portal_url' => ['nullable', 'url', 'max:200'],

            'address_postal_code' => ['nullable', 'string', 'max:9'],
            'address_street' => ['nullable', 'string', 'max:160'],
            'address_number' => ['nullable', 'string', 'max:20'],
            'address_complement' => ['nullable', 'string', 'max:80'],
            'address_neighborhood' => ['nullable', 'string', 'max:120'],
            'address_city' => ['nullable', 'string', 'max:120'],
            'address_state' => ['nullable', 'string', 'max:2'],
        ]);

        // Identidade visual
        Setting::setValue(
            'branding.display_name',
            $this->nullable($request->input('branding_display_name'))
        );
        Setting::setValue(
            'branding.slogan',
            $this->nullable($request->input('branding_slogan'))
        );

        /** @var \Illuminate\Http\UploadedFile|null $logo */
        $logo = $request->file('branding_logo');
        $removeLogo = $request->boolean('branding_remove_logo');
        $currentLogo = Setting::getValue('branding.logo');

        if ($removeLogo) {
            $this->deleteLogoIfExists($currentLogo);
            Setting::setValue('branding.logo', null);
        } elseif ($logo) {
            $extension = $logo->getClientOriginalExtension();
            $filename = sprintf('logo-%s.%s', Str::uuid(), $extension);
            $storedPath = $logo->storeAs('branding', $filename, 'public');

            $this->deleteLogoIfExists($currentLogo);
            Setting::setValue('branding.logo', $storedPath);
        }

        // Organização
        $cnpjInput = $this->nullable($request->input('organization_cnpj'));
        $cnpjDigits = null;

        if ($cnpjInput) {
            $cnpjDigits = preg_replace('/\D/', '', $cnpjInput);

            if (strlen($cnpjDigits) !== 14) {
                throw ValidationException::withMessages([
                    'organization_cnpj' => 'Informe um CNPJ válido com 14 dígitos.',
                ]);
            }
        }

        Setting::setValue(
            'organization.legal_name',
            $this->nullable($request->input('organization_legal_name'))
        );
        Setting::setValue(
            'organization.trade_name',
            $this->nullable($request->input('organization_trade_name'))
        );
        Setting::setValue('organization.cnpj', $cnpjDigits);
        Setting::setValue(
            'organization.state_registration',
            $this->nullable($request->input('organization_state_registration'))
        );
        Setting::setValue(
            'organization.municipal_registration',
            $this->nullable($request->input('organization_municipal_registration'))
        );

        // Contato
        $phoneDigits = null;
        $phoneInput = $this->nullable($request->input('contact_phone'));

        if ($phoneInput) {
            $digits = preg_replace('/\D/', '', $phoneInput);
            $phoneDigits = $digits !== '' ? $digits : null;
        }

        Setting::setValue(
            'contact.email',
            $this->nullable($request->input('contact_email'))
        );
        Setting::setValue('contact.phone', $phoneDigits);
        Setting::setValue(
            'contact.website',
            $this->nullable($request->input('contact_website'))
        );
        Setting::setValue(
            'contact.portal_url',
            $this->nullable($request->input('contact_portal_url'))
        );

        // Endereço
        $postalCodeInput = $this->nullable($request->input('address_postal_code'));
        $postalCodeDigits = null;

        if ($postalCodeInput) {
            $postalCodeDigits = preg_replace('/\D/', '', $postalCodeInput);

            if (strlen($postalCodeDigits) !== 8) {
                throw ValidationException::withMessages([
                    'address_postal_code' => 'Informe um CEP válido com 8 dígitos.',
                ]);
            }
        }

        Setting::setValue('address.postal_code', $postalCodeDigits);
        Setting::setValue(
            'address.street',
            $this->nullable($request->input('address_street'))
        );
        Setting::setValue(
            'address.number',
            $this->nullable($request->input('address_number'))
        );
        Setting::setValue(
            'address.complement',
            $this->nullable($request->input('address_complement'))
        );
        Setting::setValue(
            'address.neighborhood',
            $this->nullable($request->input('address_neighborhood'))
        );
        Setting::setValue(
            'address.city',
            $this->nullable($request->input('address_city'))
        );

        $stateValue = $this->nullable($request->input('address_state'));
        if ($stateValue) {
            $stateValue = strtoupper($stateValue);
        }
        Setting::setValue('address.state', $stateValue);

        return Redirect::route('admin.settings.index')->with('success', 'Configurações atualizadas com sucesso.');
    }

    protected function deleteLogoIfExists(?string $path): void
    {
        if (! $path) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * @return array{
     *     branding: array<string, mixed>,
     *     organization: array<string, mixed>,
     *     contact: array<string, mixed>,
     *     address: array<string, mixed>,
     * }
     */
    protected function getSettingsSnapshot(): array
    {
        $keys = Setting::getValues([
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

        return [
            'branding' => [
                'logo' => $keys['branding.logo'] ?? null,
                'display_name' => $keys['branding.display_name'] ?? 'Whisper',
                'slogan' => $keys['branding.slogan'] ?? null,
            ],
            'organization' => [
                'legal_name' => $keys['organization.legal_name'] ?? '',
                'trade_name' => $keys['organization.trade_name'] ?? null,
                'cnpj' => $this->formatCnpj($keys['organization.cnpj'] ?? null),
                'cnpj_raw' => $keys['organization.cnpj'] ?? null,
                'state_registration' => $keys['organization.state_registration'] ?? null,
                'municipal_registration' => $keys['organization.municipal_registration'] ?? null,
            ],
            'contact' => [
                'email' => $keys['contact.email'] ?? '',
                'phone' => $this->formatPhone($keys['contact.phone'] ?? null),
                'phone_raw' => $keys['contact.phone'] ?? null,
                'website' => $keys['contact.website'] ?? null,
                'portal_url' => $keys['contact.portal_url'] ?? null,
            ],
            'address' => [
                'postal_code' => $this->formatPostalCode($keys['address.postal_code'] ?? null),
                'postal_code_raw' => $keys['address.postal_code'] ?? null,
                'street' => $keys['address.street'] ?? null,
                'number' => $keys['address.number'] ?? null,
                'complement' => $keys['address.complement'] ?? null,
                'neighborhood' => $keys['address.neighborhood'] ?? null,
                'city' => $keys['address.city'] ?? null,
                'state' => $keys['address.state'] ?? null,
            ],
        ];
    }

    protected function formatCnpj(?string $cnpj): ?string
    {
        if (! $cnpj) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $cnpj);

        if (strlen($digits) !== 14) {
            return $cnpj;
        }

        return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $digits);
    }

    protected function formatPostalCode(?string $postalCode): ?string
    {
        if (! $postalCode) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $postalCode);

        if (strlen($digits) !== 8) {
            return $postalCode;
        }

        return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $digits);
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

    protected function nullable(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
