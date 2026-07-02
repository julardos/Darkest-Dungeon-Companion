import puppeteer from 'puppeteer'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const BASE = 'http://localhost:3001'
const OUT  = path.join(__dirname, '..', 'docs', 'screenshots') + '/'

const sleep = ms => new Promise(r => setTimeout(r, ms))

const browser = await puppeteer.launch({ headless: true, args: ['--no-sandbox'] })
const page    = await browser.newPage()

async function shot(filename, fn) {
  await fn()
  await sleep(1400)
  await page.screenshot({ path: OUT + filename, fullPage: true })
  console.log('✓', filename)
}

// ── 1. Team Builder – empty ──────────────────────────────────────────────────
await page.setViewport({ width: 1280, height: 800, deviceScaleFactor: 2 })
await shot('01-team-builder.png', async () => {
  await page.goto(BASE, { waitUntil: 'networkidle0' })
})

// ── 2. Team Builder – heroes placed, synergy active ─────────────────────────
await shot('02-team-synergy.png', async () => {
  await page.goto(BASE, { waitUntil: 'networkidle0' })
  await sleep(600)

  // Use tap-to-place: click hero in roster, then click rank slot
  async function tapPlace(heroNameFragment, rankText) {
    const items = await page.$$('aside ul li')
    for (const li of items) {
      const txt = await li.$eval('.font-cinzel', el => el.textContent.trim()).catch(() => '')
      if (txt.toLowerCase().includes(heroNameFragment.toLowerCase())) {
        await li.click(); await sleep(500)
        break
      }
    }
    const slots = await page.$$('.grid.grid-cols-4 > div')
    for (const slot of slots) {
      const lbl = await slot.$eval('.font-cinzel', el => el.textContent.trim()).catch(() => '')
      if (lbl === rankText) { await slot.click(); await sleep(400); break }
    }
  }

  await tapPlace('Plague Doctor', 'Rank 4')
  await tapPlace('Bounty Hunter', 'Rank 3')
  await tapPlace('Occultist',     'Rank 2')
  await tapPlace('Hellion',       'Rank 1')
  await sleep(600)
})

// ── 3. Hero detail panel ─────────────────────────────────────────────────────
await shot('03-hero-detail.png', async () => {
  // Click a filled rank slot to open detail
  const slots = await page.$$('.grid.grid-cols-4 .slot-filled')
  if (slots.length) await slots[0].click()
})

// ── 4. Dungeon filter active ─────────────────────────────────────────────────
await shot('04-dungeon-filter.png', async () => {
  // Open dungeon filter dropdown
  const btns = await page.$$('button')
  for (const b of btns) {
    const txt = await b.evaluate(el => el.textContent.trim())
    if (txt.includes('Dungeon Filter')) { await b.click(); await sleep(400); break }
  }
  // Select Cove
  const selects = await page.$$('select')
  if (selects.length) {
    await selects[0].select('cove')
    await sleep(600)
  }
})

// ── 5. Provision Calculator – Ruins medium ──────────────────────────────────
await shot('05-provisions-ruins.png', async () => {
  await page.goto(BASE, { waitUntil: 'networkidle0' })
  const tabs = await page.$$('nav button')
  for (const t of tabs) {
    const txt = await t.evaluate(el => el.textContent.trim())
    if (txt === 'Provisions') { await t.click(); break }
  }
  await sleep(800)
})

// ── 6. Provision Calculator – Cove long ─────────────────────────────────────
await shot('06-provisions-cove-long.png', async () => {
  const btns = await page.$$('.dd-btn')
  for (const b of btns) {
    const txt = await b.evaluate(el => el.textContent.trim())
    if (txt === 'Cove') { await b.click(); await sleep(200) }
    if (txt === 'Long') { await b.click(); await sleep(200) }
  }
  await sleep(700)
})

// ── 7. Curio Cheat Sheet – all ───────────────────────────────────────────────
await shot('07-curios.png', async () => {
  const tabs = await page.$$('nav button')
  for (const t of tabs) {
    const txt = await t.evaluate(el => el.textContent.trim())
    if (txt === 'Curio Cheat Sheet') { await t.click(); break }
  }
  await sleep(1000)
})

// ── 8. Curios – filtered Warrens ────────────────────────────────────────────
await shot('08-curios-warrens.png', async () => {
  const btns = await page.$$('.dd-btn')
  for (const b of btns) {
    const txt = await b.evaluate(el => el.textContent.trim())
    if (txt === 'Warrens') { await b.click(); break }
  }
  await sleep(500)
})

// ── 9. Mobile – Team Builder ─────────────────────────────────────────────────
await page.setViewport({ width: 390, height: 844, deviceScaleFactor: 2 })
await shot('09-mobile-team-builder.png', async () => {
  await page.goto(BASE, { waitUntil: 'networkidle0' })
})

// ── 10. Mobile – Curios ──────────────────────────────────────────────────────
await shot('10-mobile-curios.png', async () => {
  const tabs = await page.$$('nav button')
  for (const t of tabs) {
    const txt = await t.evaluate(el => el.textContent.trim())
    if (txt === 'Curio Cheat Sheet') { await t.click(); break }
  }
  await sleep(800)
})

await browser.close()
console.log('\nAll screenshots saved to docs/screenshots/')
