<?php

namespace App\Providers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Discussion;
use App\Models\Materials;
use App\Models\Reply;
use App\Models\Submission;
use App\Policies\AssignmentPolicy;
use App\Policies\CoursePolicy;
use App\Policies\DiscussionPolicy;
use App\Policies\MaterialPolicy;
use App\Policies\ReplyPolicy;
use App\Policies\SubmissionPolicy;
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
        $this->registerPolicies();
    }


    public function registerPolicies(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Materials::class, MaterialPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Discussion::class, DiscussionPolicy::class);
        Gate::policy(Reply::class, ReplyPolicy::class);
    }
}
