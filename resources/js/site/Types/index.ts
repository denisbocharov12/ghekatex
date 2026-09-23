/** Общие типы пропсов витрины. */

export interface MediaImage {
    url: string
    thumb: string
    alt: string | null
}

export interface LinkAction {
    label: string
    url: string
}

export interface Breadcrumb {
    label: string
    url: string | null
}

export interface SeoProps {
    title: string
    description: string | null
    keywords: string | null
    og_title: string | null
    og_description: string | null
    og_image: string | null
    canonical: string | null
    robots: string
    alternates: Record<string, string>
    breadcrumbs: Breadcrumb[]
    schema: Record<string, unknown>[]
}

export interface MenuItem {
    id: number
    label: string
    href: string
    icon: string | null
    external: boolean
    children: MenuItem[]
}

export interface SiteProps {
    menus: Record<string, MenuItem[]>
    general: Record<string, string | null>
    contacts: Record<string, string | null>
    social: Record<string, string | null>
    services: { slug: string; name: string }[]
}

export interface HeroSlide {
    id: number
    eyebrow: string | null
    title: string
    description: string | null
    cta: LinkAction | null
    secondary_cta: LinkAction | null
    overlay: number
    image: string | null
    image_mobile: string | null
    video: string | null
}

export interface Advantage {
    id: number
    icon: string
    title: string
    description: string | null
    value: string | null
    value_suffix: string | null
    is_counter: boolean
}

export interface ProductCategory {
    id: number
    slug: string
    name: string
    description: string | null
    cover: string | null
    products_count?: number
}

export interface Product {
    id: number
    slug: string
    article: string | null
    name: string
    summary: string | null
    cover: string | null
    thumb: string | null
    category: { slug: string; name: string } | null
    description?: string | null
    composition?: string | null
    attributes?: { label: string; value: string }[]
    min_order?: number | null
    lead_time?: number | null
    gallery?: MediaImage[]
    fabrics?: { slug: string; name: string; composition: string | null; weight: number | null; color: string | null; swatch: string | null }[]
    treatments?: { slug: string; name: string; icon: string }[]
}

export interface Service {
    id: number
    slug: string
    icon: string
    name: string
    summary: string | null
    cover: string | null
    description?: string | null
    lead_time?: string | null
    highlights?: { label: string; value: string; icon: string | null }[]
    process_steps?: { title: string; text: string }[]
    gallery?: MediaImage[]
}

export interface Post {
    id: number
    slug: string
    type: string
    title: string
    excerpt: string | null
    published_at: string | null
    reading: number | null
    cover: string | null
    thumb: string | null
    category: { slug: string; name: string } | null
    body?: string | null
    wide?: string | null
    gallery?: MediaImage[]
    author?: string | null
}

export interface MediaEntry {
    id: number
    type: string
    title: string | null
    caption: string | null
    provider: string | null
    video: string | null
    thumb: string | null
    full: string | null
}

export interface MediaAlbum {
    id: number
    slug: string
    title: string
    description: string | null
    cover?: string | null
    count?: number
    previews?: string[]
    items?: MediaEntry[]
}

export interface Office {
    id: number
    type: string
    name: string
    address: string
    city: string | null
    country: string
    postal: string | null
    phones: string[]
    emails: string[]
    hours: string | null
    latitude: number | null
    longitude: number | null
    primary: boolean
    photo: string | null
}

export interface Paginated<T> {
    data: T[]
    meta: { current_page: number; last_page: number; total: number; per_page?: number }
    links: { prev: string | null; next: string | null }
}

export interface FaqEntry {
    id: number
    question: string
    answer: string
}
