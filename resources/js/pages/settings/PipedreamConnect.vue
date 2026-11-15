<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit, link } from '@/routes/pipedream/connect';

type SuggestedApp = {
    slug: string;
    label: string;
    description: string;
};

type Props = {
    projectId: string;
    environment: 'development' | 'production';
    configured: boolean;
    connectionStatus?: string;
    defaultApp: string;
    suggestedApps: SuggestedApp[];
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pipedream Connect',
                href: edit(),
            },
        ],
    },
});

const selectedApp = ref(props.defaultApp);
const form = useForm({
    app: selectedApp.value,
});

watch(selectedApp, (app) => {
    form.app = app;
});

const selectedLabel = computed(() => {
    return (
        props.suggestedApps.find((app) => app.slug === selectedApp.value)
            ?.label ?? selectedApp.value
    );
});

const connect = () => {
    form.post(link.url(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Pipedream Connect" />

    <div class="flex flex-col gap-6">
        <Heading
            variant="small"
            title="Pipedream Connect"
            description="Let a signed-in user connect apps through Pipedream's hosted authorization flow."
        />

        <Alert v-if="!configured" variant="destructive">
            <AlertTitle>Credentials needed</AlertTitle>
            <AlertDescription>
                Add PIPEDREAM_CLIENT_ID and PIPEDREAM_CLIENT_SECRET to the
                deployment environment before operators use this page.
            </AlertDescription>
        </Alert>

        <Alert v-if="connectionStatus === '1'">
            <AlertTitle>App connected</AlertTitle>
            <AlertDescription>
                Pipedream returned successfully after authorizing the app.
            </AlertDescription>
        </Alert>

        <Alert v-else-if="connectionStatus === '0'" variant="destructive">
            <AlertTitle>Connection not completed</AlertTitle>
            <AlertDescription>
                Pipedream did not complete the app authorization. You can try
                again safely.
            </AlertDescription>
        </Alert>

        <Card>
            <CardHeader>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="space-y-1">
                        <CardTitle>Hosted app connection</CardTitle>
                        <CardDescription>
                            Project {{ projectId }} runs in
                            {{ environment }}. Tokens are created server-side
                            and are single-use.
                        </CardDescription>
                    </div>

                    <Badge variant="secondary">
                        {{ configured ? 'Ready' : 'Needs credentials' }}
                    </Badge>
                </div>
            </CardHeader>

            <CardContent>
                <form class="space-y-5" @submit.prevent="connect">
                    <div class="grid gap-2">
                        <Label for="pipedream-app">App slug</Label>
                        <Input
                            id="pipedream-app"
                            v-model="selectedApp"
                            name="app"
                            autocomplete="off"
                            placeholder="gmail"
                        />
                        <InputError :message="form.errors.app" />
                        <p class="text-sm text-muted-foreground">
                            Example:
                            <span class="font-medium text-foreground">
                                {{ selectedLabel }}
                            </span>
                            opens directly inside Pipedream Connect.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <button
                            v-for="app in suggestedApps"
                            :key="app.slug"
                            type="button"
                            class="rounded-xl border p-4 text-left transition hover:bg-muted"
                            :class="{
                                'border-primary bg-primary/5':
                                    selectedApp === app.slug,
                            }"
                            @click="selectedApp = app.slug"
                        >
                            <span class="block text-sm font-medium">
                                {{ app.label }}
                            </span>
                            <span
                                class="mt-1 block text-xs text-muted-foreground"
                            >
                                {{ app.description }}
                            </span>
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <Button
                            :disabled="form.processing || !configured"
                            type="submit"
                        >
                            Connect {{ selectedLabel }}
                        </Button>

                        <p class="text-sm text-muted-foreground">
                            You will leave this app to approve access in
                            Pipedream, then return here.
                        </p>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
