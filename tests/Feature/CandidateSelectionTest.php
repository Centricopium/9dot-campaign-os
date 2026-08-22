<?php

namespace Tests\Feature;

use App\Filament\Pages\CandidateStrategy;
use App\Models\Candidate;
use App\Models\CandidateAssessment;
use App\Models\Constituency;
use App\Models\Permission;
use App\Models\PoliticalParty;
use App\Models\User;
use App\Services\Reports\CampaignReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CandidateSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_assessment_calculates_weighted_score(): void
    {
        $candidate = $this->candidate('Candidate A');
        $assessor = User::factory()->create();

        $assessment = CandidateAssessment::create([
            'candidate_id' => $candidate->id,
            'assessor_id' => $assessor->id,
            'winnability_score' => 80,
            'constituency_connect_score' => 70,
            'organisation_strength_score' => 90,
            'public_work_score' => 75,
            'integrity_score' => 85,
            'leadership_score' => 80,
            'party_loyalty_score' => 90,
            'campaign_readiness_score' => 70,
            'compliance_score' => 95,
            'risk_score' => 15,
            'confidence_score' => 90,
            'evidence_notes' => 'Verified public records.',
            'remarks' => 'Strong candidate.',
            'is_submitted' => true,
        ]);

        $this->assertSame('80.50', $assessment->overall_score);
        $this->assertNotNull($assessment->submitted_at);

        $report = app(CampaignReportService::class)->generate('candidate_selection', [
            'constituency_id' => null,
            'taluka' => null,
            'village_id' => null,
            'booth_id' => null,
            'support_level' => null,
        ]);

        $this->assertSame('Strongly Recommended', $report['rows'][0][10]);
    }

    public function test_only_one_candidate_can_be_final_in_the_same_selection_pool(): void
    {
        $first = $this->candidate('Candidate A');
        $second = Candidate::create([
            'constituency_id' => $first->constituency_id,
            'political_party_id' => $first->political_party_id,
            'election_name' => $first->election_name,
            'election_year' => $first->election_year,
            'name' => 'Candidate B',
        ]);
        $selector = User::factory()->create();

        $first->selectAsFinal($selector, 'First decision');
        $second->selectAsFinal($selector, 'Final committee decision');

        $this->assertFalse($first->fresh()->is_final);
        $this->assertSame('Not Selected', $first->fresh()->status);
        $this->assertTrue($second->fresh()->is_final);
        $this->assertSame('Final Selected', $second->fresh()->status);
        $this->assertSame($selector->id, $second->fresh()->finalised_by);
    }

    public function test_authorized_user_can_open_candidate_selection_pages(): void
    {
        foreach ([
            'candidate.view',
            'candidate.create',
            'candidate_assessment.view',
            'candidate_assessment.create',
        ] as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        $user = User::factory()->create(['is_active' => true]);
        $user->givePermissionTo([
            'candidate.view',
            'candidate.create',
            'candidate_assessment.view',
            'candidate_assessment.create',
        ]);

        $this->assertTrue($user->can('candidate.view'));

        $this->actingAs($user)
            ->get('/admin/candidate-strategy')
            ->assertOk();

        $this->get('/admin/candidates')
            ->assertOk();

        $this->get('/admin/candidates/create')
            ->assertOk();

        $this->get('/admin/candidate-assessments')
            ->assertOk();

        $this->get('/admin/candidate-assessments/create')
            ->assertOk();
    }

    public function test_strategy_dashboard_loads_candidates_for_selected_assembly_only(): void
    {
        $selected = $this->candidate('Selected Assembly Candidate');
        $otherConstituency = Constituency::create(['name' => 'Other Assembly']);
        Candidate::create([
            'constituency_id' => $otherConstituency->id,
            'political_party_id' => $selected->political_party_id,
            'election_name' => 'Assembly Election',
            'election_year' => 2027,
            'name' => 'Other Assembly Candidate',
        ]);

        $page = new CandidateStrategy;
        $page->constituencyId = (string) $selected->constituency_id;

        $this->assertSame(
            ['Selected Assembly Candidate'],
            $page->getCandidatesProperty()->pluck('name')->all(),
        );
    }

    private function candidate(string $name): Candidate
    {
        $constituency = Constituency::create(['name' => 'Test Assembly']);
        $party = PoliticalParty::create(['name' => 'Test Party', 'short_name' => 'TP']);

        return Candidate::create([
            'constituency_id' => $constituency->id,
            'political_party_id' => $party->id,
            'election_name' => 'Assembly Election',
            'election_year' => 2027,
            'name' => $name,
        ]);
    }
}
