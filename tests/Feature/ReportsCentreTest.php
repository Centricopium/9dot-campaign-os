<?php

namespace Tests\Feature;

use App\Models\Booth;
use App\Models\Constituency;
use App\Models\House;
use App\Models\Village;
use App\Models\Voter;
use App\Services\Analytics\CampaignAnalyticsService;
use App\Services\Reports\CampaignReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsCentreTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_campaign_reports_can_be_generated_with_an_empty_database(): void
    {
        $service = app(CampaignReportService::class);
        $filters = [
            'constituency_id' => null,
            'taluka' => null,
            'village_id' => null,
            'booth_id' => null,
            'support_level' => null,
        ];

        $this->assertCount(21, CampaignReportService::catalog());

        foreach (array_keys(CampaignReportService::catalog()) as $report) {
            $result = $service->generate($report, $filters, 10);

            $this->assertSame($report, $result['key']);
            $this->assertNotEmpty($result['headers']);
            $this->assertIsArray($result['rows']);
        }
    }

    public function test_campaign_analytics_centre_builds_all_chart_groups_without_data(): void
    {
        $filters = [
            'constituency_id' => null,
            'taluka' => null,
            'village_id' => null,
            'booth_id' => null,
            'support_level' => null,
        ];

        $groups = app(CampaignAnalyticsService::class)->generate($filters);

        $this->assertSame(
            ['overview', 'political', 'demographics', 'survey', 'organisation', 'health', 'maps'],
            array_keys($groups),
        );
        $this->assertCount(25, collect($groups)->flatten(1));
    }

    public function test_booth_president_report_includes_voter_committee_roles(): void
    {
        $constituency = Constituency::create(['name' => 'Test Constituency']);
        $village = Village::create([
            'constituency_id' => $constituency->id,
            'name' => 'Test Village',
            'taluka' => 'Test Taluka',
            'district' => 'Test District',
        ]);
        $booth = Booth::create([
            'village_id' => $village->id,
            'booth_no' => '12',
            'booth_name' => 'Test Booth',
        ]);
        $house = House::create(['booth_id' => $booth->id, 'house_no' => 'A-1']);
        Voter::create([
            'house_id' => $house->id,
            'name' => 'President Person',
            'mobile' => '9999999999',
            'booth_committee_role' => 'Booth President',
        ]);

        $result = app(CampaignReportService::class)->generate('booth_presidents', [
            'constituency_id' => null,
            'taluka' => null,
            'village_id' => null,
            'booth_id' => null,
            'support_level' => null,
        ]);

        $this->assertSame(1, $result['row_count']);
        $this->assertSame('Booth President', $result['rows'][0][5]);
        $this->assertSame('President Person', $result['rows'][0][6]);
        $this->assertSame('9999999999', $result['rows'][0][7]);
    }
}
