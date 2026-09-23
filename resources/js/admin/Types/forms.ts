/** Описания конфигурации таблиц и форм админки. */

export interface Column {
    key: string
    label: string
    type?: 'text' | 'translated' | 'boolean' | 'image' | 'date' | 'number' | 'badge'
    width?: string
}

export interface RowField {
    key: string
    label: string
    translated?: boolean
    type?: 'text' | 'textarea' | 'select'
    options?: { value: string; label: string }[]
}

export type FieldType =
    | 'text' | 'textarea' | 'html' | 'number' | 'checkbox' | 'select'
    | 'multiselect' | 'date' | 'color' | 'list' | 'rows' | 'media' | 'gallery'

export interface FormField {
    key: string
    label: string
    type: FieldType
    translated?: boolean
    required?: boolean
    hint?: string
    accept?: string
    options?: { value: string | number; label: string }[]
    rowFields?: RowField[]
    half?: boolean
}

export interface GalleryItem {
    id: number
    url: string
    thumb: string
    name: string
    order: number
}

export interface Paginator<T = Record<string, any>> {
    data: T[]
    current_page: number
    last_page: number
    total: number
    links: { url: string | null; label: string; active: boolean }[]
}
