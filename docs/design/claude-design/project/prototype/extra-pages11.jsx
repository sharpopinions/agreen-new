// ── EXTRA PAGES 11 ─────────────────────────────────────────────────
// ServicesPopup, BrandsCategory, BrandProductsSubcategory, MainCategoryOpen

// ── SERVICES POPUP (детальна послуга у попапі) ──────────────────
function ServicesPopupPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [open, setOpen] = useState(true);
  const [form, setForm] = useState({ name:'', phone:'', email:'', service:'' });
  const upd = (k,v) => setForm(f => ({...f, [k]:v}));
  const [sent, setSent] = useState(false);

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Послуги']} setPage={setPage}/>

      {/* Banner */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 32 }}>
        <Img h={300} label="Усі послуги A-green"/>
      </div>

      <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text,
        letterSpacing: '-0.025em', marginBottom: 24, textAlign: 'center',
        fontFamily: 'Geist, Inter, sans-serif' }}>
        Послуги A-green
      </h1>

      {/* Services grid */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 14, marginBottom: 48 }}>
        {[
          { n:'01', t:'Навчальний центр',    d:'Курси та сертифікація персоналу', icon:'🎓' },
          { n:'02', t:'Технічна підтримка',   d:'Консультації фахівців 24/7', icon:'🛠️' },
          { n:'03', t:'Проектування',         d:'Розробка комплексних рішень', icon:'📐' },
          { n:'04', t:'Сервісне обслуговування', d:'Ремонт обладнання', icon:'⚙️' },
          { n:'05', t:'Підбір кольору',       d:'Лабораторія A-green', icon:'🎨' },
          { n:'06', t:'Аудит виробництва',    d:'Оптимізація процесів', icon:'📊' },
          { n:'07', t:'Тестування продукції', d:'Безкоштовні зразки', icon:'🧪' },
          { n:'08', t:'Маркетингова підтримка',d:'Для партнерів та дилерів', icon:'📣' },
        ].map(s => (
          <div key={s.n} onClick={() => { setOpen(true); upd('service', s.t); }}
            style={{ background: T.surface, border: `1px solid ${T.border}`,
              borderRadius: T.rLg, padding: 24, cursor: 'pointer',
              transition: 'all 0.2s', boxShadow: T.shadow }}
            onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.boxShadow=T.shadowMd;}}
            onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.boxShadow=T.shadow;}}>
            <div style={{ fontSize: 24, marginBottom: 12 }}>{s.icon}</div>
            <div style={{ fontSize: 11, fontWeight: 600, color: T.text3,
              letterSpacing: '0.04em', marginBottom: 6 }}>{s.n}</div>
            <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 6 }}>{s.t}</div>
            <div style={{ fontSize: 12, color: T.text2, lineHeight: 1.6 }}>{s.d}</div>
          </div>
        ))}
      </div>

      {/* Popup overlay */}
      {open && (
        <div onClick={e => { if(e.target===e.currentTarget) setOpen(false); }}
          style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)',
            zIndex: 8000, display: 'flex', alignItems: 'center', justifyContent: 'center',
            padding: 40, backdropFilter: 'blur(4px)' }}>
          <div style={{ background: T.surface, border: `1px solid ${T.border}`,
            borderRadius: T.rLg, width: 560, padding: 40,
            boxShadow: '0 24px 64px rgba(0,0,0,0.3)' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between',
              alignItems: 'center', marginBottom: 20 }}>
              <h3 style={{ fontSize: 20, fontWeight: 700, color: T.text,
                letterSpacing: '-0.025em',
                fontFamily: 'Geist, Inter, sans-serif' }}>
                Замовити послугу
              </h3>
              <button onClick={() => setOpen(false)}
                style={{ background: 'none', border: 'none', cursor: 'pointer',
                  color: T.text3, padding: 4 }}>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" strokeWidth="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            {sent ? (
              <div style={{ textAlign: 'center', padding: '20px 0' }}>
                <div style={{ fontSize: 40, marginBottom: 14 }}>✅</div>
                <h4 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 6 }}>
                  Заявку прийнято!
                </h4>
                <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.6 }}>
                  Наш менеджер зв'яжеться з вами протягом 30 хвилин.
                </p>
                <div style={{ marginTop: 20 }}>
                  <Btn variant="outline" onClick={() => { setOpen(false); setSent(false); }}>
                    Закрити
                  </Btn>
                </div>
              </div>
            ) : (
              <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                <Field label="Послуга" placeholder="Назва послуги" value={form.service}
                  onChange={e => upd('service', e.target.value)}/>
                <Field label="Ім'я *" placeholder="Іван Іваненко" value={form.name}
                  onChange={e => upd('name', e.target.value)}/>
                <Field label="Телефон *" placeholder="+380 XX XXX XX XX" value={form.phone}
                  onChange={e => upd('phone', e.target.value)}/>
                <Field label="Email" placeholder="email@company.ua" type="email" value={form.email}
                  onChange={e => upd('email', e.target.value)}/>
                <p style={{ fontSize: 11, color: T.text3, lineHeight: 1.6 }}>
                  Заповнюючи форму, ви погоджуєтесь з{' '}
                  <span onClick={()=>setPage('privacy')} style={{ color: T.text2,
                    textDecoration: 'underline', cursor: 'pointer' }}>
                    політикою конфіденційності
                  </span>
                </p>
                <Btn full onClick={() => setSent(true)}>Надіслати заявку</Btn>
              </div>
            )}
          </div>
        </div>
      )}
    </div>
  );
}

// ── BRANDS-CATEGORY (бренди по категорії) ────────────────────────
function BrandsCategoryPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const cat = CATEGORIES[3]; // Лакофарбові матеріали
  const [selSub, setSelSub] = useState(null);

  const categoryBrands = [
    { name: 'Cromax',    by:'Axalta',  desc:'Премiум автоемалi',     count:840 },
    { name: 'Duxone',    by:'Axalta',  desc:'Стандартний сегмент',    count:540 },
    { name: 'Imron',     by:'Axalta',  desc:'Промислові покриття',    count:320 },
    { name: 'Anest Iwata',by:'Японія', desc:'Фарбувальне обладнання', count:180 },
    { name: '3M',        by:'США',     desc:'Абразиви та шпатлівки',  count:760 },
    { name: 'APP',       by:'Польща',  desc:'Витратні матеріали',      count:420 },
  ];

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Каталог', cat.name]} setPage={setPage}/>

      {/* Hero */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 32, marginBottom: 32, boxShadow: T.shadow }}>
        <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text,
          letterSpacing: '-0.025em', marginBottom: 12,
          fontFamily: 'Geist, Inter, sans-serif' }}>
          Бренди в категорії «{cat.name}»
        </h1>
        <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.7, maxWidth: 720 }}>
          У категорії «{cat.name}» представлені провідні світові виробники лакофарбової продукції.
          Оберіть бренд, щоб переглянути асортимент.
        </p>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20 }}>
        {/* Sidebar: subcategories */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, height: 'fit-content', overflow: 'hidden' }}>
          <div style={{ padding: '14px 18px', borderBottom: `1px solid ${T.border}`,
            fontSize: 14, fontWeight: 600, color: T.text }}>
            Підкатегорії
          </div>
          {['Ґрунтовки','Базові емалі','Лаки','Розчинники','Шпатлівки','Аерозолі'].map((sub, i) => (
            <div key={sub} onClick={() => setSelSub(selSub===sub ? null : sub)}
              style={{ padding: '11px 18px', fontSize: 13, cursor: 'pointer',
                color: selSub===sub ? T.text : T.text2,
                background: selSub===sub ? T.muted : 'transparent',
                borderBottom: i < 5 ? `1px solid ${T.border}` : 'none',
                display: 'flex', justifyContent: 'space-between',
                transition: 'background 0.12s' }}
              onMouseEnter={e => { if(selSub!==sub) e.currentTarget.style.background=T.muted; }}
              onMouseLeave={e => { if(selSub!==sub) e.currentTarget.style.background='transparent'; }}>
              <span>{sub}</span>
              <span style={{ fontSize: 11, color: T.text3 }}>({80 + i*20})</span>
            </div>
          ))}
        </div>

        {/* Brand grid */}
        <div>
          <div style={{ fontSize: 13, color: T.text2, marginBottom: 14 }}>
            Знайдено: <strong style={{ color: T.text }}>{categoryBrands.length}</strong> брендів
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14 }}>
            {categoryBrands.map(b => (
              <div key={b.name} onClick={() => setPage('brand-products')}
                style={{ background: T.surface, border: `1px solid ${T.border}`,
                  borderRadius: T.rLg, padding: 24, cursor: 'pointer',
                  transition: 'all 0.2s', boxShadow: T.shadow }}
                onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.boxShadow=T.shadowMd;}}
                onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.boxShadow=T.shadow;}}>
                <div style={{ width: 60, height: 60, background: T.bgAlt,
                  border: `1px solid ${T.border}`, borderRadius: T.rSm,
                  marginBottom: 14, display: 'flex', alignItems: 'center',
                  justifyContent: 'center', fontSize: 22, fontWeight: 800,
                  color: T.text2, fontFamily: 'Geist, Inter, sans-serif',
                  letterSpacing: '-0.02em' }}>
                  {b.name[0]}
                </div>
                <div style={{ fontSize: 15, fontWeight: 600, color: T.text, marginBottom: 4 }}>{b.name}</div>
                <div style={{ fontSize: 11, color: T.text3, marginBottom: 12 }}>{b.by}</div>
                <div style={{ fontSize: 12, color: T.text2, lineHeight: 1.5, marginBottom: 14 }}>{b.desc}</div>
                <div style={{ display: 'flex', justifyContent: 'space-between',
                  alignItems: 'center', paddingTop: 12, borderTop: `1px solid ${T.border}` }}>
                  <span style={{ fontSize: 12, color: T.text3 }}>{b.count} товарів</span>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke={T.text2} strokeWidth="2">
                    <path d="m9 18 6-6-6-6"/>
                  </svg>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

// ── BRAND PRODUCTS SUBCATEGORY (бренд + підкатегорія) ────────────
function BrandSubcategoryPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const brand = 'TORK';
  const sub   = 'Полотенця для рук';

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Бренди', `Продукція ${brand}`, sub]} setPage={setPage}/>

      {/* Brand + subcategory header */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 28, marginBottom: 24,
        display: 'grid', gridTemplateColumns: '100px 1fr auto', gap: 24,
        alignItems: 'center', boxShadow: T.shadow }}>
        <div style={{ width: 100, height: 70, background: T.bgAlt,
          border: `1px solid ${T.border}`, borderRadius: T.rLg,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          fontSize: 28, fontWeight: 800, color: T.text2,
          fontFamily: 'Geist, Inter, sans-serif', letterSpacing: '-0.04em' }}>
          T
        </div>
        <div>
          <div style={{ fontSize: 11, fontWeight: 600, color: T.text3,
            textTransform: 'uppercase', letterSpacing: '0.08em', marginBottom: 4 }}>
            {brand} · Підкатегорія
          </div>
          <h1 style={{ fontSize: 22, fontWeight: 700, color: T.text,
            letterSpacing: '-0.025em',
            fontFamily: 'Geist, Inter, sans-serif' }}>
            {sub}
          </h1>
          <div style={{ fontSize: 13, color: T.text2, marginTop: 4 }}>
            937 товарів у підкатегорії
          </div>
        </div>
        <div style={{ display: 'flex', gap: 8 }}>
          <Btn variant="outline" size="sm" onClick={() => setPage('brand-products')}>
            ← До бренду
          </Btn>
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20 }}>
        {/* Filters */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, height: 'fit-content', overflow: 'hidden' }}>
          <div style={{ padding: '14px 18px', borderBottom: `1px solid ${T.border}`,
            fontSize: 14, fontWeight: 600, color: T.text }}>Фільтри</div>

          {/* Type */}
          <div style={{ padding: 18, borderBottom: `1px solid ${T.border}` }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 10 }}>
              Тип полотенець
            </div>
            {['В рулонах','У листах','Z-складання','V-складання'].map((t, i) => (
              <div key={t} style={{ display: 'flex', alignItems: 'center', gap: 10,
                padding: '5px 0', cursor: 'pointer', fontSize: 13, color: T.text2 }}>
                <div style={{ width: 16, height: 16, border: `1.5px solid ${T.border2}`,
                  borderRadius: 4, flexShrink: 0 }}/>
                <span>{t}</span>
                <span style={{ marginLeft: 'auto', fontSize: 11, color: T.text3 }}>({120 + i*40})</span>
              </div>
            ))}
          </div>

          {/* Layers */}
          <div style={{ padding: 18 }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 10 }}>
              Кількість шарів
            </div>
            {['1 шар','2 шари','3 шари'].map(l => (
              <div key={l} style={{ display: 'flex', alignItems: 'center', gap: 10,
                padding: '5px 0', cursor: 'pointer', fontSize: 13, color: T.text2 }}>
                <div style={{ width: 16, height: 16, border: `1.5px solid ${T.border2}`,
                  borderRadius: 4, flexShrink: 0 }}/>
                <span>{l}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Products */}
        <div>
          <div style={{ display: 'flex', justifyContent: 'space-between',
            alignItems: 'center', marginBottom: 16 }}>
            <span style={{ fontSize: 14, color: T.text2 }}>
              Показано: <strong style={{ color: T.text }}>{PRODUCTS.length}</strong> з 937 товарів
            </span>
            <select style={{ padding: '7px 10px', border: `1px solid ${T.border2}`,
              borderRadius: T.rSm, fontSize: 13, background: T.surface, color: T.text,
              fontFamily: 'Geist, Inter, sans-serif', cursor: 'pointer' }}>
              <option>Популярні</option>
              <option>Ціна ↑</option>
              <option>Ціна ↓</option>
            </select>
          </div>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16 }}>
            {PRODUCTS.slice(0, 6).map(p => (
              <ProductCard key={p.id} product={p}
                onView={() => setPage('product')} onAdd={addToCart}/>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { ServicesPopupPage, BrandsCategoryPage, BrandSubcategoryPage });
