<?php

namespace App\Actions\Claim;

use App\Data\Claim\ClaimExperienceData;
use App\Services\Claim\ClaimExperienceCompiler;
use Lorisleiva\Actions\Concerns\AsAction;

class ResolveClaimExperience
{
    use AsAction;

    public function handle(mixed $voucher): ClaimExperienceData
    {
        return app(ClaimExperienceCompiler::class)->compile($voucher);
    }
}
