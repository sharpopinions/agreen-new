// ── EXTRA PAGES ─────────────────────────────────────────────────
// 404, About, Brands, Blog, Blog-Post, Services, Contacts, Favorites, Aktsyia

// ── 404 ──────────────────────────────────────────────────────────
function Page404({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, minHeight: '70vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', padding: '60px 60px', textAlign: 'center' }}>
      <div style={{ fontSize: 120, fontWeight: 800, color: T.border2, letterSpacing: '-0.06em', lineHeight: 1, fontFamily: 'Geist, Inter, sans-serif', marginBottom: 24 }}>404</div>
      <div style={{ width: 120, height: 80, marginBottom: 32 }}>
        <Img h={80} label="🎨"/>
      </div>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, marginBottom: 16, letterSpacing: '-0.025em', maxWidth: 480 }}>
        Здається, сторінку відправили на фарбування 😉…
      </h1>
      <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.7, marginBottom: 32, maxWidth: 480 }}>
        Посилання, за яким ви перейшли, більше неактуальне або сторінку видалено. Поверніться на головну сторінку або скористайтесь меню сайту.
      </p>
      <div style={{ display: 'flex', gap: 12 }}>
        <Btn onClick={() => setPage('main')}>На головну</Btn>
        <Btn variant="outline" onClick={() => setPage('catalog')}>До каталогу</Btn>
      </div>
    </div>
  );
}

// ── ABOUT ────────────────────────────────────────────────────────
function AboutPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Про компанію']} setPage={setPage}/>
      <h1 style={{ fontSize: 32, fontWeight: 700, color: T.text, letterSpacing: '-0.03em', marginBottom: 40, textAlign: 'center', fontFamily: 'Geist, Inter, sans-serif' }}>Про компанію</h1>

      {/* Hero banner */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 48 }}>
        <Img h={320} label="фото компанії — виробництво"/>
      </div>

      {/* Intro */}
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 48, marginBottom: 56 }}>
        <div>
          <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.02em', marginBottom: 16, fontFamily: 'Geist, Inter, sans-serif' }}>A-green — професійний постачальник рішень для бізнесу</h2>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.8, marginBottom: 16 }}>
            Забезпечуємо підприємства малярно-кузовного ремонту, промисловості та гігієнічної сфери продукцією власного імпорту та виробництва.
          </p>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.8 }}>
            Понад 10 років ми є надійним партнером для сотень підприємств по всій Україні. Наша команда фахівців завжди готова допомогти у виборі правильного рішення.
          </p>
        </div>
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          {[['10+','Років на ринку'],['500+','Клієнтів в Україні'],['50+','Тисяч найменувань'],['80+','Брендів-партнерів']].map(([n,l]) => (
            <div key={n} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '16px 24px', display: 'flex', alignItems: 'center', gap: 20 }}>
              <div style={{ fontSize: 32, fontWeight: 800, color: T.text, letterSpacing: '-0.04em', fontFamily: 'Geist, Inter, sans-serif', minWidth: 80 }}>{n}</div>
              <div style={{ fontSize: 13, color: T.text2 }}>{l}</div>
            </div>
          ))}
        </div>
      </div>

      {/* Values */}
      <div style={{ marginBottom: 56 }}>
        <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.02em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Наші переваги</h2>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14 }}>
          {[
            { n:'01', title:'Широкий асортимент', desc:'Понад 50 тисяч найменувань продукції від провідних світових виробників' },
            { n:'02', title:'Гнучкі умови',       desc:'Індивідуальний підхід до кожного клієнта, система знижок та відстрочки платежу' },
            { n:'03', title:'Швидка доставка',    desc:'Доставка по всій Україні за 1–3 дні. Власний складський комплекс у Києві' },
            { n:'04', title:'Технічна підтримка', desc:'Консультації фахівців на всіх етапах вибору та застосування продукції' },
            { n:'05', title:'Навчання',            desc:'Навчальний центр для підготовки спеціалістів та сертифікація персоналу' },
            { n:'06', title:'Гарантія якості',    desc:'Вся продукція сертифікована та відповідає міжнародним стандартам якості' },
          ].map(s => (
            <div key={s.n} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 24, transition: 'all 0.2s', boxShadow: T.shadow }}
              onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
              onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
              <div style={{ fontSize: 12, fontWeight: 600, color: T.text3, marginBottom: 12, letterSpacing: '0.04em' }}>{s.n}</div>
              <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 8 }}>{s.title}</div>
              <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.65 }}>{s.desc}</div>
            </div>
          ))}
        </div>
      </div>

      {/* Brands */}
      <div style={{ marginBottom: 56 }}>
        <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.02em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Наші бренди</h2>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5, 1fr)', gap: 12 }}>
          {BRANDS.map(b => (
            <div key={b} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '20px', textAlign: 'center', cursor: 'pointer', transition: 'all 0.15s', boxShadow: T.shadow }}
              onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
              onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
              <div style={{ height: 48, display: 'flex', alignItems: 'center', justifyContent: 'center', marginBottom: 8 }}>
                <div style={{ width: 48, height: 48, background: T.muted, borderRadius: T.rSm, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 11, fontWeight: 700, color: T.text3 }}>{b[0]}</div>
              </div>
              <div style={{ fontSize: 12, fontWeight: 500, color: T.text }}>{b}</div>
            </div>
          ))}
        </div>
      </div>

      {/* Team / contacts CTA */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '40px 48px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <div>
          <h3 style={{ fontSize: 20, fontWeight: 700, color: T.text, marginBottom: 8, letterSpacing: '-0.02em' }}>Готові до співпраці?</h3>
          <p style={{ fontSize: 14, color: T.text2 }}>Зв'яжіться з нами або ознайомтесь з умовами партнерства</p>
        </div>
        <div style={{ display: 'flex', gap: 12 }}>
          <Btn onClick={() => setPage('contacts')}>Зв'язатись</Btn>
          <Btn variant="outline" onClick={() => setPage('partners')}>Партнерство</Btn>
        </div>
      </div>
    </div>
  );
}

// ── BRANDS ───────────────────────────────────────────────────────
function BrandsPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [search, setSearch] = useState('');
  const filtered = BRANDS.filter(b => b.toLowerCase().includes(search.toLowerCase()));

  const BRAND_CATS = ['Всі','Абразиви','Гігієна','Лакофарбові','Захист','Клеї'];
  const [cat, setCat] = useState('Всі');

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Бренди']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 6, fontFamily: 'Geist, Inter, sans-serif' }}>Бренди</h1>
      <p style={{ fontSize: 14, color: T.text2, marginBottom: 24 }}>Ми представляємо провідних виробників у своїх категоріях</p>

      {/* Search */}
      <div style={{ position: 'relative', marginBottom: 20 }}>
        <svg style={{ position: 'absolute', left: 12, top: '50%', transform: 'translateY(-50%)', color: T.text3, pointerEvents: 'none' }}
          width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
          <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Введіть назву бренду..."
          style={{ width: '100%', background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '10px 16px 10px 40px', color: T.text, fontSize: 14, outline: 'none', fontFamily: 'Geist, Inter, sans-serif' }}
          onFocus={e => e.target.style.borderColor = T.border2} onBlur={e => e.target.style.borderColor = T.border}/>
      </div>

      {/* Category tabs */}
      <div style={{ display: 'flex', gap: 8, marginBottom: 28, flexWrap: 'wrap' }}>
        {BRAND_CATS.map(c => (
          <button key={c} onClick={() => setCat(c)}
            style={{ padding: '7px 18px', borderRadius: T.rPill,
              background: cat === c ? T.invBg : T.surface,
              border: `1px solid ${cat === c ? T.invBg : T.border}`,
              color: cat === c ? T.invText : T.text2, fontSize: 13, fontWeight: cat === c ? 600 : 400,
              cursor: 'pointer', fontFamily: 'Geist, Inter, sans-serif', transition: 'all 0.15s' }}>
            {c}
          </button>
        ))}
      </div>

      {/* Brand grid */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5, 1fr)', gap: 16 }}>
        {filtered.map(b => (
          <div key={b} onClick={() => setPage('brand-products')}
            style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28,
              textAlign: 'center', cursor: 'pointer', transition: 'all 0.2s', boxShadow: T.shadow }}
            onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
            onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
            <div style={{ width: 72, height: 72, background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rLg, margin: '0 auto 16px', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 20, fontWeight: 700, color: T.text2 }}>
              {b[0]}
            </div>
            <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 4 }}>{b}</div>
            <div style={{ fontSize: 12, color: T.text3 }}>Переглянути товари →</div>
          </div>
        ))}
      </div>
      {filtered.length === 0 && (
        <div style={{ textAlign: 'center', padding: '60px 20px', color: T.text2 }}>
          <div style={{ fontSize: 14 }}>Бренд не знайдено</div>
        </div>
      )}
    </div>
  );
}

// ── BLOG ─────────────────────────────────────────────────────────
const BLOG_POSTS = [
  { id:1, cat:'Новини ринку',    title:'Нові технології лакофарбових покриттів у 2026 році',          date:'27 квіт 2026', read:'5 хв' },
  { id:2, cat:'Поради',         title:'Безпека на виробництві: огляд засобів захисту для персоналу',  date:'20 квіт 2026', read:'7 хв' },
  { id:3, cat:'ЗМІ про нас',    title:'Дозуючі системи Kimberly-Clark: повний огляд лінійки 2026',    date:'15 квіт 2026', read:'4 хв' },
  { id:4, cat:'Новинки',        title:'Abrasives Mirka: нові позиції в асортименті A-green',          date:'10 квіт 2026', read:'3 хв' },
  { id:5, cat:'Поради',         title:'Як правильно вибрати абразивний матеріал для кузовного ремонту',date:'05 квіт 2026', read:'8 хв' },
  { id:6, cat:'ЗМІ про нас',    title:'A-green на виставці Paint & Body 2026',                         date:'01 квіт 2026', read:'3 хв' },
  { id:7, cat:'Новини ринку',   title:'Зміни у системі сертифікації хімічної продукції в Україні',    date:'25 бер 2026',  read:'6 хв' },
  { id:8, cat:'Новинки',        title:'Нові засоби гігієни Dettol Professional у нашому каталозі',    date:'20 бер 2026',  read:'2 хв' },
];

function BlogPage({ setPage, initialCategory }) {
  const { T } = useContext(ThemeCtx);
  const cats = ['Всі', ...Array.from(new Set(BLOG_POSTS.map(p => p.cat)))];
  const [cat, setCat] = useState(initialCategory || 'Всі');
  const filtered = cat === 'Всі' ? BLOG_POSTS : BLOG_POSTS.filter(p => p.cat === cat);

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Блог']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 24, textAlign: 'center', fontFamily: 'Geist, Inter, sans-serif' }}>Блог</h1>

      {/* Category tabs */}
      <div style={{ display: 'flex', gap: 8, marginBottom: 32, justifyContent: 'center', flexWrap: 'wrap' }}>
        {cats.map(c => (
          <button key={c} onClick={() => setCat(c)}
            style={{ padding: '7px 18px', borderRadius: T.rPill,
              background: cat === c ? T.invBg : T.surface,
              border: `1px solid ${cat === c ? T.invBg : T.border}`,
              color: cat === c ? T.invText : T.text2, fontSize: 13, fontWeight: cat === c ? 600 : 400,
              cursor: 'pointer', fontFamily: 'Geist, Inter, sans-serif', transition: 'all 0.15s' }}>
            {c}
          </button>
        ))}
      </div>

      {/* Posts grid */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16 }}>
        {filtered.map(post => (
          <div key={post.id} onClick={() => setPage('blog-post')}
            style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg,
              overflow: 'hidden', cursor: 'pointer', transition: 'all 0.2s', boxShadow: T.shadow }}
            onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
            onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
            <Img h={160} label="фото"/>
            <div style={{ padding: '16px' }}>
              <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                <span style={{ fontSize: 10, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.08em' }}>{post.cat}</span>
                <span style={{ fontSize: 10, color: T.text3 }}>{post.read} читання</span>
              </div>
              <div style={{ fontSize: 13, fontWeight: 500, color: T.text, lineHeight: 1.5, marginBottom: 12 }}>{post.title}</div>
              <div style={{ fontSize: 12, color: T.text3 }}>{post.date}</div>
            </div>
          </div>
        ))}
      </div>
      <div style={{ textAlign: 'center', marginTop: 32 }}>
        <Btn variant="outline">Завантажити ще</Btn>
      </div>
    </div>
  );
}

// ── BLOG POST ─────────────────────────────────────────────────────
function BlogPostPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const post = BLOG_POSTS[0];
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Блог', post.title.substring(0,30) + '…']} setPage={setPage}/>
      <div style={{ maxWidth: 800, margin: '0 auto' }}>
        <div style={{ marginBottom: 24 }}>
          <span style={{ fontSize: 11, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.1em', marginRight: 16 }}>{post.cat}</span>
          <span style={{ fontSize: 12, color: T.text3 }}>{post.date} · {post.read} читання</span>
        </div>
        <h1 style={{ fontSize: 30, fontWeight: 700, color: T.text, lineHeight: 1.3, letterSpacing: '-0.025em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>{post.title}</h1>
        <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 32 }}>
          <Img h={360} label="головне фото статті"/>
        </div>
        {[
          'Ринок лакофарбових покриттів постійно розвивається, пропонуючи нові рішення для підвищення якості малярно-кузовних робіт. У 2026 році ми спостерігаємо кілька ключових тенденцій.',
          'Серед них — розвиток водорозчинних технологій, підвищена стійкість до ультрафіолету та нові формули для прискорення сушіння. Ці інновації дозволяють підприємствам значно скоротити час виробничого циклу.',
          'A-green представляє оновлену лінійку продукції від провідних виробників, що відповідає найновішим стандартам галузі. Наші фахівці готові допомогти у виборі оптимального рішення для вашого підприємства.',
        ].map((p, i) => (
          <p key={i} style={{ fontSize: 15, color: T.text2, lineHeight: 1.85, marginBottom: 20 }}>{p}</p>
        ))}
        <div style={{ borderTop: `1px solid ${T.border}`, paddingTop: 32, marginTop: 40 }}>
          <h3 style={{ fontSize: 18, fontWeight: 700, color: T.text, marginBottom: 20 }}>Схожі статті</h3>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14 }}>
            {BLOG_POSTS.slice(1, 4).map(p => (
              <div key={p.id} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden', cursor: 'pointer' }}
                onMouseEnter={e => e.currentTarget.style.borderColor = T.border2} onMouseLeave={e => e.currentTarget.style.borderColor = T.border}>
                <Img h={120} label=""/>
                <div style={{ padding: 14 }}>
                  <div style={{ fontSize: 12, fontWeight: 500, color: T.text, lineHeight: 1.5 }}>{p.title}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

// ── SERVICES ─────────────────────────────────────────────────────
function ServicesPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const services = [
    { n:'01', title:'Навчальний центр',   desc:'Навчання та сертифікація персоналу з роботи з продукцією провідних виробників. Курси для спеціалістів малярно-кузовного ремонту та промислових підприємств.', details:['Теоретичні та практичні заняття','Видача сертифікатів','Навчання на базі підприємства клієнта'] },
    { n:'02', title:'Технічна підтримка', desc:'Консультації фахівців з підбору продукції та обладнання. Допомога у вирішенні технічних питань на всіх етапах застосування.', details:['Безкоштовна консультація','Виїзд спеціаліста','Гаряча лінія 24/7'] },
    { n:'03', title:'Проектування',       desc:'Розробка комплексних рішень для оснащення підприємств. Проектування малярних кабін, ліній нанесення покриттів та систем дозування.', details:['Технічне завдання','3D-моделювання','Авторський нагляд'] },
    { n:'04', title:'Обслуговування',     desc:'Технічне обслуговування та ремонт обладнання. Планові та позапланові технічні огляди, заміна витратних матеріалів.', details:['Планове ТО','Аварійний виїзд','Запасні частини'] },
  ];
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Послуги']} setPage={setPage}/>

      {/* Banner */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 40 }}>
        <Img h={260} label="банер послуг — обладнання та спеціалісти"/>
      </div>

      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 8, fontFamily: 'Geist, Inter, sans-serif' }}>Послуги</h1>
      <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.65, marginBottom: 40, maxWidth: 680 }}>
        Ми надаємо повний спектр послуг для підтримки вашого бізнесу — від навчання персоналу до технічного обслуговування обладнання.
      </p>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 48 }}>
        {services.map(s => (
          <div key={s.n} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28, transition: 'all 0.2s', boxShadow: T.shadow }}
            onMouseEnter={e => { e.currentTarget.style.borderColor = T.border2; e.currentTarget.style.boxShadow = T.shadowMd; }}
            onMouseLeave={e => { e.currentTarget.style.borderColor = T.border; e.currentTarget.style.boxShadow = T.shadow; }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 16 }}>
              <div style={{ width: 40, height: 40, background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rSm, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 13, fontWeight: 700, color: T.text3 }}>{s.n}</div>
            </div>
            <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 10 }}>{s.title}</h3>
            <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.65, marginBottom: 16 }}>{s.desc}</p>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 6, marginBottom: 20 }}>
              {s.details.map(d => (
                <div key={d} style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13, color: T.text2 }}>
                  <div style={{ width: 4, height: 4, borderRadius: '50%', background: T.text3, flexShrink: 0 }}/>
                  {d}
                </div>
              ))}
            </div>
            <Btn variant="outline" size="sm">Детальніше</Btn>
          </div>
        ))}
      </div>

      {/* Reviews */}
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.02em', marginBottom: 24, fontFamily: 'Geist, Inter, sans-serif' }}>Відгуки клієнтів</h2>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14, marginBottom: 48 }}>
        {[
          { name:'ТОВ «Автосервіс Плюс»', text:'Навчальний центр A-green допоміг підготувати наш персонал. Дякуємо за якісний підхід!', rating:5 },
          { name:'ТОВ «ProPaint»',         text:'Технічна підтримка завжди на висоті. Швидко вирішують будь-які питання.', rating:5 },
          { name:'ФОП Коваленко',          text:'Проектування малярної кабіни пройшло бездоганно. Рекомендуємо!', rating:5 },
        ].map((r, i) => (
          <div key={i} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 24, boxShadow: T.shadow }}>
            <div style={{ marginBottom: 12 }}><Stars rating={r.rating}/></div>
            <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.7, marginBottom: 16, fontStyle: 'italic' }}>"{r.text}"</p>
            <div style={{ fontSize: 13, fontWeight: 600, color: T.text }}>{r.name}</div>
          </div>
        ))}
      </div>

      {/* CTA */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '36px 48px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <div>
          <h3 style={{ fontSize: 20, fontWeight: 700, color: T.text, marginBottom: 8 }}>Залиште заявку на послугу</h3>
          <p style={{ fontSize: 14, color: T.text2 }}>Наш менеджер зв'яжеться з вами протягом 30 хвилин</p>
        </div>
        <Btn onClick={() => setPage('contacts')}>Залишити заявку</Btn>
      </div>
    </div>
  );
}

// ── CONTACTS ─────────────────────────────────────────────────────
function ContactsPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [form, setForm] = useState({ name:'', phone:'', email:'', msg:'' });
  const upd = (k, v) => setForm(f => ({ ...f, [k]: v }));
  const [sent, setSent] = useState(false);

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Контакти']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 40, fontFamily: 'Geist, Inter, sans-serif' }}>Контакти</h1>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 32, marginBottom: 40 }}>
        {/* Contact info */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
          {[
            { label:'Адреса', value:'02660, м. Київ, вул. Крайня, 1', icon:'📍' },
            { label:'E-mail адреса', value:'info@a-green.com.ua\noffice@a-green.com.ua', icon:'✉' },
            { label:'Графік роботи', value:'Пн – Чт: 9:00 – 16:00\nПт: до 15:00\nСб та Нд: вихідні', icon:'🕐' },
            { label:'Відділ продажу', value:'+38 (097) 075-71-70\n+38 (050) 075-71-70\n+38 (063) 075-71-70', icon:'📞' },
            { label:'Бухгалтерія', value:'+38 (097) 075-71-70', icon:'📞' },
          ].map(c => (
            <div key={c.label} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '16px 20px', display: 'flex', gap: 16, alignItems: 'flex-start' }}>
              <div style={{ fontSize: 20, flexShrink: 0, marginTop: 2 }}>{c.icon}</div>
              <div>
                <div style={{ fontSize: 12, fontWeight: 600, color: T.text3, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 6 }}>{c.label}</div>
                <div style={{ fontSize: 14, color: T.text, lineHeight: 1.7, whiteSpace: 'pre-line' }}>{c.value}</div>
              </div>
            </div>
          ))}
        </div>

        {/* Form */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 32 }}>
          {sent ? (
            <div style={{ textAlign: 'center', padding: '40px 20px' }}>
              <div style={{ fontSize: 40, marginBottom: 16 }}>✅</div>
              <h3 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 8 }}>Повідомлення надіслано!</h3>
              <p style={{ fontSize: 14, color: T.text2 }}>Ми зв'яжемось з вами найближчим часом.</p>
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
              <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 4 }}>Написати нам</h3>
              <Field label="Ім'я *" placeholder="Іван Іваненко" value={form.name} onChange={e => upd('name', e.target.value)}/>
              <Field label="Телефон *" placeholder="+380 XX XXX XX XX" value={form.phone} onChange={e => upd('phone', e.target.value)}/>
              <Field label="Email" placeholder="email@company.ua" type="email" value={form.email} onChange={e => upd('email', e.target.value)}/>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                <label style={{ fontSize: 14, fontWeight: 500, color: T.text }}>Повідомлення</label>
                <textarea value={form.msg} onChange={e => upd('msg', e.target.value)} placeholder="Ваше запитання або пропозиція..."
                  style={{ background: T.bgAlt, border: `1px solid ${T.border}`, borderRadius: T.rSm, padding: '10px 12px', color: T.text, fontSize: 14, outline: 'none', resize: 'vertical', minHeight: 100, fontFamily: 'Geist, Inter, sans-serif' }}/>
              </div>
              <Btn onClick={() => setSent(true)}>Надіслати</Btn>
            </div>
          )}
        </div>
      </div>

      {/* Map placeholder */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', border: `1px solid ${T.border}` }}>
        <Img h={300} label="карта — Київ, вул. Крайня 1"/>
      </div>
    </div>
  );
}

// ── FAVORITES ────────────────────────────────────────────────────
function FavoritesPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [items, setItems] = useState(PRODUCTS.slice(0, 3));

  if (items.length === 0) return (
    <div style={{ background: T.bg, minHeight: '55vh', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', gap: 16 }}>
      <div style={{ width: 64, height: 64, background: T.muted, borderRadius: T.rLg, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.5">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </div>
      <div style={{ fontSize: 18, fontWeight: 600, color: T.text }}>Список бажань порожній</div>
      <div style={{ fontSize: 14, color: T.text2 }}>Додайте товари з каталогу</div>
      <Btn onClick={() => setPage('catalog')}>До каталогу</Btn>
    </div>
  );

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Обрані товари']} setPage={setPage}/>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: 24 }}>
        <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>Обрані товари</h1>
        <span style={{ fontSize: 14, color: T.text2 }}>{items.length} товарів</span>
      </div>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16, marginBottom: 32 }}>
        {items.map(p => (
          <div key={p.id} style={{ position: 'relative' }}>
            <ProductCard product={p} onView={() => setPage('product')} onAdd={addToCart}/>
            <button onClick={() => setItems(prev => prev.filter(i => i.id !== p.id))}
              style={{ position: 'absolute', top: 8, right: 8, background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rSm, width: 32, height: 32, display: 'flex', alignItems: 'center', justifyContent: 'center', cursor: 'pointer', zIndex: 10 }}>
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>
        ))}
      </div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '16px 24px' }}>
        <span style={{ fontSize: 14, color: T.text2 }}>Всього: {items.length} товарів</span>
        <div style={{ display: 'flex', gap: 10 }}>
          <Btn variant="outline" onClick={() => setItems([])}>Очистити список</Btn>
          <Btn onClick={() => { items.forEach(p => addToCart(p)); setPage('cart'); }}>Додати все до кошика</Btn>
        </div>
      </div>
    </div>
  );
}

// ── SALE PAGE (Акція) ─────────────────────────────────────────────
function SalePage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const saleProducts = PRODUCTS.filter(p => p.badge === 'Акція' || p.oldPrice);

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Акції']} setPage={setPage}/>

      {/* Banner */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, overflow: 'hidden', marginBottom: 32 }}>
        <Img h={200} label="акційний банер — знижки до 30%"/>
      </div>

      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 8, fontFamily: 'Geist, Inter, sans-serif' }}>Акції</h1>
      <p style={{ fontSize: 14, color: T.text2, marginBottom: 24 }}>Спеціальні пропозиції та знижки на обрані товари</p>

      <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20 }}>
        {/* Filters sidebar */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 20, height: 'fit-content' }}>
          <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 16 }}>Фільтри</div>
          <div style={{ fontSize: 12, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 10 }}>Тип акції</div>
          {['Знижка','Розпродаж','Бонус','Подарунок'].map(t => (
            <div key={t} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '5px 0', cursor: 'pointer', fontSize: 13, color: T.text2 }}>
              <div style={{ width: 16, height: 16, border: `1.5px solid ${T.border2}`, borderRadius: 4, flexShrink: 0 }}/>
              {t}
            </div>
          ))}
          <div style={{ height: 1, background: T.border, margin: '16px 0' }}/>
          <div style={{ fontSize: 12, fontWeight: 600, color: T.text2, textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 10 }}>Бренд</div>
          {BRANDS.slice(0, 5).map(b => (
            <div key={b} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '5px 0', cursor: 'pointer', fontSize: 13, color: T.text2 }}>
              <div style={{ width: 16, height: 16, border: `1.5px solid ${T.border2}`, borderRadius: 4, flexShrink: 0 }}/>
              {b}
            </div>
          ))}
        </div>
        {/* Products */}
        <div>
          <div style={{ fontSize: 14, color: T.text2, marginBottom: 14 }}>
            Знайдено: <strong style={{ color: T.text }}>{saleProducts.length}</strong> акційних товарів
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16 }}>
            {saleProducts.length > 0
              ? saleProducts.map(p => <ProductCard key={p.id} product={p} onView={() => setPage('product')} onAdd={addToCart}/>)
              : PRODUCTS.slice(0, 3).map(p => <ProductCard key={p.id} product={p} onView={() => setPage('product')} onAdd={addToCart}/>)
            }
          </div>
        </div>
      </div>
    </div>
  );
}

// ── PARTNERS ─────────────────────────────────────────────────────
function PartnersPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Партнерство']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 40, fontFamily: 'Geist, Inter, sans-serif' }}>Партнерство</h1>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16, marginBottom: 40 }}>
        {[
          { title:'Дилери',                  desc:'Авторизовані партнери з правом продажу продукції A-green у своєму регіоні. Знижки до 25%, маркетингова підтримка, навчання персоналу.' },
          { title:'Оптові покупці',           desc:'Підприємства, що закуповують продукцію для власного виробництва. Знижки від обсягу, відстрочка платежу, персональний менеджер.' },
          { title:'Партнери по установці',    desc:'Сервісні компанії та інсталятори. Технічна підтримка, навчання, пріоритетне обслуговування та спеціальні ціни.' },
        ].map(p => (
          <div key={p.title} style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28, boxShadow: T.shadow }}>
            <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 12 }}>{p.title}</h3>
            <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.7, marginBottom: 20 }}>{p.desc}</p>
            <Btn variant="outline" size="sm" onClick={() => setPage('dashboard')}>Подати заявку</Btn>
          </div>
        ))}
      </div>

      <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: '40px 48px' }}>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40 }}>
          <div>
            <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, marginBottom: 16, letterSpacing: '-0.02em' }}>Вигідні умови для партнерів</h2>
            {['Знижки до 25% від роздрібної ціни','Безкоштовна доставка від 5 000 ₴','Відстрочка платежу до 30 днів','Персональний менеджер','Маркетингова підтримка','Навчання персоналу'].map(item => (
              <div key={item} style={{ display: 'flex', alignItems: 'center', gap: 10, padding: '7px 0', borderBottom: `1px solid ${T.border}`, fontSize: 14, color: T.text2 }}>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text} strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {item}
              </div>
            ))}
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
            <Field label="Назва компанії *" placeholder="ТОВ 'Назва компанії'"/>
            <Field label="Контактна особа *" placeholder="Іван Іваненко"/>
            <Field label="Телефон *" placeholder="+380 XX XXX XX XX"/>
            <Field label="Email *" placeholder="email@company.ua" type="email"/>
            <Btn onClick={() => setPage('dashboard')}>Стати партнером</Btn>
          </div>
        </div>
      </div>
    </div>
  );
}

// ── DELIVERY PAGE ─────────────────────────────────────────────────
function DeliveryPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Доставка та оплата']} setPage={setPage}/>
      <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em', marginBottom: 40, fontFamily: 'Geist, Inter, sans-serif' }}>Доставка та оплата</h1>
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 32, marginBottom: 40 }}>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28 }}>
          <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 20 }}>Доставка</h2>
          {[
            { title:'Нова Пошта',      desc:'Доставка по всій Україні. 1–3 робочі дні. Вартість розраховується за тарифами перевізника.' },
            { title:'Укрпошта',        desc:'Доставка по всій Україні. 3–7 робочих днів.' },
            { title:'Кур\'єр по Києву',desc:'Доставка наступного робочого дня. Вартість: 150 ₴.' },
            { title:'Самовивіз',       desc:'Безкоштовно. Адреса: м. Київ, вул. Крайня, 1. Пн–Пт: 9:00–17:00.' },
          ].map(d => (
            <div key={d.title} style={{ marginBottom: 16, paddingBottom: 16, borderBottom: `1px solid ${T.border}` }}>
              <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 4 }}>{d.title}</div>
              <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.65 }}>{d.desc}</div>
            </div>
          ))}
        </div>
        <div style={{ background: T.surface, border: `1px solid ${T.border}`, borderRadius: T.rLg, padding: 28 }}>
          <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text, marginBottom: 20 }}>Оплата</h2>
          {[
            { title:'Картка онлайн',              desc:'Visa, Mastercard, Apple Pay, Google Pay. Безпечне з\'єднання SSL.' },
            { title:'Безготівковий розрахунок',   desc:'Для юридичних осіб. Рахунок-фактура після підтвердження замовлення.' },
            { title:'Оплата при отриманні',       desc:'Готівкою або карткою кур\'єру / у відділенні Нової Пошти.' },
          ].map(p => (
            <div key={p.title} style={{ marginBottom: 16, paddingBottom: 16, borderBottom: `1px solid ${T.border}` }}>
              <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 4 }}>{p.title}</div>
              <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.65 }}>{p.desc}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

Object.assign(window, {
  Page404, AboutPage, BrandsPage, BlogPage, BlogPostPage,
  ServicesPage, ContactsPage, FavoritesPage, SalePage,
  PartnersPage, DeliveryPage,
});
