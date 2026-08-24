<?php

namespace Tests\Feature;

use Tests\TestCase;

class CampaignAnalyticsMobileLayoutTest extends TestCase
{
    public function test_analytics_tabs_are_touch_scrollable_without_page_overflow(): void
    {
        $view = file_get_contents(resource_path('views/filament/pages/campaign-analytics-centre.blade.php'));

        $this->assertStringContainsString('overflow-x:auto', $view);
        $this->assertStringContainsString('-webkit-overflow-scrolling:touch', $view);
        $this->assertStringContainsString('touch-action:pan-x', $view);
        $this->assertStringContainsString('min-width:0', $view);
        $this->assertStringContainsString('role="tablist"', $view);
        $this->assertStringContainsString('aria-selected=', $view);
    }
}
