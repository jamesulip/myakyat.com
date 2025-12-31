<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { Form, Head, Link, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: edit().url,
    },
];

const page = usePage();
const user = page.props.auth.user;

const canUseStravaAvatar = !!(
    user.strava_id &&
    user.strava_avatar_url &&
    (user.avatar ?? '') !== user.strava_avatar_url
);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Profile information"
                    description="Update your name and email address"
                />

                <Form
                    v-bind="ProfileController.update.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing, recentlySuccessful }"
                >
                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            class="mt-1 block w-full"
                            name="name"
                            :default-value="user.name"
                            required
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError class="mt-2" :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            name="email"
                            :default-value="user.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            A new verification link has been sent to your email
                            address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="processing"
                            data-test="update-profile-button"
                            >Save</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </Form>
            </div>

            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Strava"
                    description="Connect your Strava account"
                />

                <div class="space-y-3">
                    <p v-if="user.strava_id" class="text-sm text-muted-foreground">
                        Connected to Strava athlete #{{ user.strava_id }}.
                    </p>
                    <p v-else class="text-sm text-muted-foreground">
                        Not connected.
                    </p>

                    <p v-if="status === 'strava-connected'" class="text-sm font-medium text-green-600">
                        Strava connected.
                    </p>
                    <p v-else-if="status === 'strava-disconnected'" class="text-sm font-medium text-green-600">
                        Strava disconnected.
                    </p>
                    <p v-else-if="status === 'strava-access-denied'" class="text-sm font-medium text-red-600">
                        Strava connection was cancelled.
                    </p>
                    <p v-else-if="status === 'strava-invalid-state'" class="text-sm font-medium text-red-600">
                        Strava connection failed. Please try again.
                    </p>

                    <p
                        v-if="props.status === 'strava-connected' && canUseStravaAvatar"
                        class="text-sm text-muted-foreground"
                    >
                        We found your Strava profile photo. Use it as your avatar?
                    </p>

                    <div class="flex items-center gap-3">
                        <Button v-if="!user.strava_id" as-child>
                            <a href="/settings/strava/redirect">Connect Strava</a>
                        </Button>

                        <Button
                            v-else-if="props.status === 'strava-connected' && canUseStravaAvatar"
                            variant="secondary"
                            as-child
                        >
                            <Link href="/settings/strava/avatar" method="put" as="button">Use Strava avatar</Link>
                        </Button>

                        <Button v-else variant="outline" as-child>
                            <Link href="/settings/strava" method="delete" as="button">Disconnect</Link>
                        </Button>
                    </div>
                </div>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
