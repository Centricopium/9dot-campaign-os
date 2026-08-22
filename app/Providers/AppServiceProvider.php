<?php

namespace App\Providers;

use App\Models\BoothOrganisation;
use App\Models\CampaignDailyBriefing;
use App\Models\CampaignEvent;
use App\Models\CampaignIssue;
use App\Models\CampaignTask;
use App\Models\Candidate;
use App\Models\CandidateAssessment;
use App\Models\House;
use App\Models\User;
use App\Models\Voter;
use App\Models\VoterImportBatch;
use App\Services\Security\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user): ?bool {
            return $user->isSuperAdmin() ? true : null;
        });

        foreach ([User::class, Voter::class, House::class, Candidate::class, CandidateAssessment::class, CampaignTask::class, CampaignIssue::class, CampaignEvent::class, CampaignDailyBriefing::class, BoothOrganisation::class, VoterImportBatch::class] as $modelClass) {
            $modelClass::created(fn (Model $model) => app(AuditLogger::class)->modelEvent($model, 'created'));
            $modelClass::updated(fn (Model $model) => app(AuditLogger::class)->modelEvent($model, 'updated'));
            $modelClass::deleted(fn (Model $model) => app(AuditLogger::class)->modelEvent($model, 'deleted'));
        }

        Event::listen(Login::class, function (Login $event): void {
            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
            app(AuditLogger::class)->authentication('login', $event->user, 'User signed in');
        });
        Event::listen(Logout::class, fn (Logout $event) => app(AuditLogger::class)->authentication('logout', $event->user, 'User signed out'));
        Event::listen(Failed::class, fn () => app(AuditLogger::class)->authentication('login_failed', null, 'Failed sign-in attempt'));
    }
}
