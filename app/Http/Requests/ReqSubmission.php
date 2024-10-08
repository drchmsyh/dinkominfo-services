<?php

namespace App\Http\Requests;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;

class ReqSubmission extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'applicant' => 'required|string|max:255',
            'instansi' => 'required|int',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ];

        $service = Service::findOrFail($this->input('service_id'));

        switch ($service->slug) {
            case str_contains($service->slug, 'domain'):
                $rules += [
                    'app_name' => 'required|string|max:255',
                    'desc_name' => 'required|string|max:255',
                    'site' => 'required|string|max:255',
                    'ip' => 'nullable|string|max:255',
                    'add_inform' => 'nullable|string|max:255',
                    'document' => 'required|file|mimes:pdf,docx|max:10240', // Max 10MB

                ];
                break;
            case str_contains($service->slug, 'clearance'):
                $rules += [
                    'title_req' => 'required|string|max:255',
                    'purpose' => 'required|string',
                    'add_inform' => 'required|string|max:255',
                    'documents' => 'required|array',
                    'documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
                ];
                break;
            case str_contains($service->slug, 'vps'):
            case str_contains($service->slug, 'hosting'):
                $rules += [
                    'cpu' => 'required|string|max:255',
                    'ram' => 'required|string|max:255',
                    'storage' => 'required|string|max:255',
                    'purpose' => 'required|string|max:255',
                    'add_inform' => 'nullable|string|max:255',
                    'document' => 'required|file|mimes:pdf,docx|max:10240', // Max 10MB
                ];
                if (str_contains($service->slug, 'vps')) {
                    $rules['os'] = 'required|string|max:255';
                }
                break;
            default:
                $rules += [
                    'purpose' => 'required|string',
                    'add_inform' => 'required|string|max:255',
                    'documents' => 'required|array',
                    'documents.*' => 'file|mimes:pdf,doc,docx|max:10240',
                ];
                break;
        }
        return $rules;
    }


    public function messages(): array
    {
        return [
            'document.mimes' => 'Dokumen harus bertipe: pdf,doc, atau docx',
            'documents.*.mimes' => 'Dokumen harus bertipe: pdf,doc, atau docx',
            'documents.*.max' => 'Dokumen tidak boleh lebih dari 10MB.',
        ];
    }
}
