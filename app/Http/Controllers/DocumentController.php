<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\JobCard;
use App\Models\MeasurementProfile;
use App\Services\UnicodePdfService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(
        protected UnicodePdfService $pdfService
    ) {}

    public function invoicePdf(Request $request, Invoice $invoice)
    {
        $locale = $request->get('locale', 'en');
        $pdf = $this->pdfService->generateInvoicePdf($invoice, $locale);

        if ($request->has('download')) {
            return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
        }

        return $pdf->stream("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function measurementSheetPdf(Request $request, Customer $customer, MeasurementProfile $profile)
    {
        $locale = $request->get('locale', 'en');
        $pdf = $this->pdfService->generateMeasurementSheetPdf($customer, $profile, $locale);

        if ($request->has('download')) {
            return $pdf->download("Measurements-{$customer->customer_code}.pdf");
        }

        return $pdf->stream("Measurements-{$customer->customer_code}.pdf");
    }

    public function jobCardPdf(Request $request, JobCard $jobCard)
    {
        $locale = $request->get('locale', 'en');
        $pdf = $this->pdfService->generateJobCardPdf($jobCard, $locale);

        if ($request->has('download')) {
            return $pdf->download("JobCard-{$jobCard->job_number}.pdf");
        }

        return $pdf->stream("JobCard-{$jobCard->job_number}.pdf");
    }

    public function deliverySlipPdf(Request $request, Delivery $delivery)
    {
        $locale = $request->get('locale', 'en');
        $pdf = $this->pdfService->generateDeliverySlipPdf($delivery, $locale);

        if ($request->has('download')) {
            return $pdf->download("Delivery-{$delivery->delivery_number}.pdf");
        }

        return $pdf->stream("Delivery-{$delivery->delivery_number}.pdf");
    }
}
