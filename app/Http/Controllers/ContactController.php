<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Mail\ContactFormConfirmation;
use App\Mail\ContactFormSubmission;
use App\Models\Product;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index', [
            'treatments' => Treatment::all(),
            'products' => Product::all(),
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'inquiry_type' => 'required|in:treatment,product,other',

            'treatment_id' => 'nullable|integer|exists:treatments,id',
            'product_id' => 'nullable|integer|exists:products,id',

            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $contactData = [
            'inquiry_type' => $validated['inquiry_type'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],

            'treatment_name' => $this->getTreatmentName($validated),
            'product_name' => $this->getProductName($validated),
        ];

        Mail::to($validated['email'])
            ->send(new ContactFormConfirmation($contactData));

        $owner = User::where('role', Role::Owner)->first();

        if ($owner) {
            Mail::to($owner->email)
                ->send(new ContactFormSubmission($contactData));
        }

        return redirect()
            ->route('contact.index')
            ->with('success', 'Bedankt voor uw bericht. We nemen snel contact op.');
    }

    private function getTreatmentName(array $data): ?string
    {
        if ($data['inquiry_type'] !== 'treatment' || empty($data['treatment_id'])) {
            return null;
        }

        return Treatment::find($data['treatment_id'])?->name;
    }

    private function getProductName(array $data): ?string
    {
        if ($data['inquiry_type'] !== 'product' || empty($data['product_id'])) {
            return null;
        }

        return Product::find($data['product_id'])?->name;
    }
}