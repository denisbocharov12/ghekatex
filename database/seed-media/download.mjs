/**
 * Скачивает демонстрационные фотографии по манифесту.
 *
 * Запускается вручную: `node database/seed-media/download.mjs`.
 * Файлы не хранятся в репозитории — они большие и легко восстанавливаются.
 */

import { createWriteStream } from 'node:fs'
import { mkdir, readFile, stat } from 'node:fs/promises'
import { dirname, join } from 'node:path'
import { fileURLToPath } from 'node:url'
import { get } from 'node:https'

const here = dirname(fileURLToPath(import.meta.url))

const SIZES = {
    portrait: { w: 1200, h: 1600 },
    landscape: { w: 1600, h: 1200 },
    wide: { w: 1920, h: 1080 },
    square: { w: 800, h: 800 },
}

function download(url, target) {
    return new Promise((resolve, reject) => {
        get(url, { headers: { 'User-Agent': 'GHEKATEX-seed/1.0' } }, (response) => {
            if (response.statusCode >= 300 && response.statusCode < 400 && response.headers.location) {
                response.resume()
                download(response.headers.location, target).then(resolve, reject)
                return
            }

            if (response.statusCode !== 200) {
                response.resume()
                reject(new Error(`HTTP ${response.statusCode} для ${url}`))
                return
            }

            const file = createWriteStream(target)
            response.pipe(file)
            file.on('finish', () => file.close(() => resolve()))
            file.on('error', reject)
        }).on('error', reject)
    })
}

const manifest = JSON.parse(await readFile(join(here, 'manifest.json'), 'utf8'))

let downloaded = 0
let skipped = 0

for (const [group, entries] of Object.entries(manifest)) {
    if (group.startsWith('_')) continue

    const folder = join(here, group)
    await mkdir(folder, { recursive: true })

    for (const [name, [id, size]] of Object.entries(entries)) {
        const target = join(folder, `${name}.jpg`)

        try {
            const info = await stat(target)

            if (info.size > 10_000) {
                skipped++
                continue
            }
        } catch {
            // файла ещё нет — качаем
        }

        const { w, h } = SIZES[size] ?? SIZES.landscape
        const url = `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${w}&h=${h}&q=80&fm=jpg`

        try {
            await download(url, target)
            downloaded++
            process.stdout.write(`. `)
        } catch (error) {
            console.error(`\n${group}/${name}: ${error.message}`)
        }
    }
}

console.log(`\nСкачано: ${downloaded}, пропущено: ${skipped}`)
