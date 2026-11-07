<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\MeasurementProfile;
use App\Models\Order;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use ImranDev\UnicodePdf\UnicodePdfDocument;

class UnicodePdfService
{
    /**
     * Generate printable multi-lingual PDF invoice
     */
    public function generateInvoicePdf(Invoice $invoice, string $locale = 'en'): UnicodePdfDocument
    {
        $direction = in_array($locale, ['ar', 'ur'], true) ? 'rtl' : 'ltr';

        return UnicodePdf::loadView('documents.invoice', [
            'invoice' => $invoice,
            'tenant' => $invoice->tenant,
            'order' => $invoice->order,
            'customer' => $invoice->customer,
            'locale' => $locale,
            'direction' => $direction,
        ])
        ->locale($locale)
        ->direction($direction)
        ->setPaper('a4', 'portrait')
        ->name("Invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Generate printable body measurement specification sheet
     */
    public function generateMeasurementSheetPdf(
        Customer $customer,
        MeasurementProfile $profile,
        string $locale = 'en'
    ): UnicodePdfDocument {
        $direction = in_array($locale, ['ar', 'ur'], true) ? 'rtl' : 'ltr';

        return UnicodePdf::loadView('documents.measurement_sheet', [
            'customer' => $customer,
            'profile' => $profile,
            'template' => $profile->template,
            'tenant' => $profile->tenant,
            'locale' => $locale,
            'direction' => $direction,
        ])
        ->locale($locale)
        ->direction($direction)
        ->setPaper('a4', 'portrait')
        ->name("Measurements-{$customer->customer_code}-{$profile->profile_name}.pdf");
    }

    /**
     * Generate workshop Job Card for tailors and cutters
     */
    public function generateJobCardPdf(JobCard $jobCard, string $locale = 'en'): UnicodePdfDocument
    {
        $direction = in_array($locale, ['ar', 'ur'], true) ? 'rtl' : 'ltr';

        return UnicodePdf::loadView('documents.job_card', [
            'jobCard' => $jobCard,
            'order' => $jobCard->order,
            'item' => $jobCard->orderItem,
            'tenant' => $jobCard->tenant,
            'locale' => $locale,
            'direction' => $direction,
        ])
        ->locale($locale)
        ->direction($direction)
        ->setPaper('a4', 'portrait')
        ->name("JobCard-{$jobCard->job_number}.pdf");
    }

    /**
     * Generate delivery dispatch receipt
     */
    public function generateDeliverySlipPdf(Delivery $delivery, string $locale = 'en'): UnicodePdfDocument
    {
        $direction = in_array($locale, ['ar', 'ur'], true) ? 'rtl' : 'ltr';

        return UnicodePdf::loadView('documents.delivery_slip', [
            'delivery' => $delivery,
            'order' => $delivery->order,
            'tenant' => $delivery->tenant,
            'locale' => $locale,
            'direction' => $direction,
        ])
        ->locale($locale)
        ->direction($direction)
        ->setPaper('a5', 'landscape')
        ->name("Delivery-{$delivery->delivery_number}.pdf");
    }
}
