<script setup lang="ts">
import { computed } from 'vue'
import {
    Award, BadgeCheck, Box, Calendar, CheckCircle2, Clock, Droplet, Factory, Gem,
    Globe, Handshake, Layers, Leaf, MapPin, Package, Palette, Printer, Recycle,
    Ruler, Scissors, ShieldCheck, Shirt, Sparkles, Star, Target, Truck, Users, Wrench,
} from 'lucide-vue-next'

/**
 * Иконка по имени, выбранному редактором в админке.
 *
 * Набор ограничен намеренно: импорт всей библиотеки утяжелял бы бандл
 * витрины на сотни килобайт, а редактору нужен обозримый список.
 */
const props = withDefaults(defineProps<{ name: string | null; size?: number }>(), { size: 24 })

const registry: Record<string, unknown> = {
    'award': Award,
    'badge-check': BadgeCheck,
    'box': Box,
    'calendar': Calendar,
    'check-circle': CheckCircle2,
    'clock': Clock,
    'droplet': Droplet,
    'factory': Factory,
    'gem': Gem,
    'globe': Globe,
    'handshake': Handshake,
    'layers': Layers,
    'leaf': Leaf,
    'map-pin': MapPin,
    'package': Package,
    'palette': Palette,
    'printer': Printer,
    'recycle': Recycle,
    'ruler': Ruler,
    'scissors': Scissors,
    'shield-check': ShieldCheck,
    'shirt': Shirt,
    'sparkles': Sparkles,
    'star': Star,
    'target': Target,
    'truck': Truck,
    'users': Users,
    'wrench': Wrench,
}

const component = computed(() => registry[props.name ?? ''] ?? Sparkles)
</script>

<template>
  <component :is="component" :size="props.size" />
</template>
