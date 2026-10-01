<?php

declare(strict_types=1);

it('uses the cockpit shell for cockpit pages', function (): void {
    $root = dirname(__DIR__, 2);
    $application = file_get_contents($root.'/resources/js/app.ts');

    expect($application)->not->toBeFalse()
        ->toContain("import AppSidebarLayoutCockpit from '@/layouts/app/AppSidebarLayoutCockpit.vue';")
        ->toContain("case name.startsWith('x-change/cockpit/'):")
        ->toContain('return AppSidebarLayoutCockpit;');
});

it('keeps the cockpit header visible on desktop and pull-to-reveal on mobile', function (): void {
    $root = dirname(__DIR__, 2);
    $layout = file_get_contents(
        $root.'/resources/js/layouts/app/AppSidebarLayoutCockpit.vue',
    );

    expect($layout)->not->toBeFalse()
        ->toContain('<div class="hidden md:block">')
        ->toContain("const mobileBreakpoint = '(max-width: 767px)';")
        ->toContain('window.scrollY <= 0')
        ->toContain('touch.clientY <= topGestureBoundary')
        ->toContain('distance >= revealDistance')
        ->toContain('data-testid="cockpit-mobile-revealed-header"')
        ->toContain('fixed inset-x-0 top-0')
        ->toContain('md:hidden')
        ->toContain('window.removeEventListener');
});
