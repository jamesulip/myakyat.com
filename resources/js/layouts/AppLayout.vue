<script setup lang="ts">
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];

    contentContainer?: boolean;
    contentPadded?: boolean;
    contentWrapperClass?: string;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],

    contentContainer: false,
    contentPadded: false,
    contentWrapperClass: undefined,
});
</script>

<template>
    <AppHeaderLayout
        :breadcrumbs="breadcrumbs"
        :content-container="contentContainer"
        :content-padded="contentPadded"
        :content-wrapper-class="contentWrapperClass"
    >
        <template v-if="$slots.background" #background>
            <slot name="background" />
        </template>
        <slot />
    </AppHeaderLayout>
</template>
