/*
| Статический экспорт витрины для GitHub Pages.
|
| Скрипт обходит поднятое приложение по списку `php artisan site:urls`,
| раскладывает ответы файлами `<путь>/index.html` и копирует публичные
| ассеты. Корневые пути переписываются под подкаталог, в котором Pages
| отдаёт проект (`https://<логин>.github.io/<репозиторий>/`).
|
| Запуск:
|   node tools/static-export.mjs --origin=http://127.0.0.1:8000 --base=/ghekatex --out=dist
*/

import fs from 'node:fs/promises'
import path from 'node:path'
import { execFileSync } from 'node:child_process'

const args = Object.fromEntries(
    process.argv.slice(2).map((item) => {
        const [key, value = ''] = item.replace(/^--/, '').split('=')

        return [key, value]
    }),
)

const origin = (args.origin || 'http://127.0.0.1:8000').replace(/\/$/, '')
const base = args.base === undefined ? '' : `/${args.base.replace(/^\/|\/$/g, '')}`.replace(/^\/$/, '')
const outDir = path.resolve(args.out || 'dist')

/** Заменять ли абсолютные адреса приложения на путь публикации. */
const rewriteOrigin = 'rewrite-origin' in args

/** Каталоги `public`, которые уезжают в статику как есть. */
const assetDirs = ['build', 'brand', 'media', 'storage']

/*
| Корневые пути, зашитые в разметку и в собранный бандл.
|
| На Pages проект живёт в подкаталоге, поэтому `/brand/icon.svg`
| превращается в `/ghekatex/brand/icon.svg`. Префиксы достаточно
| характерные, чтобы замена по строке была безопасной.
*/
const rootPrefixes = ['/build/', '/brand/', '/media/', '/storage/', '/favicon.ico']

function withBase(text) {
    let result = text

    /*
    | Локальная проверка выгрузки: приложение отдаёт абсолютные адреса со
    | своим origin. В CI вместо этого выставляется APP_URL будущего сайта,
    | и заменять уже нечего.
    */
    if (rewriteOrigin) {
        result = result.replaceAll(origin, base)
        result = result.replaceAll(origin.replaceAll('/', '\\/'), base.replaceAll('/', '\\/'))
    }

    if (base === '') {
        return result
    }

    for (const prefix of rootPrefixes) {
        // Ловим значения атрибутов и строковые литералы в бандле
        result = result.replaceAll(`"${prefix}`, `"${base}${prefix}`)
        result = result.replaceAll(`'${prefix}`, `'${base}${prefix}`)
        result = result.replaceAll(`(${prefix}`, `(${base}${prefix}`)
        result = result.replaceAll(`=${prefix}`, `=${base}${prefix}`)

        /*
        | Пропсы страницы лежат в атрибуте `data-page` как JSON, а PHP
        | экранирует там слеши. Без этой замены картинки из медиатеки
        | остаются с корневым путём и не открываются.
        */
        const escaped = prefix.replaceAll('/', '\\/')
        const escapedBase = base.replaceAll('/', '\\/')

        result = result.replaceAll(`"${escaped}`, `"${escapedBase}${escaped}`)
        result = result.replaceAll(`&quot;${escaped}`, `&quot;${escapedBase}${escaped}`)
    }

    return result
}

async function writeFile(relative, contents) {
    const target = path.join(outDir, relative)

    await fs.mkdir(path.dirname(target), { recursive: true })
    await fs.writeFile(target, contents)
}

async function copyAssets() {
    for (const dir of assetDirs) {
        const source = path.resolve('public', dir)

        try {
            await fs.access(source)
        } catch {
            console.warn(`  пропуск public/${dir} — каталога нет`)

            continue
        }

        // `public/storage` — симлинк, а Pages симлинки не отдаёт
        await fs.cp(source, path.join(outDir, dir), { recursive: true, dereference: true })
        console.log(`  скопирован public/${dir}`)
    }

    // Внутри собранного бандла тоже лежат корневые пути
    if (base !== '') {
        await rewriteTree(path.join(outDir, 'build'))
    }
}

async function rewriteTree(dir) {
    let entries = []

    try {
        entries = await fs.readdir(dir, { withFileTypes: true })
    } catch {
        return
    }

    for (const entry of entries) {
        const target = path.join(dir, entry.name)

        if (entry.isDirectory()) {
            await rewriteTree(target)

            continue
        }

        if (!/\.(js|css|json|map)$/.test(entry.name)) {
            continue
        }

        const contents = await fs.readFile(target, 'utf8')

        await fs.writeFile(target, withBase(contents))
    }
}

async function main() {
    /*
    | При запущенном `npm run dev` Laravel подставляет в разметку адрес
    | Vite вместо собранных файлов — выгрузка получилась бы нерабочей.
    */
    try {
        await fs.access(path.resolve('public/hot'))

        console.error('Найден public/hot: остановите dev-сервер Vite и выполните npm run build')
        process.exit(1)
    } catch {
        // Файла нет — значит используется собранный бандл
    }

    const paths = execFileSync('php', ['artisan', 'site:urls'], { encoding: 'utf8' })
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean)

    console.log(`Экспорт ${paths.length} страниц из ${origin} в ${outDir}`)

    await fs.rm(outDir, { recursive: true, force: true })
    await fs.mkdir(outDir, { recursive: true })

    let failed = 0

    for (const route of paths) {
        const response = await fetch(`${origin}${route}`, { redirect: 'follow' })

        if (!response.ok) {
            console.error(`  ${response.status} ${route}`)
            failed += 1

            continue
        }

        const html = withBase(await response.text())

        await writeFile(path.join(route.replace(/^\//, ''), 'index.html'), html)
    }

    // Корень репозитория уводит на язык по умолчанию
    const fallback = paths[0] ?? '/ro'

    await writeFile(
        'index.html',
        `<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0; url=.${fallback}/">
    <link rel="canonical" href="${base}${fallback}/">
    <title>GHEKATEX</title>
</head>
<body>
    <a href=".${fallback}/">GHEKATEX</a>
</body>
</html>
`,
    )

    // Pages отдаёт 404.html на любой неизвестный адрес
    const notFound = await fetch(`${origin}${fallback}/stranica-ne-naydena-404`)

    await writeFile('404.html', withBase(await notFound.text()))

    for (const file of ['sitemap.xml', 'robots.txt']) {
        const response = await fetch(`${origin}/${file}`)

        if (response.ok) {
            await writeFile(file, await response.text())
        }
    }

    // Иначе Pages прогоняет выгрузку через Jekyll и режет служебные файлы
    await writeFile('.nojekyll', '')

    console.log('Копирование ассетов…')
    await copyAssets()

    if (failed > 0) {
        console.error(`Не удалось получить страниц: ${failed}`)
        process.exit(1)
    }

    console.log('Готово')
}

await main()
