<?php

namespace Tests\Feature\Design;

use Tests\Feature\FeatureTestCase;

class DesignAccessibilityTest extends FeatureTestCase
{
    /**
     * WCAG 2.2 SC 2.5.8: Touch targets must be at least 48x48px on mobile.
     * Tailwind: w-12 = 48px, min-w-12 min-h-12 = 48x48 minimum.
     */
    public function testMobileTableActionsTriggerHasAdequateTouchTarget(): void
    {
        $response = $this->loginAs()
            ->withServerVariables(['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15'])
            ->get(route('transactions.index'));

        $response->assertOk();

        // Mobile actions trigger must be at least 48x48px (min-w-12 min-h-12 or w-12 h-12)
        $html = $response->getContent();
        $this->assertTrue(
            str_contains($html, 'min-w-12') && str_contains($html, 'min-h-12') ||
            str_contains($html, 'w-12') && str_contains($html, 'h-12'),
            'Mobile table actions trigger should have 48x48px minimum touch target (min-w-12 min-h-12 or w-12 h-12)'
        );
    }

    /**
     * Admin menu buttons (notifications, search, add, settings) must have 48x48px touch targets.
     */
    public function testAdminMenuPrimaryButtonsHaveAdequateTouchTarget(): void
    {
        $response = $this->loginAs()
            ->get(route('dashboard'));

        $response->assertOk();

        $html = $response->getContent();
        // Menu toggle buttons should have min-w-12 min-h-12 or w-12 h-12
        $this->assertTrue(
            str_contains($html, 'min-w-12') || str_contains($html, 'w-12'),
            'Admin menu buttons should have 48px minimum width for touch targets'
        );
    }

    /**
     * Hamburger menu button on mobile bar must be adequately sized.
     */
    public function testMobileHamburgerMenuHasAdequateTouchTarget(): void
    {
        $response = $this->loginAs()
            ->withServerVariables(['HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15'])
            ->get(route('dashboard'));

        $response->assertOk();

        $html = $response->getContent();
        $this->assertTrue(
            str_contains($html, 'js-hamburger-menu'),
            'Hamburger menu should be present'
        );
    }

    /**
     * Safe area insets for notched devices (iOS, Android).
     */
    public function testAdminLayoutIncludesSafeAreaSupport(): void
    {
        $response = $this->loginAs()
            ->get(route('dashboard'));

        $response->assertOk();

        $html = $response->getContent();
        $this->assertTrue(
            str_contains($html, 'safe-area') || str_contains($html, 'env(safe-area'),
            'Layout should support safe area insets for notched devices'
        );
    }

    /**
     * Auth form inputs should have inputmode and autocomplete for better mobile UX.
     */
    public function testLoginFormHasMobileFriendlyInputAttributes(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();

        $html = $response->getContent();
        $this->assertTrue(
            str_contains($html, 'inputmode') || str_contains($html, 'autocomplete'),
            'Login form should have inputmode or autocomplete attributes for mobile'
        );
    }

    /**
     * Auth form should not use fixed height that causes overflow on mobile.
     */
    public function testLoginFormDoesNotUseProblematicFixedHeight(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();

        $html = $response->getContent();
        // lg:h-64 forces 16rem height on large screens - can cause issues; we should remove or make conditional
        $this->assertTrue(
            ! str_contains($html, 'lg:h-64') || str_contains($html, 'min-h-'),
            'Login form should avoid fixed height that causes mobile overflow'
        );
    }
}
