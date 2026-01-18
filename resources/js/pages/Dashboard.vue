<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ProfileActivitiesLayout from '@/layouts/ProfileActivitiesLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import UserInfo from '../components/UserInfo.vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage();
const user = page.props.auth.user;
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout
        :breadcrumbs="breadcrumbs"
        :content-container="true"
        :content-padded="true"
        content-wrapper-class="mt-[100px]"
    >
        <template #background>
            <div class="w-full overflow-hidden bg-sky-50 dark:bg-slate-950">
                <div class="relative aspect-3/1">
                    <div
                        aria-hidden="true"
                        class="absolute inset-0 bg-linear-to-b from-sky-200 to-white dark:from-sky-950 dark:to-slate-950"
                    />

                    <svg
                        aria-hidden="true"
                        viewBox="0 0 1200 400"
                        class="absolute bottom-0 left-1/2 h-full w-[140%] -translate-x-1/2 opacity-80 dark:opacity-70"
                        preserveAspectRatio="xMidYMax slice"
                    >
                        <path
                            d="M0 320 L180 180 L330 280 L520 120 L700 260 L900 140 L1200 320 L1200 400 L0 400 Z"
                            class="fill-slate-900/10 dark:fill-white/10"
                        />
                        <path
                            d="M0 360 L220 220 L420 330 L620 170 L820 300 L1020 210 L1200 360 L1200 400 L0 400 Z"
                            class="fill-slate-900/15 dark:fill-white/15"
                        />
                    </svg>
                </div>
            </div>
        </template>

        <ProfileActivitiesLayout>
            <template #cover>
                <!-- <PlaceholderPattern /> -->
            </template>

                <template #profile>
                    <div class="flex items-center gap-3">
                        <UserInfo :user="user" :show-email="true" />
                    </div>

                    <div
                        class="relative mt-4 aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                    </div>
                </template>

                <template #activities>
                    <div
                        class="relative min-h-[60vh] overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <!-- <PlaceholderPattern /> -->
                    </div>
                </template>
            </ProfileActivitiesLayout>
    </AppLayout>
</template>
