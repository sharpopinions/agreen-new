// ── PAGE COMPONENTS — v3 · Figma-faithful ────────────────────────

// ── MAIN PAGE ─────────────────────────────────────────────────────
function MainPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [tab, setTab] = useState('popular');
  const [slide, setSlide] = useState(0);

  return (
    <div style={{ background: T.bg }}>

      {/* ── 1. HERO: sidebar + banner + partnership ── */}
      <section style={{ padding: '24px 60px', display: 'grid', gridTemplateColumns: '280px 1fr 280px', gap: 16 }}>

        {/* Left: category sidebar */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden' }}>
          <div style={{ padding: '12px 16px', borderBottom: `1px solid ${T.border}`, fontSize: 12, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.06em' }}>Категорії</div>
          {CATEGORIES.map((cat, i) => (
            <div key={cat.id} onClick={() => setPage('catalog')}
              style={{ padding: '9px 16px 9px 14px', fontSize: 13, cursor: 'pointer',
                borderBottom: i < CATEGORIES.length - 1 ? `1px solid ${T.border}` : 'none',
                display: 'flex', justifyContent: 'space-between', alignItems: 'center', lineHeight: '1.43',
                transition: 'all 0.12s', color: T.text2 }}
              onMouseEnter={e => { e.currentTarget.style.background = T.muted; e.currentTarget.style.color = T.text; }}
              onMouseLeave={e => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = T.text2; }}>
              <span>{cat.name}</span>
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"><path d="m9 18 6-6-6-6"/></svg>
            </div>
          ))}
          <div onClick={() => setPage('catalog')}
            style={{ padding: '10px 16px', fontSize: 12, fontWeight: 500, color: T.text2, cursor: 'pointer', textAlign: 'center', borderTop: `1px solid ${T.border}`, transition: 'color 0.12s' }}
            onMouseEnter={e => e.target.style.color = T.text} onMouseLeave={e => e.target.style.color = T.text2}>
            Весь каталог →
          </div>
        </div>

        {/* Center: hero banner */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, position: 'relative', overflow: 'hidden', minHeight: 440 }}>
          <Img h={440} label="головний банер"/>
          <div style={{ position: 'absolute', bottom: 0, left: 0, right: 0, padding: '36px 36px 32px',
            background: T.bg === '#09090b'
              ? 'linear-gradient(to top, rgba(9,9,11,0.96) 0%, rgba(9,9,11,0.5) 60%, transparent 100%)'
              : 'linear-gradient(to top, rgba(255,255,255,0.97) 0%, rgba(255,255,255,0.65) 60%, transparent 100%)' }}>
            <div style={{ fontSize: 11, color: T.text3, letterSpacing: '0.1em', textTransform: 'uppercase', marginBottom: 10 }}>Промислові товари</div>
            <h1 style={{ fontFamily: 'Geist, Inter, sans-serif', fontSize: 28, fontWeight: 700, color: T.text, lineHeight: 1.2, marginBottom: 12, letterSpacing: '-0.025em' }}>
              Надаємо широкий<br/>асортимент продукції
            </h1>
            <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.65, marginBottom: 24, maxWidth: 440 }}>
              Для комплексного обслуговування підприємств малярно-кузовного ремонту, промислових та виробничих підприємств.
            </p>
            <div style={{ display: 'flex', gap: 10 }}>
              <Btn onClick={() => setPage('catalog')}>Замовити</Btn>
              <Btn variant="secondary" onClick={() => setPage('catalog')}>До каталогу</Btn>
            </div>
          </div>
          {/* Slide dots */}
          <div style={{ position: 'absolute', bottom: 16, right: 20, display: 'flex', gap: 6 }}>
            {[0,1,2,3].map(i => (
              <div key={i} onClick={() => setSlide(i)} style={{ width: slide === i ? 20 : 6, height: 6,
                borderRadius: T.rPill, background: slide === i ? T.invBg : T.border2,
                cursor: 'pointer', transition: 'all 0.3s' }}/>
            ))}
          </div>
          {/* Nav arrows */}
          <button style={{ position: 'absolute', left: 12, top: '50%', transform: 'translateY(-50%)',
            background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm,
            width: 32, height: 40, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer' }}
            onClick={() => setSlide(s => Math.max(0, s - 1))}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text2} strokeWidth="2"><path d="m15 18-6-6 6-6"/></svg>
          </button>
          <button style={{ position: 'absolute', right: 12, top: '50%', transform: 'translateY(-50%)',
            background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm,
            width: 32, height: 40, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer' }}
            onClick={() => setSlide(s => Math.min(3, s + 1))}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text2} strokeWidth="2"><path d="m9 18 6-6-6-6"/></svg>
          </button>
        </div>

        {/* Right: partnership */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden', display: 'flex', flexDirection: 'column' }}>
          <Img h={130} label="фото партнерства"/>
          <div style={{ padding: '18px 16px', flex: 1 }}>
            <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 8 }}>Партнерство</div>
            <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.65, marginBottom: 16 }}>
              Вигідні умови для дилерів та оптових покупців. Гнучка система знижок від 5%.
            </div>
            <Btn variant="outline" size="sm" full onClick={() => setPage('partners')}>Стати партнером →</Btn>
          </div>
        </div>
      </section>

      {/* ── 2. БРЕНДИ ── */}
      <section style={{ padding: '0 60px 40px' }}>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg,
          padding: '12px 20px', display: 'flex', alignItems: 'center', overflowX: 'auto', gap: 0 }}>
          <span style={{ fontSize: 12, fontWeight: 600, color: T.text3, textTransform: 'uppercase',
            letterSpacing: '0.08em', whiteSpace: 'nowrap', marginRight: 20, flexShrink: 0 }}>Бренди</span>
          <div style={{ width: 1, height: 20, background: T.border, marginRight: 12, flexShrink: 0 }}/>
          {BRANDS.map(b => (
            <div key={b} style={{ padding: '6px 14px', fontSize: 13, fontWeight: 500, color: T.text2,
              cursor: 'pointer', whiteSpace: 'nowrap', flexShrink: 0, borderRadius: T.rSm, transition: 'all 0.12s' }}
              onMouseEnter={e => { e.currentTarget.style.background = T.muted; e.currentTarget.style.color = T.text; }}
              onMouseLeave={e => { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = T.text2; }}>
              {b}
            </div>
          ))}
          <div style={{ marginLeft: 'auto', flexShrink: 0 }}>
            <Btn variant="ghost" size="sm" onClick={() => setPage('brands')}>Дивитись усі бренди →</Btn>
          </div>
        </div>
      </section>

      {/* ── 3. ПОПУЛЯРНІ КАТЕГОРІЇ ── */}
      <section style={{ padding: '0 60px 52px' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: 20 }}>
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Популярні категорії</h2>
            <p style={{ fontSize: 13, color: T.text2, marginTop: 4 }}>Понад 5 000 товарів у наявності</p>
          </div>
          <Btn variant="ghost" size="sm" onClick={() => setPage('catalog')}>Дивитись все →</Btn>
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14 }}>
          {CATEGORIES.slice(0, 6).map(cat => (
            <CatCard key={cat.id} cat={cat} onSelect={() => setPage('catalog')}/>
          ))}
        </div>
        <div style={{ textAlign: 'center', marginTop: 24 }}>
          <Btn variant="outline" onClick={() => setPage('catalog')}>Показати ще</Btn>
        </div>
      </section>

      {/* ── 4. КАТАЛОГ (tabs + products) ── */}
      <section style={{ padding: '0 60px 52px' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20 }}>
          <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Каталог</h2>
          <div style={{ display: 'flex', background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: 3, gap: 2 }}>
            {[['popular','Популярні'],['sale','Акційні'],['preorder','Передзамовлення']].map(([k,l]) => (
              <button key={k} onClick={() => setTab(k)}
                style={{ padding: '6px 16px', borderRadius: T.rSm,
                  background: tab === k ? T.surface : 'transparent',
                  border: tab === k ? `1px solid ${T.border}` : '1px solid transparent',
                  color: tab === k ? T.text : T.text2, fontSize: 13, fontWeight: tab === k ? 500 : 400,
                  cursor: 'pointer', fontFamily: 'Geist, Inter, sans-serif',
                  boxShadow: tab === k ? T.shadow : 'none', transition: 'all 0.12s' }}>
                {l}
              </button>
            ))}
          </div>
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16 }}>
          {PRODUCTS.slice(0, 8).map(p => (
            <ProductCard key={p.id} product={p} onView={() => setPage('product')} onAdd={addToCart}/>
          ))}
        </div>
        <div style={{ textAlign: 'center', marginTop: 28 }}>
          <Btn variant="outline" onClick={() => setPage('catalog')}>Показати ще</Btn>
        </div>
      </section>

      {/* ── 5. ПОСЛУГИ ── */}
      <section style={{ padding: '0 60px 52px' }}>
        <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 20, fontFamily: 'Geist, Inter, sans-serif' }}>Послуги</h2>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 14 }}>
          {[
            { n: '01', title: 'Навчальний центр',   desc: 'Навчання персоналу та сертифікація з роботи з продукцією' },
            { n: '02', title: 'Технічна підтримка', desc: 'Допомога у виборі та налаштуванні обладнання від спеціалістів' },
            { n: '03', title: 'Проектування',        desc: 'Розробка рішень для промислових та виробничих підприємств' },
            { n: '04', title: 'Ще одна послуга',     desc: 'Регулярне технічне обслуговування та ремонт обладнання' },
          ].map(s => (
            <div key={s.n} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg,
              padding: 24, cursor: 'pointer', transition: 'all 0.2s', boxShadow: T.shadow }}
              onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
              onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
              <div style={{ fontSize: 12, fontWeight: 600, color: T.text3, marginBottom: 14, letterSpacing: '0.04em' }}>{s.n}</div>
              <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 8, lineHeight: '1.43' }}>{s.title}</div>
              <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.65, marginBottom: 16 }}>{s.desc}</div>
              <span style={{ fontSize: 13, color: T.text2, fontWeight: 500 }}>Детальніше →</span>
            </div>
          ))}
        </div>
        <div style={{ textAlign: 'center', marginTop: 24 }}>
          <Btn variant="outline" onClick={() => setPage('services')}>Дивитись всі послуги</Btn>
        </div>
      </section>

      {/* ── 6. ПАРТНЕРСТВО ── */}
      <section style={{ padding: '0 60px 52px' }}>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '40px 48px',
          display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 32 }}>
          {[
            { title: 'Пропозиції дилерам',               desc: 'Вигідні умови та бонуси для авторизованих дилерів' },
            { title: 'Пропозиції оптовим покупцям',      desc: 'Знижки від обсягу та пріоритетна доставка' },
            { title: 'Пропозиції партнерам по установці', desc: 'Технічна підтримка та навчання спеціалістів' },
          ].map((p, i) => (
            <div key={i}>
              <div style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 10, lineHeight: 1.35 }}>{p.title}</div>
              <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.7, marginBottom: 16 }}>{p.desc}</div>
              <span style={{ fontSize: 13, color: T.text2, fontWeight: 500, cursor: 'pointer' }}>Детальніше →</span>
            </div>
          ))}
        </div>
        {/* Вигідні умови banner */}
        <div style={{ marginTop: 14, background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rLg,
          padding: '28px 48px', display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40, alignItems: 'center' }}>
          <div>
            <div style={{ fontSize: 18, fontWeight: 700, color: T.text, marginBottom: 12, letterSpacing: '-0.02em' }}>Вигідні умови для дилерів</div>
            <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.7, marginBottom: 16 }}>
              Перелік вигідних умов:<br/>
              — Знижки до 25% від роздрібної ціни<br/>
              — Ексклюзивні пропозиції<br/>
              — Пріоритетна доставка
            </div>
            <Btn onClick={() => setPage('dashboard')}>Детальніше →</Btn>
          </div>
          <Img h={160} label="банер партнерства"/>
        </div>
      </section>

      {/* ── 7. ПРО КОМПАНІЮ (stats + text + image) ── */}
      <section style={{ padding: '0 60px 52px' }}>
        <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Про компанію</h2>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40 }}>
          {/* Stats + text */}
          <div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 0,
              background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg,
              padding: '28px 0', marginBottom: 24 }}>
              {[['50+','Тисяч товарів'],['80+','Брендів'],['1100','Артикулів']].map(([n,l],i) => (
                <div key={n} style={{ textAlign: 'center', borderRight: i < 2 ? `1px solid ${T.border}` : 'none', padding: '0 20px' }}>
                  <div style={{ fontSize: 40, fontWeight: 800, color: T.text, letterSpacing: '-0.04em', lineHeight: 1, fontFamily: 'Geist, Inter, sans-serif' }}>{n}</div>
                  <div style={{ fontSize: 12, color: T.text3, marginTop: 6 }}>{l}</div>
                </div>
              ))}
            </div>
            <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.8 }}>
              Текст про компанію. Надаємо широкий асортимент продукції для комплексного обслуговування підприємств малярно-кузовного ремонту, промислових та виробничих підприємств, а також підприємств, що використовують гігієнічну продукцію.
            </div>
            <div style={{ marginTop: 20 }}>
              <Btn variant="outline" onClick={() => setPage('about')}>Детальніше</Btn>
            </div>
          </div>
          {/* Image */}
          <div style={{ borderRadius: T.rLg, overflow: 'hidden' }}>
            <Img h={280} label="фото компанії"/>
          </div>
        </div>
      </section>

      {/* ── 8. БЛОГ ТА НОВИНИ ── */}
      <section style={{ padding: '0 60px 52px' }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: 20 }}>
          <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Блог та новини</h2>
          <Btn variant="ghost" size="sm">Дивитись всі новини →</Btn>
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 14 }}>
          {['Новини ринку', 'Нова стаття', 'ЗМІ про нас', 'Нова стаття'].map((title, i) => (
            <div key={i} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg,
              overflow: 'hidden', cursor: 'pointer', transition: 'all 0.2s', boxShadow: T.shadow }}
              onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
              onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
              <Img h={140} label="фото новини"/>
              <div style={{ padding: '14px 16px 18px' }}>
                <div style={{ fontSize: 10, color: T.text3, marginBottom: 6, letterSpacing: '0.1em', textTransform: 'uppercase' }}>27 квітня 2026</div>
                <div style={{ fontSize: 13, fontWeight: 500, color: T.text, lineHeight: 1.5, marginBottom: 10 }}>{title}</div>
                <span style={{ fontSize: 12, color: T.text2, fontWeight: 500 }}>Читати →</span>
              </div>
            </div>
          ))}
        </div>
      </section>

    </div>
  );
}

// ── CATALOG PAGE ──────────────────────────────────────────────────
function CatalogPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [view, setView]     = useState('grid');
  const [search, setSearch] = useState('');
  const [selCat, setSelCat] = useState(null);
  const [priceFrom, setPriceFrom] = useState('');
  const [priceTo, setPriceTo]     = useState('');
  const [sort, setSort]           = useState('popular');
  // filter accordion
  const [openSec, setOpenSec] = useState(['cat','price','brand']);
  const toggle = k => setOpenSec(p => p.includes(k) ? p.filter(x => x !== k) : [...p, k]);

  const filtered = PRODUCTS.filter(p => {
    if (selCat && p.catId !== selCat) return false;
    if (search && !p.name.toLowerCase().includes(search.toLowerCase())) return false;
    if (priceFrom && p.price < +priceFrom) return false;
    if (priceTo   && p.price > +priceTo)   return false;
    return true;
  });

  const sorted = [...filtered].sort((a, b) => {
    if (sort === 'asc')    return a.price - b.price;
    if (sort === 'desc')   return b.price - a.price;
    if (sort === 'rating') return b.rating - a.rating;
    return 0;
  });

  return (
    <div style={{ background: T.bg, padding: '24px 60px' }}>
      <Crumbs items={['Каталог']} setPage={setPage}/>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: 6 }}>
        <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>
          {selCat ? CATEGORIES.find(c => c.id === selCat)?.name : 'Каталог'}
        </h1>
      </div>
      <p style={{ fontSize: 14, color: T.text2, marginBottom: 20, lineHeight: 1.6 }}>
        Оберіть категорію або скористайтесь пошуком. Для партнерів — гнучка система знижок!
      </p>

      {/* Search */}
      <div style={{ position: 'relative', marginBottom: 24 }}>
        <svg style={{ position: 'absolute', left: 12, top: '50%', transform: 'translateY(-50%)', color: T.text3, pointerEvents: 'none' }}
          width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input value={search} onChange={e => setSearch(e.target.value)}
          placeholder="Введіть назву товару або категорії..."
          style={{ width: '100%', background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm,
            padding: '10px 16px 10px 40px', color: T.text, fontSize: 14, outline: 'none',
            fontFamily: 'Geist, Inter, sans-serif', transition: 'border-color 0.15s' }}
          onFocus={e => e.target.style.borderColor = T.border2}
          onBlur={e => e.target.style.borderColor = T.border}/>
      </div>

      {/* Category grid when no filter */}
      {!selCat && !search && (
        <div style={{ marginBottom: 40 }}>
          <div style={{ fontSize: 13, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 14 }}>Всі категорії</div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12 }}>
            {CATEGORIES.map(cat => <CatCard key={cat.id} cat={cat} onSelect={c => setSelCat(c.id)}/>)}
          </div>
          <div style={{ height: 1, background: T.border, margin: '40px 0 0' }}/>
        </div>
      )}

      <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20 }}>

        {/* ── FILTER SIDEBAR (matches Figma: 300px, gray bg, sections) ── */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden', height: 'fit-content' }}>

          {/* Header */}
          <div style={{ padding: '14px 18px', borderBottom: `1px solid ${T.border}`,
            display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <span style={{ fontSize: 14, fontWeight: 600, color: T.text }}>Фільтри</span>
            {(selCat || priceFrom || priceTo) && (
              <span onClick={() => { setSelCat(null); setPriceFrom(''); setPriceTo(''); }}
                style={{ fontSize: 12, color: T.text2, cursor: 'pointer', textDecoration: 'underline', textUnderlineOffset: 2 }}>
                Очистити всі
              </span>
            )}
          </div>

          {/* Ціна */}
          <div style={{ borderBottom: `1px solid ${T.border}` }}>
            <div onClick={() => toggle('price')}
              style={{ padding: '12px 18px', display: 'flex', justifyContent: 'space-between', cursor: 'pointer',
                fontSize: 13, fontWeight: 600, color: T.text }}>
              Ціна
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                style={{ transform: openSec.includes('price') ? 'rotate(180deg)' : 'none', transition: 'transform 0.2s' }}>
                <path d="m6 9 6 6 6-6"/>
              </svg>
            </div>
            {openSec.includes('price') && (
              <div style={{ padding: '0 18px 16px' }}>
                <div style={{ display: 'flex', gap: 8, marginBottom: 12 }}>
                  <input value={priceFrom} onChange={e => setPriceFrom(e.target.value)} placeholder="від"
                    style={{ flex: 1, background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rSm,
                      padding: '7px 10px', color: T.text, fontSize: 13, outline: 'none', minWidth: 0,
                      fontFamily: 'Geist, Inter, sans-serif' }}/>
                  <span style={{ display: 'flex', alignItems: 'center', color: T.text3, fontSize: 13 }}>—</span>
                  <input value={priceTo} onChange={e => setPriceTo(e.target.value)} placeholder="до"
                    style={{ flex: 1, background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rSm,
                      padding: '7px 10px', color: T.text, fontSize: 13, outline: 'none', minWidth: 0,
                      fontFamily: 'Geist, Inter, sans-serif' }}/>
                </div>
                {/* Price range track */}
                <div style={{ height: 3, background: T.border, borderRadius: T.rPill, position: 'relative' }}>
                  <div style={{ position: 'absolute', left: '5%', right: '20%', height: '100%', background: T.invBg, borderRadius: T.rPill }}/>
                </div>
                <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 6, fontSize: 11, color: T.text3 }}>
                  <span>0 ₴</span><span>5 000 ₴</span>
                </div>
              </div>
            )}
          </div>

          {/* Категорія */}
          <div style={{ borderBottom: `1px solid ${T.border}` }}>
            <div onClick={() => toggle('cat')}
              style={{ padding: '12px 18px', display: 'flex', justifyContent: 'space-between', cursor: 'pointer',
                fontSize: 13, fontWeight: 600, color: T.text }}>
              Категорія
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                style={{ transform: openSec.includes('cat') ? 'rotate(180deg)' : 'none', transition: 'transform 0.2s' }}>
                <path d="m6 9 6 6 6-6"/>
              </svg>
            </div>
            {openSec.includes('cat') && (
              <div style={{ padding: '0 18px 14px' }}>
                {CATEGORIES.map(cat => (
                  <div key={cat.id} onClick={() => setSelCat(selCat === cat.id ? null : cat.id)}
                    style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '5px 0', cursor: 'pointer',
                      fontSize: 13, color: selCat === cat.id ? T.text : T.text2, lineHeight: '1.43' }}>
                    <div style={{ width: 16, height: 16, border: `1.5px solid ${selCat === cat.id ? T.invBg : T.border2}`,
                      borderRadius: 4, background: selCat === cat.id ? T.invBg : 'transparent',
                      flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center', transition: 'all 0.15s' }}>
                      {selCat === cat.id && (
                        <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                          <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                        </svg>
                      )}
                    </div>
                    <span>{cat.name}</span>
                    <span style={{ marginLeft: 'auto', fontSize: 11, color: T.text3 }}>{cat.count}</span>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Бренди */}
          <div style={{ borderBottom: `1px solid ${T.border}` }}>
            <div onClick={() => toggle('brand')}
              style={{ padding: '12px 18px', display: 'flex', justifyContent: 'space-between', cursor: 'pointer',
                fontSize: 13, fontWeight: 600, color: T.text }}>
              Бренди
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                style={{ transform: openSec.includes('brand') ? 'rotate(180deg)' : 'none', transition: 'transform 0.2s' }}>
                <path d="m6 9 6 6 6-6"/>
              </svg>
            </div>
            {openSec.includes('brand') && (
              <div style={{ padding: '0 18px 14px' }}>
                {BRANDS.slice(0, 6).map(b => (
                  <div key={b} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '5px 0',
                    cursor: 'pointer', fontSize: 13, color: T.text2 }}>
                    <div style={{ width: 16, height: 16, border: `1.5px solid ${T.border2}`, borderRadius: 4, flexShrink: 0 }}/>
                    <span>{b}</span>
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Наявність */}
          <div>
            <div onClick={() => toggle('stock')}
              style={{ padding: '12px 18px', display: 'flex', justifyContent: 'space-between', cursor: 'pointer',
                fontSize: 13, fontWeight: 600, color: T.text }}>
              Наявність
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                style={{ transform: openSec.includes('stock') ? 'rotate(180deg)' : 'none', transition: 'transform 0.2s' }}>
                <path d="m6 9 6 6 6-6"/>
              </svg>
            </div>
            {openSec.includes('stock') && (
              <div style={{ padding: '0 18px 14px' }}>
                {[['Є в наявності','156'],['Під замовлення','48'],['Акційні','23']].map(([l, c]) => (
                  <div key={l} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '5px 0',
                    cursor: 'pointer', fontSize: 13, color: T.text2 }}>
                    <div style={{ width: 16, height: 16, border: `1.5px solid ${T.border2}`, borderRadius: 4, flexShrink: 0 }}/>
                    <span>{l}</span>
                    <span style={{ marginLeft: 'auto', fontSize: 11, color: T.text3 }}>({c})</span>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* ── PRODUCTS GRID ── */}
        <div>
          {/* Toolbar */}
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
            <div style={{ fontSize: 14, color: T.text2 }}>
              Знайдено: <strong style={{ color: T.text }}>{sorted.length}</strong> товарів
            </div>
            <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
              <select value={sort} onChange={e => setSort(e.target.value)}
                style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm,
                  padding: '7px 10px', color: T.text2, fontSize: 13, outline: 'none',
                  fontFamily: 'Geist, Inter, sans-serif', cursor: 'pointer' }}>
                <option value="popular">Популярні</option>
                <option value="asc">Ціна ↑</option>
                <option value="desc">Ціна ↓</option>
                <option value="rating">Рейтинг</option>
              </select>
              <div style={{ display: 'flex', gap: 2, background: T.muted, borderRadius: T.rSm, border: `1px solid ${T.border}`, padding: 3 }}>
                {['grid','list'].map(v => (
                  <button key={v} onClick={() => setView(v)}
                    style={{ padding: '5px 8px', borderRadius: T.rSm,
                      background: view === v ? T.surface : 'transparent',
                      border: view === v ? `1px solid ${T.border}` : '1px solid transparent',
                      cursor: 'pointer', color: view === v ? T.text : T.text2,
                      boxShadow: view === v ? T.shadow : 'none', transition: 'all 0.12s' }}>
                    {v === 'grid'
                      ? <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                      : <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="2" width="14" height="2.5" rx="1"/><rect x="1" y="6.75" width="14" height="2.5" rx="1"/><rect x="1" y="11.5" width="14" height="2.5" rx="1"/></svg>}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {sorted.length === 0 ? (
            <div style={{ textAlign: 'center', padding: '60px 20px' }}>
              <div style={{ fontSize: 14, fontWeight: 500, color: T.text, marginBottom: 6 }}>Нічого не знайдено</div>
              <div style={{ fontSize: 13, color: T.text2, marginBottom: 16 }}>Спробуйте змінити фільтри</div>
              <Btn variant="outline" size="sm" onClick={() => { setSelCat(null); setSearch(''); }}>Скинути фільтри</Btn>
            </div>
          ) : view === 'grid' ? (
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16 }}>
              {sorted.map(p => <ProductCard key={p.id} product={p} onView={() => setPage('product')} onAdd={addToCart}/>)}
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
              {sorted.map(p => (
                <div key={p.id} onClick={() => setPage('product')}
                  style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg,
                    padding: 16, display: 'flex', gap: 16, cursor: 'pointer', transition: 'all 0.15s', boxShadow: T.shadow }}
                  onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
                  onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
                  <div style={{ flexShrink: 0, borderRadius: T.rSm, overflow: 'hidden', width: 90 }}><Img h={90} label=""/></div>
                  <div style={{ flex: 1 }}>
                    <div style={{ fontSize: 12, color: T.text3, marginBottom: 4 }}>Арт: {p.sku}</div>
                    <div style={{ fontSize: 14, color: T.text, lineHeight: 1.5, marginBottom: 6 }}>{p.name}</div>
                    <Stars rating={p.rating}/>
                  </div>
                  <div style={{ display: 'flex', flexDirection: 'column', justifyContent: 'space-between', alignItems: 'flex-end', minWidth: 140 }}>
                    <div style={{ fontSize: 17, fontWeight: 700, color: T.text, letterSpacing: '-0.025em' }}>{fmt(p.price)}</div>
                    <Btn size="sm" onClick={e => { e.stopPropagation(); addToCart(p); }}>До кошика</Btn>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

// ── PRODUCT PAGE ──────────────────────────────────────────────────
function ProductPage({ setPage, addToCart, mode = 'default' }) {
  const { T } = useContext(ThemeCtx);
  const p = PRODUCTS[0];
  const [qty, setQty] = useState(1);
  const [tab, setTab] = useState('desc');
  const [img, setImg] = useState(0);

  const tabs = [['desc','Опис'],['specs','Характеристики'],['docs','Інструкції'],['video','Відеоогляд'],['reviews','Відгуки (12)'],['related','Супутні товари']];

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={
        mode==='brand'
          ? ['Бренди','Продукція TORK','Дозуюче обладнання','Диспенсери']
          : ['Каталог','Дозуюче обладнання','Диспенсери']
      } setPage={setPage}/>

      {/* Mode switcher (демо) */}
      <div style={{ display:'flex', gap:6, marginBottom:20, padding:6,
        background: T.bgAlt, border:`1px solid ${T.border}`,
        borderRadius: T.rLg, width:'fit-content' }}>
        {[
          ['default','Звичайна'],
          ['business','Бізнес-клієнт'],
          ['to-order','Під замовлення'],
          ['modified','Модифікована'],
          ['brand','З бренду'],
        ].map(([k, l]) => (
          <button key={k} onClick={() => setPage(k==='default' ? 'product' : `product-${k==='to-order'?'order':k}`)}
            style={{ padding:'6px 12px', borderRadius: T.rSm,
              background: mode===k ? T.surface : 'transparent',
              border: mode===k ? `1px solid ${T.border}` : '1px solid transparent',
              color: mode===k ? T.text : T.text2,
              fontSize: 12, fontWeight: mode===k ? 600 : 400,
              cursor: 'pointer', fontFamily: 'Geist, Inter, sans-serif',
              boxShadow: mode===k ? T.shadow : 'none', transition: 'all 0.12s' }}>
            {l}
          </button>
        ))}
      </div>
      <div style={{ display: 'grid', gridTemplateColumns: '400px 1fr', gap: 40, marginBottom: 48 }}>
        {/* Gallery */}
        <div>
          <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden', marginBottom: 10 }}>
            <Img h={340} label={`фото товару ${img + 1}`}/>
          </div>
          <div style={{ display: 'flex', gap: 8 }}>
            {[0,1,2,3].map(i => (
              <div key={i} onClick={() => setImg(i)}
                style={{ flex: 1, border: `1.5px solid ${img === i ? T.invBg : T.border}`, borderRadius: T.rSm, overflow: 'hidden', cursor: 'pointer', transition: 'border-color 0.15s' }}>
                <Img h={70} label={`${i + 1}`}/>
              </div>
            ))}
          </div>
        </div>
        {/* Info */}
        <div>
          <div style={{ display: 'flex', gap: 6, marginBottom: 12 }}>
            <Badge label="Хіт"/>
            {mode === 'to-order'    && <Badge label="Новинка"/>}
            {mode === 'business'    && <Badge label="Хіт"/>}
            {mode === 'modified'    && <Badge label="Новинка"/>}
          </div>
          <h1 style={{ fontSize: 22, fontWeight: 700, color: T.text, lineHeight: 1.35, letterSpacing: '-0.025em', marginBottom: 14, fontFamily: 'Geist, Inter, sans-serif' }}>
            Сенсорний диспенсер для паперових рушників Kimberly-Clark 9960
          </h1>
          <div style={{ display: 'flex', alignItems: 'center', gap: 10, marginBottom: 10 }}>
            <Stars rating={4.2}/>
            <span style={{ fontSize: 13, color: T.text2 }}>4.2/5 (12 відгуків)</span>
            <span style={{ color: T.border2 }}>·</span>
            <span onClick={() => setTab('reviews')} style={{ fontSize: 13, color: T.text2, cursor: 'pointer', textDecoration: 'underline', textUnderlineOffset: 3 }}>Залишити відгук</span>
          </div>
          <div style={{ fontSize: 13, color: T.text3, marginBottom: 20,
            display:'flex', alignItems:'center', gap:8 }}>
            <span>Артикул: SE50281</span>
            {mode === 'modified' && (
              <>
                <span style={{ color:T.text3 }}>·</span>
                <span style={{ padding:'2px 8px', background: T.bg==='#09090b'?'#451a03':'#fffbeb',
                  border:`1px solid ${T.bg==='#09090b'?'#7c2d12':'#fde68a'}`,
                  borderRadius: T.rPill, fontSize: 11, fontWeight:500,
                  color: T.bg==='#09090b'?'#fbbf24':'#b45309' }}>
                  ⚙ Модифікований
                </span>
              </>
            )}
          </div>
          <div style={{ border: `1px solid #fde68a`, borderRadius: T.rSm, padding: '10px 14px', fontSize: 13,
            color: T.bg === '#09090b' ? '#fbbf24' : '#b45309',
            background: T.bg === '#09090b' ? '#451a03' : '#fffbeb', marginBottom: 20, display: 'flex', alignItems: 'center', gap: 8 }}>
            <span>⚠</span><span>Цей диспенсер працює лише з "Активатором X"</span>
          </div>
          <div style={{ marginBottom: 20 }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 10 }}>Розмір</div>
            <div style={{ display: 'flex', gap: 8 }}>
              {['30×45','40×65'].map((sz, i) => (
                <div key={sz} style={{ padding: '7px 18px', border: `1.5px solid ${i === 0 ? T.invBg : T.border}`,
                  borderRadius: T.rSm, fontSize: 13, fontWeight: i === 0 ? 600 : 400,
                  color: i === 0 ? T.invText : T.text2, background: i === 0 ? T.invBg : 'transparent', cursor: 'pointer' }}>{sz}</div>
              ))}
            </div>
          </div>
          <div style={{ marginBottom: 24 }}>
            {mode === 'business' ? (
              <div>
                <div style={{ display:'flex', alignItems:'baseline', gap:12, marginBottom:6 }}>
                  <div style={{ fontSize: 32, fontWeight: 800, color: T.text, letterSpacing: '-0.04em', lineHeight: 1, fontFamily: 'Geist, Inter, sans-serif' }}>15 299 ₴</div>
                  <span style={{ padding:'3px 10px', background: T.bg==='#09090b'?'#172554':'#eff6ff',
                    border: `1px solid ${T.bg==='#09090b'?'#1e3a8a':'#bfdbfe'}`,
                    borderRadius: T.rPill, fontSize: 11, fontWeight: 600,
                    color: T.bg==='#09090b'?'#60a5fa':'#2563eb' }}>
                    Партнерська ціна
                  </span>
                </div>
                <div style={{ fontSize: 14, color: T.text3, textDecoration:'line-through' }}>
                  Роздрібна: 16 999 ₴
                </div>
                <div style={{ fontSize: 13, color: '#16a34a', marginTop: 6, fontWeight: 500 }}>✓ В наявності: 150 шт</div>
              </div>
            ) : mode === 'to-order' ? (
              <div>
                <div style={{ fontSize: 32, fontWeight: 800, color: T.text, letterSpacing: '-0.04em', lineHeight: 1, fontFamily: 'Geist, Inter, sans-serif' }}>1 099 ₴</div>
                <div style={{ fontSize: 13, color: T.warn, marginTop: 6, fontWeight: 500,
                  display:'flex', alignItems:'center', gap:6 }}>
                  <span>⌛</span> Під замовлення · 7–14 днів
                </div>
              </div>
            ) : (
              <div>
                <div style={{ fontSize: 32, fontWeight: 800, color: T.text, letterSpacing: '-0.04em', lineHeight: 1, fontFamily: 'Geist, Inter, sans-serif' }}>1 099 ₴</div>
                <div style={{ fontSize: 13, color: '#16a34a', marginTop: 6, fontWeight: 500 }}>✓ В наявності: 150 шт</div>
              </div>
            )}
          </div>
          <div style={{ display: 'flex', gap: 10, marginBottom: 10 }}>
            <QtyCtrl qty={qty} setQty={setQty}/>
            {mode === 'to-order' ? (
              <Btn size="lg" full onClick={() => setPage('contacts')}>Замовити</Btn>
            ) : (
              <Btn size="lg" full onClick={() => { addToCart(p); setPage('cart'); }}>До кошика</Btn>
            )}
          </div>
          {mode === 'to-order' ? (
            <Btn variant="outline" full>🔔 Повідомити про надходження</Btn>
          ) : mode === 'business' ? (
            <Btn variant="outline" full>📋 Запит на гуртову ціну</Btn>
          ) : (
            <Btn variant="outline" full>Швидке замовлення</Btn>
          )}
          <div style={{ marginTop: 24, background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '16px 20px' }}>
            <div style={{ fontSize: 12, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 12 }}>Характеристики</div>
            {[['Серія','Image Design'],['Колір','Сталевий'],['Матеріал','Метал/пластик'],['Розмір','373×345×204 мм'],['Система','H1']].map(([k,v]) => (
              <div key={k} style={{ display: 'flex', justifyContent: 'space-between', padding: '7px 0', borderBottom: `1px solid ${T.border}`, fontSize: 13 }}>
                <span style={{ color: T.text2 }}>{k}</span><span style={{ color: T.text, fontWeight: 500 }}>{v}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
      {/* Tabs */}
      <div style={{ borderBottom: `1px solid ${T.border}`, marginBottom: 28, display: 'flex', gap: 2 }}>
        {tabs.map(([k,l]) => (
          <button key={k} onClick={() => setTab(k)}
            style={{ padding: '10px 18px', background: 'none', border: 'none', cursor: 'pointer', fontSize: 14,
              fontFamily: 'Geist, Inter, sans-serif', fontWeight: tab === k ? 600 : 400,
              color: tab === k ? T.text : T.text2, borderBottom: `2px solid ${tab === k ? T.text : 'transparent'}`,
              marginBottom: -1, transition: 'all 0.15s' }}>
            {l}
          </button>
        ))}
      </div>
      <div style={{ marginBottom: 60 }}>
        {tab === 'desc' && (
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40 }}>
            <div>
              <h3 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 14 }}>Опис товару</h3>
              <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.8 }}>
                Сенсорний диспенсер для паперових рушників Kimberly-Clark 9960 — преміальне рішення для громадських туалетів, офісів та виробничих приміщень. Автоматична подача рушника за допомогою інфрачервоного датчика мінімізує контакт з поверхнею.
              </p>
            </div>
            <div style={{ borderRadius: T.rLg, overflow: 'hidden' }}><Img h={260} label="фото опис"/></div>
          </div>
        )}
        {tab === 'specs' && (
          <div style={{ maxWidth: 560 }}>
            <h3 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 18 }}>Характеристики</h3>
            {[['Серія','Image Design'],['Артикул','SE50281'],['Колір','Сталевий'],['Матеріал','Метал/ABS-пластик'],['Розмір','373×345×204 мм'],['Монтаж','Настінний'],['Ємність','до 6 рулонів по 150 м'],['Живлення','4×AA'],['Гарантія','2 роки']].map(([k,v]) => (
              <div key={k} style={{ display: 'flex', padding: '10px 0', borderBottom: `1px solid ${T.border}`, fontSize: 14 }}>
                <span style={{ color: T.text2, width: 220, flexShrink: 0 }}>{k}</span>
                <span style={{ color: T.text, fontWeight: 500 }}>{v}</span>
              </div>
            ))}
          </div>
        )}
        {tab === 'reviews' && (
          <div style={{ display: 'grid', gridTemplateColumns: '200px 1fr', gap: 40 }}>
            <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 24, textAlign: 'center' }}>
              <div style={{ fontSize: 48, fontWeight: 800, color: T.text, letterSpacing: '-0.04em', fontFamily: 'Geist, Inter, sans-serif' }}>4.2</div>
              <div style={{ margin: '10px 0 6px' }}><Stars rating={4.2}/></div>
              <div style={{ fontSize: 12, color: T.text3 }}>12 відгуків</div>
            </div>
            <div>
              {[5,4,3,2,1].map(n => (
                <div key={n} style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 10 }}>
                  <span style={{ fontSize: 13, color: T.text2, width: 10 }}>{n}</span>
                  <div style={{ flex: 1, height: 6, background: T.muted, borderRadius: T.rPill }}>
                    <div style={{ width: n===5?'67%':n===4?'25%':n===3?'8%':'0%', height: '100%', background: T.invBg, borderRadius: T.rPill }}/>
                  </div>
                  <span style={{ fontSize: 12, color: T.text3, width: 30 }}>{n===5?'(8)':n===4?'(3)':n===3?'(1)':'(0)'}</span>
                </div>
              ))}
              <div style={{ marginTop: 20 }}><Btn variant="outline">Написати відгук</Btn></div>
            </div>
          </div>
        )}
        {tab === 'related' && (
          <div>
            <h3 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 20 }}>Супутні товари</h3>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16 }}>
              {PRODUCTS.slice(1, 5).map(p => <ProductCard key={p.id} product={p} onView={() => {}} onAdd={addToCart}/>)}
            </div>
          </div>
        )}
        {(tab === 'docs' || tab === 'video') && (
          <div style={{ borderRadius: T.rLg, overflow: 'hidden' }}>
            <Img h={280} label={tab === 'video' ? 'відеоогляд' : 'документи'}/>
          </div>
        )}
      </div>
    </div>
  );
}

// ── CART PAGE ─────────────────────────────────────────────────────
function CartPage({ setPage, cart, updateQty, removeItem }) {
  const { T } = useContext(ThemeCtx);
  const total = cart.reduce((s, i) => s + i.product.price * i.qty, 0);

  if (cart.length === 0) return (
    <div style={{ background: T.bg, minHeight: '55vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 16 }}>
      <div style={{ width: 64, height: 64, background: T.muted, borderRadius: T.rLg, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.5">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
          <line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
      </div>
      <div style={{ fontSize: 18, fontWeight: 600, color: T.text }}>Кошик порожній</div>
      <div style={{ fontSize: 14, color: T.text2 }}>Додайте товари з каталогу</div>
      <Btn onClick={() => setPage('catalog')}>До каталогу</Btn>
    </div>
  );

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Кошик']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Кошик</h1>
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 300px', gap: 20 }}>
        <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 130px 110px 40px', gap: 16, padding: '8px 16px', fontSize: 12, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.08em' }}>
            <span>Товар</span><span style={{ textAlign: 'center' }}>Кількість</span><span style={{ textAlign: 'right' }}>Сума</span><span/>
          </div>
          {cart.map(item => (
            <div key={item.product.id} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 16, display: 'grid', gridTemplateColumns: '1fr 130px 110px 40px', gap: 16, alignItems: 'center' }}>
              <div style={{ display: 'flex', gap: 14, alignItems: 'center' }}>
                <div style={{ flexShrink: 0, borderRadius: T.rSm, overflow: 'hidden', width: 80 }}><Img h={80} label=""/></div>
                <div>
                  <div style={{ fontSize: 12, color: T.text3, marginBottom: 4 }}>Арт: {item.product.sku}</div>
                  <div style={{ fontSize: 13, fontWeight: 500, color: T.text, lineHeight: 1.5 }}>{item.product.name}</div>
                  <div style={{ fontSize: 12, color: T.text2, marginTop: 4 }}>{fmt(item.product.price)} / шт</div>
                </div>
              </div>
              <div style={{ display: 'flex', justifyContent: 'center' }}>
                <QtyCtrl qty={item.qty} setQty={q => updateQty(item.product.id, q)}/>
              </div>
              <div style={{ textAlign: 'right', fontSize: 16, fontWeight: 700, color: T.text, letterSpacing: '-0.025em' }}>{fmt(item.product.price * item.qty)}</div>
              <div style={{ display: 'flex', justifyContent: 'flex-end' }}>
                <button onClick={() => removeItem(item.product.id)}
                  style={{ background: 'none', border: 'none', color: T.text3, cursor: 'pointer', padding: 6, borderRadius: T.rSm, transition: 'all 0.12s' }}
                  onMouseEnter={e => { e.currentTarget.style.background = T.muted; e.currentTarget.style.color = T.danger; }}
                  onMouseLeave={e => { e.currentTarget.style.background = 'none'; e.currentTarget.style.color = T.text3; }}>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
              </div>
            </div>
          ))}
        </div>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 24, height: 'fit-content', position: 'sticky', top: 80 }}>
          <div style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 20 }}>Ваше замовлення</div>
          <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 14, color: T.text2, marginBottom: 10 }}>
            <span>Товари ({cart.length}):</span><span style={{ color: T.text, fontWeight: 500 }}>{fmt(total)}</span>
          </div>
          <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 14, color: T.text2, marginBottom: 20, paddingBottom: 20, borderBottom: `1px solid ${T.border}` }}>
            <span>Доставка:</span><span style={{ color: T.text3 }}>розраховується</span>
          </div>
          <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 18, fontWeight: 700, color: T.text, marginBottom: 20, letterSpacing: '-0.025em' }}>
            <span>Разом:</span><span>{fmt(total)}</span>
          </div>
          <Btn full onClick={() => setPage('checkout')}>Оформити замовлення</Btn>
          <div style={{ marginTop: 10 }}><Btn variant="ghost" full onClick={() => setPage('catalog')}>Продовжити покупки</Btn></div>
        </div>
      </div>
    </div>
  );
}

// ── CHECKOUT PAGE ─────────────────────────────────────────────────
function CheckoutPage({ setPage, cart, clearCart }) {
  const { T } = useContext(ThemeCtx);
  const [step, setStep] = useState(1);
  const [form, setForm] = useState({ phone:'', email:'', name:'', company:'', comment:'', delivery:'nova', payment:'card', city:'', address:'' });
  const upd = (k, v) => setForm(f => ({ ...f, [k]: v }));
  const total = cart.reduce((s, i) => s + i.product.price * i.qty, 0);

  if (step === 4) return (
    <div style={{ background: T.bg, minHeight: '55vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', textAlign: 'center', padding: 60 }}>
      <div style={{ width: 64, height: 64, background: T.bg==='#09090b'?'#052e16':'#f0fdf4', border: `1px solid ${T.bg==='#09090b'?'#14532d':'#bbf7d0'}`, borderRadius: T.rLg, display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: 20 }}>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#16a34a" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, marginBottom: 8, letterSpacing: '-0.025em' }}>Замовлення оформлено!</h2>
      <p style={{ fontSize: 14, color: T.text2, marginBottom: 6 }}>Дякуємо! Ми зв'яжемось з вами найближчим часом.</p>
      <p style={{ fontSize: 13, color: T.text3, marginBottom: 28 }}>Номер замовлення: <span style={{ color: T.text, fontWeight: 600 }}>#A-2026-04220</span></p>
      <Btn onClick={() => { clearCart(); setPage('main'); }}>На головну</Btn>
    </div>
  );

  const steps = ['Контактні дані','Доставка','Оплата'];
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Кошик','Оформлення']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 32, fontFamily: 'Geist, Inter, sans-serif' }}>Оформлення замовлення</h1>
      <div style={{ display: 'flex', alignItems: 'center', marginBottom: 32, maxWidth: 480 }}>
        {steps.map((label, i) => (
          <React.Fragment key={label}>
            <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 6 }}>
              <div style={{ width: 32, height: 32, borderRadius: T.rPill,
                border: `1.5px solid ${step>i+1?'transparent':step===i+1?T.invBg:T.border}`,
                background: step>i+1?'#16a34a':step===i+1?T.invBg:'transparent',
                display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 13, fontWeight: 600,
                color: step>=i+1?(step>i+1?'#fff':T.invText):T.text3 }}>
                {step>i+1?<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>:i+1}
              </div>
              <div style={{ fontSize: 11, fontWeight: 500, color: step>=i+1?T.text:T.text3, whiteSpace: 'nowrap', textTransform: 'uppercase', letterSpacing: '0.06em' }}>{label}</div>
            </div>
            {i<2 && <div style={{ flex: 1, height: 1.5, background: step>i+1?'#16a34a':T.border, margin: '0 12px', marginBottom: 18, transition: 'background 0.3s' }}/>}
          </React.Fragment>
        ))}
      </div>
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 280px', gap: 20 }}>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 32 }}>
          {step===1 && (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 20 }}>
              <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text }}>Контактні дані</h2>
              <div style={{ background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '12px 16px', fontSize: 13, color: T.text2 }}>
                Постійний клієнт?{' '}<span style={{ color: T.text, fontWeight: 600, cursor: 'pointer', textDecoration: 'underline' }}>Авторизуйтесь</span>{' '}— ми заповнимо дані автоматично.
              </div>
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                <Field label="Ім'я" required placeholder="Іван Іваненко" value={form.name} onChange={e => upd('name',e.target.value)}/>
                <Field label="Телефон" required placeholder="+380 XX XXX XX XX" value={form.phone} onChange={e => upd('phone',e.target.value)}/>
                <Field label="Email" required placeholder="email@company.ua" type="email" value={form.email} onChange={e => upd('email',e.target.value)}/>
                <Field label="Компанія" placeholder="Назва компанії" value={form.company} onChange={e => upd('company',e.target.value)}/>
              </div>
              <div><Btn onClick={() => setStep(2)}>Далі — Доставка →</Btn></div>
            </div>
          )}
          {step===2 && (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text }}>Доставка</h2>
              {[['nova','Нова Пошта','1–3 дні · розраховується'],['ukr','Укрпошта','3–7 днів'],['courier','Кур\'єр по Києву','Наступний день · 150 ₴'],['pickup','Самовивіз','Київ, вул. Крайня 1']].map(([k,l,s]) => (
                <div key={k} onClick={() => upd('delivery',k)}
                  style={{ padding: '14px 16px', border: `1.5px solid ${form.delivery===k?T.invBg:T.border}`, borderRadius: T.rSm, cursor: 'pointer', background: form.delivery===k?T.muted:'transparent', display: 'flex', gap: 12, alignItems: 'center', transition: 'all 0.15s' }}>
                  <div style={{ width: 18, height: 18, border: `1.5px solid ${form.delivery===k?T.invBg:T.border2}`, borderRadius: T.rPill, flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    {form.delivery===k && <div style={{ width: 8, height: 8, borderRadius: '50%', background: T.invBg }}/>}
                  </div>
                  <div><div style={{ fontSize: 14, fontWeight: 500, color: T.text }}>{l}</div><div style={{ fontSize: 12, color: T.text2, marginTop: 2 }}>{s}</div></div>
                </div>
              ))}
              {form.delivery!=='pickup' && (
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14 }}>
                  <Field label="Місто" required placeholder="Київ" value={form.city} onChange={e => upd('city',e.target.value)}/>
                  <Field label="Відділення / адреса" placeholder="Відділення №5" value={form.address} onChange={e => upd('address',e.target.value)}/>
                </div>
              )}
              <div style={{ display: 'flex', gap: 10 }}><Btn variant="secondary" onClick={() => setStep(1)}>← Назад</Btn><Btn onClick={() => setStep(3)}>Далі — Оплата →</Btn></div>
            </div>
          )}
          {step===3 && (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text }}>Оплата</h2>
              {[['card','Картка онлайн','Visa, Mastercard, Apple Pay'],['invoice','Безготівковий розрахунок','Рахунок-фактура для юр. осіб'],['cod','Оплата при отриманні','Готівкою або карткою']].map(([k,l,s]) => (
                <div key={k} onClick={() => upd('payment',k)}
                  style={{ padding: '14px 16px', border: `1.5px solid ${form.payment===k?T.invBg:T.border}`, borderRadius: T.rSm, cursor: 'pointer', background: form.payment===k?T.muted:'transparent', display: 'flex', gap: 12, alignItems: 'center', transition: 'all 0.15s' }}>
                  <div style={{ width: 18, height: 18, border: `1.5px solid ${form.payment===k?T.invBg:T.border2}`, borderRadius: T.rPill, flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                    {form.payment===k && <div style={{ width: 8, height: 8, borderRadius: '50%', background: T.invBg }}/>}
                  </div>
                  <div><div style={{ fontSize: 14, fontWeight: 500, color: T.text }}>{l}</div><div style={{ fontSize: 12, color: T.text2, marginTop: 2 }}>{s}</div></div>
                </div>
              ))}
              <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                <label style={{ fontSize: 14, fontWeight: 500, color: T.text }}>Коментар</label>
                <textarea value={form.comment} onChange={e => upd('comment',e.target.value)} placeholder="Додаткові побажання..."
                  style={{ background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '10px 12px', color: T.text, fontSize: 14, outline: 'none', resize: 'vertical', minHeight: 80, fontFamily: 'Geist, Inter, sans-serif' }}/>
              </div>
              <p style={{ fontSize: 13, color: T.text3, lineHeight: 1.6 }}>Натискаючи «Підтвердити», ви погоджуєтесь з <span style={{ color: T.text2, textDecoration: 'underline', cursor: 'pointer' }}>умовами публічної оферти</span>.</p>
              <div style={{ display: 'flex', gap: 10 }}><Btn variant="secondary" onClick={() => setStep(2)}>← Назад</Btn><Btn onClick={() => { clearCart(); setStep(4); }}>Підтвердити замовлення</Btn></div>
            </div>
          )}
        </div>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 20, height: 'fit-content', position: 'sticky', top: 80 }}>
          <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 16 }}>Ваше замовлення</div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 10, marginBottom: 16, paddingBottom: 16, borderBottom: `1px solid ${T.border}` }}>
            {cart.map(item => (
              <div key={item.product.id} style={{ display: 'flex', justifyContent: 'space-between', gap: 8 }}>
                <div style={{ fontSize: 12, color: T.text2, lineHeight: 1.5, flex: 1 }}>{item.product.name} ×{item.qty}</div>
                <div style={{ fontSize: 12, color: T.text, fontWeight: 500, whiteSpace: 'nowrap' }}>{fmt(item.product.price*item.qty)}</div>
              </div>
            ))}
          </div>
          <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: 16, fontWeight: 700, color: T.text, letterSpacing: '-0.025em' }}>
            <span>Разом:</span><span>{fmt(total)}</span>
          </div>
        </div>
      </div>
    </div>
  );
}

// ── DASHBOARD PAGE ────────────────────────────────────────────────
function DashboardPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [sec, setSec] = useState('catalog');
  const navItems = [
    { key:'catalog',   icon:'⊞', label:'Каталог'     },
    { key:'orders',    icon:'◫', label:'Замовлення'   },
    { key:'documents', icon:'⊟', label:'Документи'   },
    { key:'profile',   icon:'◎', label:'Профіль'     },
    { key:'support',   icon:'◈', label:'Підтримка'   },
    { key:'study',     icon:'◉', label:'Навч. центр' },
  ];
  return (
    <div style={{ background: T.bg, minHeight: 'calc(100vh - 100px)', display: 'grid', gridTemplateColumns: '220px 1fr' }}>
      <div style={{ background: T.surface, borderRight: `1px solid ${T.border}`, padding: '20px 0', display: 'flex', flexDirection: 'column' }}>
        <div style={{ padding: '0 16px 20px', borderBottom: `1px solid ${T.border}`, marginBottom: 8 }}>
          <div style={{ width: 44, height: 44, background: T.muted, borderRadius: T.rLg, display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: 10, fontSize: 20 }}>👤</div>
          <div style={{ fontSize: 13, fontWeight: 600, color: T.text, letterSpacing: '-0.01em' }}>ТОВ "Авто Сервіс"</div>
          <div style={{ fontSize: 12, color: T.text3, marginTop: 2 }}>Бізнес-партнер</div>
        </div>
        <div style={{ flex: 1 }}>
          {navItems.map(item => (
            <div key={item.key} onClick={() => setSec(item.key)}
              style={{ padding: '9px 16px', fontSize: 14, cursor: 'pointer', fontWeight: sec===item.key?500:400,
                color: sec===item.key?T.text:T.text2, background: sec===item.key?T.muted:'transparent',
                borderLeft: `2px solid ${sec===item.key?T.invBg:'transparent'}`, transition: 'all 0.12s',
                display: 'flex', alignItems: 'center', gap: 10 }}>
              <span style={{ fontSize: 15 }}>{item.icon}</span>{item.label}
            </div>
          ))}
        </div>
        <div style={{ padding: '16px', borderTop: `1px solid ${T.border}` }}>
          <span onClick={() => setPage('main')} style={{ fontSize: 13, color: T.text3, cursor: 'pointer' }}
            onMouseEnter={e => e.target.style.color = T.text2} onMouseLeave={e => e.target.style.color = T.text3}>
            ← Магазин
          </span>
        </div>
      </div>
      <div style={{ padding: 32, overflow: 'auto' }}>
        {sec==='catalog' && (
          <div>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
              <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Каталог</h2>
              <span style={{ padding: '4px 12px', background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rPill, fontSize: 11, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.08em' }}>Партнерські ціни</span>
            </div>
            <div style={{ position: 'relative', marginBottom: 24 }}>
              <svg style={{ position: 'absolute', left: 12, top: '50%', transform: 'translateY(-50%)', color: T.text3 }}
                width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
              </svg>
              <input placeholder="Пошук по каталогу..."
                style={{ width: '100%', background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '10px 16px 10px 38px', color: T.text, fontSize: 14, outline: 'none', fontFamily: 'Geist, Inter, sans-serif' }}/>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 12 }}>
              {CATEGORIES.map(cat => <CatCard key={cat.id} cat={cat} onSelect={() => {}}/>)}
            </div>
          </div>
        )}
        {sec==='orders' && (
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Замовлення</h2>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {[{id:'#A-2026-04220',date:'22 квіт 2026',status:'processing',total:3297,items:3},{id:'#A-2026-04100',date:'10 квіт 2026',status:'delivered',total:1099,items:1},{id:'#A-2026-03150',date:'15 берез 2026',status:'delivered',total:5640,items:7}].map(o => (
                <div key={o.id} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '14px 18px', display: 'flex', alignItems: 'center', gap: 16, cursor: 'pointer', boxShadow: T.shadow, transition: 'border-color 0.12s' }}
                  onMouseEnter={e => e.currentTarget.style.borderColor = T.border2} onMouseLeave={e => e.currentTarget.style.borderColor = T.border}>
                  <div style={{ flex: 1 }}>
                    <div style={{ fontSize: 14, fontWeight: 600, color: T.text }}>{o.id}</div>
                    <div style={{ fontSize: 12, color: T.text2, marginTop: 2 }}>{o.date} · {o.items} товари</div>
                  </div>
                  <span style={{ padding: '4px 12px', borderRadius: T.rPill, fontSize: 12, fontWeight: 500,
                    background: o.status==='delivered'?(T.bg==='#09090b'?'#052e16':'#f0fdf4'):T.muted,
                    color: o.status==='delivered'?'#16a34a':T.text2,
                    border: `1px solid ${o.status==='delivered'?(T.bg==='#09090b'?'#14532d':'#bbf7d0'):T.border}` }}>
                    {o.status==='delivered'?'Доставлено':'В обробці'}
                  </span>
                  <div style={{ fontSize: 16, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', minWidth: 100, textAlign: 'right' }}>{fmt(o.total)}</div>
                </div>
              ))}
            </div>
          </div>
        )}
        {sec==='documents' && (
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Документи</h2>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 10 }}>
              {['Рахунок-фактура #2026-04','Акт виконаних робіт','Прайс-лист партнера','Договір поставки','Видаткова накладна','Сертифікати якості'].map(doc => (
                <div key={doc} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 18, display: 'flex', gap: 12, alignItems: 'center', cursor: 'pointer', boxShadow: T.shadow, transition: 'all 0.15s' }}
                  onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
                  onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke={T.text2} strokeWidth="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  <div style={{ fontSize: 13, fontWeight: 500, color: T.text }}>{doc}</div>
                </div>
              ))}
            </div>
          </div>
        )}
        {sec==='profile' && (
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Профіль</h2>
            <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28, maxWidth: 480 }}>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                <Field label="Назва компанії" placeholder="ТОВ 'Авто Сервіс'"/>
                <Field label="Контактна особа" placeholder="Іван Іваненко"/>
                <Field label="Email" placeholder="ivan@company.ua" type="email"/>
                <Field label="Телефон" placeholder="+380 XX XXX XX XX"/>
                <Field label="ЄДРПОУ" placeholder="12345678"/>
                <div style={{ paddingTop: 4 }}><Btn>Зберегти зміни</Btn></div>
              </div>
            </div>
          </div>
        )}
        {sec==='support' && (
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Підтримка</h2>
            <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28, maxWidth: 540 }}>
              <div style={{ background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '14px 16px', marginBottom: 24 }}>
                <div style={{ fontSize: 12, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 4 }}>Ваш менеджер</div>
                <div style={{ fontSize: 14, fontWeight: 600, color: T.text }}>Марина Коваленко</div>
                <div style={{ fontSize: 12, color: T.text2, marginTop: 2 }}>+380 44 123-45-68 · marina@a-green.com.ua</div>
              </div>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                <Field label="Тема звернення" placeholder="Коротко опишіть питання..."/>
                <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                  <label style={{ fontSize: 14, fontWeight: 500, color: T.text }}>Повідомлення</label>
                  <textarea placeholder="Детальний опис..." style={{ background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '10px 12px', color: T.text, fontSize: 14, outline: 'none', resize: 'vertical', minHeight: 100, fontFamily: 'Geist, Inter, sans-serif' }}/>
                </div>
                <div><Btn>Надіслати</Btn></div>
              </div>
            </div>
          </div>
        )}
        {sec==='study' && (
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Навчальний центр</h2>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 12 }}>
              {[{title:'Робота з диспенсерами',type:'Відеокурс',dur:'45 хв',isNew:true},{title:'Абразивні матеріали: вибір',type:'Вебінар',dur:'1.5 год',isNew:false},{title:'Лакофарбові роботи',type:'Відеокурс',dur:'3 год',isNew:false},{title:'Сертифікація персоналу',type:'Іспит',dur:'2 год',isNew:true},{title:'Зберігання хімматеріалів',type:'Документ',dur:'15 хв',isNew:false},{title:'Нове від Kimberly-Clark 2026',type:'Презентація',dur:'30 хв',isNew:true}].map(c => (
                <div key={c.title} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 20, cursor: 'pointer', transition: 'all 0.2s', boxShadow: T.shadow }}
                  onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
                  onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 10 }}>
                    <span style={{ fontSize: 11, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.08em' }}>{c.type}</span>
                    {c.isNew && <Badge label="Новинка"/>}
                  </div>
                  <div style={{ fontSize: 14, fontWeight: 500, color: T.text, lineHeight: 1.5, marginBottom: 10 }}>{c.title}</div>
                  <div style={{ fontSize: 12, color: T.text3 }}>⏱ {c.dur}</div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

Object.assign(window, { MainPage, CatalogPage, ProductPage, CartPage, CheckoutPage, DashboardPage });
