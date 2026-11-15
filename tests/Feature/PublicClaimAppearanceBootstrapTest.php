<?php

test('public claim appearance initializes in the client shell', function (): void {
    $source = file_get_contents(resource_path('js/app.ts'));

    expect($source)
        ->toContain("import { initializePublicClaimAppearance } from '@/composables/usePublicClaimAppearance';")
        ->toContain('if (!ssr) initializePublicClaimAppearance(page);');
});
