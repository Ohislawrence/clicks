<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentDetailsController extends Controller
{
    /**
     * Update the user's payment details.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:10|min:10',
            'bank_name' => 'nullable|string|max:255',
            'bank_code' => 'nullable|string|max:10',
        ]);

        $user = $request->user();

        // Get existing payment details or create new array
        $paymentDetails = $user->payment_details ?? [];

        // Update with new values
        $paymentDetails = array_merge($paymentDetails, array_filter($validated));

        // Update user
        $user->update([
            'payment_details' => $paymentDetails,
        ]);

        return back()->with('success', 'Payment details updated successfully.');
    }
}
