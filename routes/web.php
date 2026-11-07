<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\OnboardingWizardController;
use App\Http\Controllers\PublicWebsiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Marketing & Information Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicWebsiteController::class, 'index'])->name('home');
Route::get('/about', [PublicWebsiteController::class, 'about'])->name('about');
Route::get('/modules', [PublicWebsiteController::class, 'modules'])->name('modules.index');
Route::get('/modules/{key}', [PublicWebsiteController::class, 'moduleDetail'])->name('modules.show');
Route::get('/solutions/{type}', [PublicWebsiteController::class, 'solution'])->name('solution.show');
Route::get('/pricing', [PublicWebsiteController::class, 'pricing'])->name('pricing');
Route::get('/docs', [PublicWebsiteController::class, 'docs'])->name('docs');
Route::get('/contact', [PublicWebsiteController::class, 'contact'])->name('contact');
Route::get('/faq', [PublicWebsiteController::class, 'faq'])->name('faq');
Route::get('/terms', [PublicWebsiteController::class, 'terms'])->name('terms');
Route::get('/privacy', [PublicWebsiteController::class, 'privacy'])->name('privacy');

/*
|--------------------------------------------------------------------------
| Interactive 8-Role Live Demo Switcher
|--------------------------------------------------------------------------
*/
Route::get('/demo', [PublicWebsiteController::class, 'demo'])->name('demo');
Route::post('/demo/login', [PublicWebsiteController::class, 'loginAsDemoUser'])->name('demo.login');

/*
|--------------------------------------------------------------------------
| Self-Service Guided Onboarding Wizard
|--------------------------------------------------------------------------
*/
Route::get('/onboarding', [OnboardingWizardController::class, 'show'])->name('onboarding');
Route::post('/onboarding', [OnboardingWizardController::class, 'process'])->name('onboarding.process');

/*
|--------------------------------------------------------------------------
| Printable / Downloadable Unicode PDF Document Routes
|--------------------------------------------------------------------------
*/
Route::prefix('documents')->middleware('auth')->group(function () {
    Route::get('/invoice/{invoice}', [DocumentController::class, 'invoicePdf'])->name('document.invoice');
    Route::get('/measurement/{customer}/{profile}', [DocumentController::class, 'measurementSheetPdf'])->name('document.measurement');
    Route::get('/job-card/{jobCard}', [DocumentController::class, 'jobCardPdf'])->name('document.jobcard');
    Route::get('/delivery/{delivery}', [DocumentController::class, 'deliverySlipPdf'])->name('document.delivery');
});
