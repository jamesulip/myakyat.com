<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];

    contentContainer?: boolean;
    contentPadded?: boolean;
    contentWrapperClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],

    contentContainer: false,
    contentPadded: false,
    contentWrapperClass: undefined,
});
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent class="relative">
            <div
                v-if="$slots.background"
                class="absolute inset-x-0 top-0 z-0 w-full"
            >
                <slot name="background" />
            </div>

            <div
                class="relative z-10"
                :class="[
                    props.contentContainer
                        ? 'mx-auto w-full px-4 md:max-w-7xl'
                        : '',
                    props.contentPadded ? 'p-4' : '',
                    props.contentWrapperClass,
                ]"
            >
                <slot />
            </div>
        </AppContent>
    </AppShell>
</template>
