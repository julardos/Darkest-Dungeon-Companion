import puppeteer from 'puppeteer'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const BASE = 'http://localhost:3001'
const OUT  = path.join(__dirname, '..', 'docs', 'screenshots') + '/'

const sleep = ms => new Promise(r => setTimeout(r, ms))

const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox'] })
const page    = await browser.newPage()
await page.setViewport({ width: 1920, height: 1080, deviceScaleFactor: 1 })

async function shot(filename, fn) {
  await fn()
  await sleep(1200)
  await page.screenshot({ path: OUT + filename, fullPage: false })
  console.log('✓', filename)
}

// ── 1. Team Builder — heroes placed with synergy active ──────────────────────
await shot('02-team-synergy.png', async () => {
  await page.goto(BASE, { waitUntil: 'networkidle0' })
  await sleep(600)

  async function tapPlace(heroNameFragment, rankText) {
    const items = await page.$$('aside ul li')
    for (const li of items) {
      const txt = await li.$eval('.font-cinzel', el => el.textContent.trim()).catch(() => '')
      if (txt.toLowerCase().includes(heroNameFragment.toLowerCase())) {
        await li.click(); await sleep(400); break
      }
    }
    const slots = await page.$$('.grid.grid-cols-4 > div')
    for (const slot of slots) {
      const lbl = await slot.$eval('.font-cinzel', el => el.textContent.trim()).catch(() => '')
      if (lbl === rankText) { await slot.click(); await sleep(300); break }
    }
  }

  await tapPlace('Plague Doctor', 'Rank 4')
  await tapPlace('Bounty Hunter', 'Rank 3')
  await tapPlace('Occultist',     'Rank 2')
  await tapPlace('Hellion',       'Rank 1')
  await sleep(800)
})

// ── 2. Provision Calculator — Ruins medium ───────────────────────────────────
await shot('05-provisions-ruins.png', async () => {
  await page.goto(BASE, { waitUntil: 'networkidle0' })
  const tabs = await page.$$('nav button')
  for (const t of tabs) {
    const txt = await t.evaluate(el => el.textContent.trim())
    if (txt === 'Provisions') { await t.click(); break }
  }
  await sleep(800)
})

// ── 3. Curio Cheat Sheet — top of the grid ───────────────────────────────────
await shot('07-curios.png', async () => {
  const tabs = await page.$$('nav button')
  for (const t of tabs) {
    const txt = await t.evaluate(el => el.textContent.trim())
    if (txt === 'Curio Cheat Sheet') { await t.click(); break }
  }
  await sleep(1000)
})

await browser.close()
console.log('\nDone — docs/screenshots/')
