<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Ellipsis } from 'lucide-vue-next';
import { cockpitSecondaryNavigation } from '@/cockpit/navigation';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
</script>

<template>
    <Sheet v-slot="{ open }">
        <div class="relative h-10 shrink-0 md:hidden">
            <SheetTrigger as-child>
                <Button
                    variant="outline"
                    size="icon-lg"
                    class="absolute top-0 right-2 z-40 size-10 rounded-full bg-background/95 shadow-sm backdrop-blur"
                    aria-label="More Cockpit destinations"
                    :aria-expanded="open"
                    data-testid="cockpit-mobile-more-trigger"
                >
                    <Ellipsis class="size-5" aria-hidden="true" />
                </Button>
            </SheetTrigger>
        </div>

        <SheetContent
            side="bottom"
            class="rounded-t-2xl p-0 pb-[env(safe-area-inset-bottom)] md:hidden"
            data-testid="cockpit-mobile-more-menu"
        >
            <SheetHeader class="border-b text-left">
                <SheetTitle>More</SheetTitle>
                <SheetDescription>
                    Account, guides, and system readiness.
                </SheetDescription>
            </SheetHeader>

            <nav class="grid gap-2 p-4" aria-label="More Cockpit destinations">
                <SheetClose
                    v-for="item in cockpitSecondaryNavigation"
                    :key="item.key"
                    as-child
                >
                    <Link
                        :href="item.href"
                        prefetch
                        class="flex min-h-14 flex-col justify-center rounded-xl border px-4 py-3 text-left transition hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        :data-testid="`cockpit-mobile-more-item-${item.key}`"
                    >
                        <span class="font-semibold">{{ item.label }}</span>
                        <span class="text-sm text-muted-foreground">
                            {{ item.description }}
                        </span>
                    </Link>
                </SheetClose>
            </nav>
        </SheetContent>
    </Sheet>
</template>
