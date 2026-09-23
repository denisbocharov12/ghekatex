<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref, watch } from 'vue'
import type { Office } from '@site/Types'

/**
 * Карта офисов и производств на OpenStreetMap.
 *
 * Leaflet грузится динамически: библиотека и её стили нужны только на
 * странице контактов и не должны попадать в общий бандл витрины.
 */
const props = defineProps<{ offices: Office[] }>()

const container = ref<HTMLElement | null>(null)
let map: any = null

async function render(): Promise<void> {
    const points = props.offices.filter((office) => office.latitude && office.longitude)

    if (!container.value || points.length === 0) return

    const L = (await import('leaflet')).default
    await import('leaflet/dist/leaflet.css')

    map?.remove()
    map = L.map(container.value, { scrollWheelZoom: false }).setView(
        [points[0].latitude as number, points[0].longitude as number],
        12,
    )

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 18,
    }).addTo(map)

    // Метка — фирменный знак вместо стандартной булавки Leaflet
    const icon = L.icon({
        iconUrl: '/brand/icon_pattern.svg',
        iconSize: [26, 34],
        iconAnchor: [13, 34],
        popupAnchor: [0, -30],
    })

    const markers = points.map((office) =>
        L.marker([office.latitude as number, office.longitude as number], { icon })
            .addTo(map)
            .bindPopup(`<strong>${office.name}</strong><br>${office.address}`),
    )

    if (markers.length > 1) {
        map.fitBounds(L.featureGroup(markers).getBounds().pad(0.25))
    }
}

onMounted(render)
watch(() => props.offices, render, { deep: true })

onBeforeUnmount(() => {
    map?.remove()
    map = null
})
</script>

<template>
  <div
    ref="container"
    class="h-[420px] w-full bg-bone-50 lg:h-[520px]"
    role="application"
    :aria-label="$t('contacts.map')"
  />
</template>
