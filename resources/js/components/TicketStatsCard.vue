<template>
    <div 
        :class="[
            'p-3.5 rounded-xl border transition-all duration-200 cursor-pointer select-none',
            isActive ? activeClass : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-xs'
        ]"
        @click="$emit('click')"
    >
        <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500">{{ title }}</span>
            <span :class="badgeClass">{{ badgeText }}</span>
        </div>
        <div :class="['text-2xl font-bold my-1 tracking-tight', countClass]">
            {{ count }}
        </div>
        <div class="flex items-center justify-between text-xs text-slate-400">
            <span>{{ subtitle }}</span>
            <span v-if="isActive" class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-blue-100 text-blue-700">Aktif</span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    badgeText: { type: String, default: '' },
    badgeClass: { type: String, default: 'text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600' },
    count: { type: [Number, String], default: 0 },
    countClass: { type: String, default: 'text-slate-800' },
    isActive: { type: Boolean, default: false },
    activeClass: { type: String, default: 'bg-blue-50/50 border-blue-400 shadow-sm ring-1 ring-blue-400/30' }
});

defineEmits(['click']);
</script>
