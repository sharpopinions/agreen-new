// ── EXTRA PAGES 4 ─────────────────────────────────────────────────
// Cart-Empty overlay, Partners Pop-up, Aktsyii (list), Role Selection

// ── CART EMPTY POPUP (overlay) ────────────────────────────────────
// This is shown as an overlay when cart is empty and user tries to open it.
// Triggered as a floating panel over the current page.
// In our prototype, CartPage already handles empty state, so this is
// a standalone empty cart screen with recommended products.

function CartEmptyPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Кошик']} setPage={setPage}/>

      {/* Empty state */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: '60px 40px', textAlign: 'center',
        marginBottom: 40, display: 'flex', flexDirection: 'column',
        alignItems: 'center', gap: 16 }}>
        <div style={{ width: 80, height: 80, background: T.muted, borderRadius: T.rLg,
          display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.2">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
            <line x1="3" y1="6" x2="21" y2="6"/>
            <path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
        </div>
        <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text,
          letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif' }}>
          Ваш кошик порожній
        </h2>
        <p style={{ fontSize: 14, color: T.text2, maxWidth: 380, lineHeight: 1.65 }}>
          Схоже, ви ще не додали жодного товару. Перегляньте каталог або
          скористайтесь пошуком, щоб знайти потрібне.
        </p>
        <div style={{ display: 'flex', gap: 12, marginTop: 8 }}>
          <Btn onClick={() => setPage('catalog')}>До каталогу</Btn>
          <Btn variant="outline" onClick={() => setPage('favorites')}>Обрані товари</Btn>
        </div>
      </div>

      {/* Recommended products */}
      <div>
        <div style={{ fontSize: 18, fontWeight: 700, color: T.text,
          letterSpacing: '-0.025em', marginBottom: 20,
          fontFamily: 'Geist, Inter, sans-serif' }}>
          Можливо вас зацікавить
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4,1fr)', gap: 16 }}>
          {PRODUCTS.slice(0,4).map(p => (
            <ProductCard key={p.id} product={p}
              onView={() => setPage('product')} onAdd={addToCart}/>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── PARTNERS PAGE (with popup CTA) ───────────────────────────────
function PartnersPopupPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [showForm, setShowForm] = useState(false);
  const [partnerType, setPartnerType] = useState('dealer');
  const [form, setForm] = useState({ company:'', name:'', phone:'', email:'' });
  const [sent, setSent] = useState(false);
  const upd = (k,v) => setForm(f => ({...f,[k]:v}));

  const partnerTypes = [
    { key:'dealer', label:'Дилер', desc:'Продаж нашої продукції від свого імені, ексклюзивні умови та маркетингова підтримка' },
    { key:'wholesale', label:'Оптовий покупець', desc:'Знижки від обсягу закупівлі, відстрочка платежу, персональний менеджер' },
    { key:'installer', label:'Партнер по установці', desc:'Технічна підтримка, навчання спеціалістів, пільгові ціни на обладнання' },
  ];

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Партнерам']} setPage={setPage}/>

      {/* About company */}
      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40, marginBottom: 48 }}>
        <div>
          <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text,
            letterSpacing: '-0.025em', marginBottom: 20,
            fontFamily: 'Geist, Inter, sans-serif', lineHeight: 1.25 }}>
            Станьте партнером A-green
          </h1>
          <div style={{ fontSize: 14, color: T.text2, lineHeight: 1.8, marginBottom: 16 }}>
            Компанія A-green — комплексний постачальник професійних матеріалів, обладнання та
            інструментів для малярно-кузовного ремонту для автосервісів, виробничих та ремонтних
            підприємств. Ми є дистрибутором та імпортером світових брендів-лідерів.
          </div>
          <div style={{ fontSize: 14, color: T.text2, lineHeight: 1.8, marginBottom: 24 }}>
            Ми робимо все для забезпечення безперебійної роботи ремонтних та виробничих процесів
            наших партнерів: пошук рішень для виконання задач, оптимізація вартості, навчання
            персоналу. Багаторічний досвід злагодженої команди A-green гарантує впровадження
            найкращого рішення для вашого бізнесу.
          </div>
          <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 12 }}>
            Наші продуктові рішення забезпечують:
          </div>
          {['Економію вартості','Економію часу та енергоресурсів',
            'Стабільність якості та кольору захисного покриття',
            'Використання єдиної лінійки продуктів'].map(item => (
            <div key={item} style={{ display:'flex', alignItems:'center', gap:10,
              padding:'6px 0', fontSize: 13, color: T.text2,
              borderBottom: `1px solid ${T.border}` }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke={T.text} strokeWidth="2.5" style={{ flexShrink:0 }}>
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              {item}
            </div>
          ))}
        </div>
        <div style={{ borderRadius: T.rLg, overflow:'hidden' }}>
          <Img h={420} label="фото — виробництво та команда"/>
        </div>
      </div>

      {/* Partner types */}
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em',
        marginBottom: 20, fontFamily: 'Geist, Inter, sans-serif' }}>
        Типи партнерства
      </h2>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 16, marginBottom: 48 }}>
        {partnerTypes.map(pt => (
          <div key={pt.key} onClick={() => { setPartnerType(pt.key); setShowForm(true); }}
            style={{ background: T.surface, border: `1px solid ${T.border}`,
              borderRadius: T.rLg, padding: 28, cursor: 'pointer',
              transition: 'all 0.2s', boxShadow: T.shadow }}
            onMouseEnter={e => { e.currentTarget.style.borderColor=T.border2; e.currentTarget.style.boxShadow=T.shadowMd; }}
            onMouseLeave={e => { e.currentTarget.style.borderColor=T.border; e.currentTarget.style.boxShadow=T.shadow; }}>
            <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 10 }}>{pt.label}</h3>
            <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.65, marginBottom: 20 }}>{pt.desc}</p>
            <Btn variant="outline" size="sm">Подати заявку →</Btn>
          </div>
        ))}
      </div>

      {/* Brands */}
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text, letterSpacing: '-0.025em',
        marginBottom: 20, fontFamily: 'Geist, Inter, sans-serif' }}>
        Бренди A-green
      </h2>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(5,1fr)', gap: 12, marginBottom: 48 }}>
        {BRANDS.map(b => (
          <div key={b} style={{ background: T.surface, border: `1px solid ${T.border}`,
            borderRadius: T.rLg, padding: '20px 16px', textAlign: 'center',
            cursor: 'pointer', transition: 'all 0.15s', boxShadow: T.shadow }}
            onMouseEnter={e => { e.currentTarget.style.borderColor=T.border2; e.currentTarget.style.boxShadow=T.shadowMd; }}
            onMouseLeave={e => { e.currentTarget.style.borderColor=T.border; e.currentTarget.style.boxShadow=T.shadow; }}>
            <div style={{ width: 48, height: 48, background: T.bgAlt, border: `1px solid ${T.border}`,
              borderRadius: T.rSm, margin: '0 auto 10px', display: 'flex',
              alignItems: 'center', justifyContent: 'center',
              fontSize: 16, fontWeight: 700, color: T.text2 }}>{b[0]}</div>
            <div style={{ fontSize: 12, fontWeight: 600, color: T.text }}>{b}</div>
          </div>
        ))}
      </div>

      {/* Pop-up / Application form */}
      {showForm && (
        <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)',
          zIndex: 8000, display: 'flex', alignItems: 'center', justifyContent: 'center',
          padding: 40 }} onClick={e => { if(e.target===e.currentTarget) setShowForm(false); }}>
          <div style={{ background: T.surface, border: `1px solid ${T.border}`,
            borderRadius: T.rLg, width: 760, overflow: 'hidden',
            boxShadow: '0 24px 64px rgba(0,0,0,0.3)',
            display: 'grid', gridTemplateColumns: '1fr 1fr' }}>
            {/* Left: form */}
            <div style={{ padding: 40 }}>
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 24 }}>
                <h3 style={{ fontSize: 18, fontWeight: 700, color: T.text, letterSpacing: '-0.01em' }}>
                  Заявка на партнерство
                </h3>
                <button onClick={() => setShowForm(false)}
                  style={{ background: 'none', border: 'none', cursor: 'pointer',
                    color: T.text3, padding: 4 }}>
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" strokeWidth="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
                </button>
              </div>

              {/* Type selector */}
              <div style={{ display: 'flex', gap: 6, marginBottom: 20,
                background: T.muted, borderRadius: T.rSm,
                border: `1px solid ${T.border}`, padding: 3 }}>
                {partnerTypes.map(pt => (
                  <button key={pt.key} onClick={() => setPartnerType(pt.key)}
                    style={{ flex: 1, padding: '6px 8px', borderRadius: T.rSm, fontSize: 11,
                      fontWeight: partnerType===pt.key ? 600 : 400,
                      background: partnerType===pt.key ? T.surface : 'transparent',
                      border: partnerType===pt.key ? `1px solid ${T.border}` : '1px solid transparent',
                      color: partnerType===pt.key ? T.text : T.text2, cursor: 'pointer',
                      fontFamily: 'Geist, Inter, sans-serif',
                      boxShadow: partnerType===pt.key ? T.shadow : 'none', transition: 'all 0.12s' }}>
                    {pt.label}
                  </button>
                ))}
              </div>

              {sent ? (
                <div style={{ textAlign: 'center', padding: '32px 16px' }}>
                  <div style={{ fontSize: 40, marginBottom: 16 }}>🎉</div>
                  <h4 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 8 }}>
                    Заявку надіслано!
                  </h4>
                  <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.6 }}>
                    Ми зв'яжемось з вами протягом одного робочого дня.
                  </p>
                  <div style={{ marginTop: 20 }}>
                    <Btn variant="outline" onClick={() => { setShowForm(false); setSent(false); }}>
                      Закрити
                    </Btn>
                  </div>
                </div>
              ) : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                  <Field label="Назва компанії *" placeholder="ТОВ 'Назва'"
                    value={form.company} onChange={e => upd('company',e.target.value)}/>
                  <Field label="Контактна особа *" placeholder="Іван Іваненко"
                    value={form.name} onChange={e => upd('name',e.target.value)}/>
                  <Field label="Телефон *" placeholder="+380 XX XXX XX XX"
                    value={form.phone} onChange={e => upd('phone',e.target.value)}/>
                  <Field label="Email *" placeholder="email@company.ua" type="email"
                    value={form.email} onChange={e => upd('email',e.target.value)}/>
                  <Btn full onClick={() => setSent(true)}>Надіслати заявку</Btn>
                </div>
              )}
            </div>
            {/* Right: info */}
            <div style={{ background: T.bgAlt, borderLeft: `1px solid ${T.border}`,
              padding: 40, display: 'flex', flexDirection: 'column', gap: 16 }}>
              <Img h={160} label="партнер A-green"/>
              <div style={{ fontSize: 15, fontWeight: 700, color: T.text }}>
                {partnerTypes.find(pt=>pt.key===partnerType)?.label}
              </div>
              <div style={{ fontSize: 13, color: T.text2, lineHeight: 1.7 }}>
                {partnerTypes.find(pt=>pt.key===partnerType)?.desc}
              </div>
              {[['Знижки до 25%','від роздрібної ціни'],
                ['Персональний менеджер','завжди на зв\'язку'],
                ['Навчання','за рахунок компанії'],
                ['Маркетингова підтримка','матеріали та акції']].map(([t,s]) => (
                <div key={t} style={{ display: 'flex', gap: 8, alignItems: 'flex-start' }}>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke={T.text} strokeWidth="2.5" style={{ marginTop:2, flexShrink:0 }}>
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                  <div>
                    <span style={{ fontSize: 13, fontWeight: 600, color: T.text }}>{t}</span>
                    <span style={{ fontSize: 13, color: T.text2 }}> — {s}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

// ── AKTSYII (Список акцій) ────────────────────────────────────────
const PROMOS = [
  { id:1, brand:'Tork', title:'-10% на дозуюче обладнання від бренду Tork',       until:'30 травня 2026', cat:'Дозуюче обладнання',    pct:10 },
  { id:2, brand:'Mirka',title:'-15% на абразивні диски Mirka серії Abranet',       until:'15 червня 2026', cat:'Абразивні матеріали',   pct:15 },
  { id:3, brand:'3M',   title:'Безкоштовна доставка від 2 000 ₴ на товари 3M',    until:'31 травня 2026', cat:'Витратні матеріали',    pct:0  },
  { id:4, brand:'DuPont',title:'-20% на захисні комбінезони DuPont Tyvek',        until:'20 травня 2026', cat:'Захисні засоби',        pct:20 },
  { id:5, brand:'Sika', title:'-10% на клеї та герметики Sika при замовленні від 5 шт', until:'30 черв 2026', cat:'Клеї та герметики', pct:10 },
  { id:6, brand:'Dettol',title:'-5% на гігієнічну продукцію Dettol Professional', until:'31 трав 2026', cat:'Гігієнічна продукція',   pct:5  },
];

function AktsyiiPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [selCat, setSelCat] = useState(null);
  const [selBrand, setSelBrand] = useState(null);

  const filtered = PROMOS.filter(p => {
    if (selCat   && p.cat   !== selCat)   return false;
    if (selBrand && p.brand !== selBrand) return false;
    return true;
  });

  const cats   = Array.from(new Set(PROMOS.map(p=>p.cat)));
  const brands = Array.from(new Set(PROMOS.map(p=>p.brand)));

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Акції']} setPage={setPage}/>

      {/* Banner */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 28 }}>
        <Img h={200} label="банер акцій — знижки та спецпропозиції"/>
      </div>

      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'baseline', marginBottom: 8 }}>
        <h1 style={{ fontSize: 24, fontWeight: 700, color: T.text, letterSpacing: '-0.025em',
          fontFamily: 'Geist, Inter, sans-serif' }}>Акції</h1>
        <span style={{ fontSize: 14, color: T.text2 }}>
          Актуальних пропозицій: <strong style={{ color: T.text }}>{filtered.length}</strong>
        </span>
      </div>
      <p style={{ fontSize: 14, color: T.text2, marginBottom: 24, lineHeight: 1.6 }}>
        Спеціальні пропозиції та знижки для наших клієнтів
      </p>

      <div style={{ display: 'grid', gridTemplateColumns: '220px 1fr', gap: 20 }}>
        {/* Sidebar */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, overflow: 'hidden', height: 'fit-content' }}>
          <div style={{ padding: '14px 18px', borderBottom: `1px solid ${T.border}`,
            display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
            <span style={{ fontSize: 14, fontWeight: 600, color: T.text }}>Фільтри</span>
            {(selCat||selBrand) && (
              <span onClick={() => { setSelCat(null); setSelBrand(null); }}
                style={{ fontSize: 12, color: T.text2, cursor: 'pointer',
                  textDecoration: 'underline', textUnderlineOffset: 2 }}>
                Скинути
              </span>
            )}
          </div>

          {/* Category */}
          <div style={{ borderBottom: `1px solid ${T.border}` }}>
            <div style={{ padding: '12px 18px', fontSize: 13, fontWeight: 600, color: T.text }}>
              Категорія
            </div>
            <div style={{ padding: '0 18px 14px' }}>
              {cats.map(c => (
                <div key={c} onClick={() => setSelCat(selCat===c?null:c)}
                  style={{ display:'flex', alignItems:'center', gap:10, padding:'5px 0',
                    cursor:'pointer', fontSize:13, color: selCat===c?T.text:T.text2 }}>
                  <div style={{ width:16, height:16, border:`1.5px solid ${selCat===c?T.invBg:T.border2}`,
                    borderRadius:4, background: selCat===c?T.invBg:'transparent',
                    flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center',
                    transition:'all 0.15s' }}>
                    {selCat===c && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                      <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>}
                  </div>
                  {c}
                </div>
              ))}
            </div>
          </div>

          {/* Brand */}
          <div>
            <div style={{ padding: '12px 18px', fontSize: 13, fontWeight: 600, color: T.text }}>
              Бренд
            </div>
            <div style={{ padding: '0 18px 14px' }}>
              {brands.map(b => (
                <div key={b} onClick={() => setSelBrand(selBrand===b?null:b)}
                  style={{ display:'flex', alignItems:'center', gap:10, padding:'5px 0',
                    cursor:'pointer', fontSize:13, color: selBrand===b?T.text:T.text2 }}>
                  <div style={{ width:16, height:16, border:`1.5px solid ${selBrand===b?T.invBg:T.border2}`,
                    borderRadius:4, background: selBrand===b?T.invBg:'transparent',
                    flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center',
                    transition:'all 0.15s' }}>
                    {selBrand===b && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                      <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>}
                  </div>
                  {b}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Promo list */}
        <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
          {filtered.map(promo => (
            <div key={promo.id} onClick={() => setPage('aktsyia')}
              style={{ background: T.surface, border: `1px solid ${T.border}`,
                borderRadius: T.rLg, overflow: 'hidden', cursor: 'pointer',
                transition: 'all 0.2s', boxShadow: T.shadow,
                display: 'grid', gridTemplateColumns: '200px 1fr auto' }}
              onMouseEnter={e => { e.currentTarget.style.borderColor=T.border2; e.currentTarget.style.boxShadow=T.shadowMd; }}
              onMouseLeave={e => { e.currentTarget.style.borderColor=T.border; e.currentTarget.style.boxShadow=T.shadow; }}>
              {/* Brand logo */}
              <div style={{ background: T.bgAlt, display: 'flex', flexDirection: 'column',
                alignItems: 'center', justifyContent: 'center', gap: 8, padding: 24,
                borderRight: `1px solid ${T.border}` }}>
                <div style={{ width: 56, height: 56, background: T.muted,
                  border: `1px solid ${T.border}`, borderRadius: T.rSm,
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: 18, fontWeight: 700, color: T.text2 }}>
                  {promo.brand[0]}
                </div>
                <div style={{ fontSize: 12, fontWeight: 600, color: T.text }}>{promo.brand}</div>
              </div>

              {/* Info */}
              <div style={{ padding: '20px 24px', display: 'flex', flexDirection: 'column',
                justifyContent: 'center', gap: 8 }}>
                <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
                  {promo.pct > 0 && (
                    <span style={{ padding: '3px 10px', background: T.bg==='#09090b'?'#450a0a':'#fef2f2',
                      border: `1px solid ${T.bg==='#09090b'?'#7f1d1d':'#fecaca'}`,
                      borderRadius: T.rPill, fontSize: 12, fontWeight: 600, color: '#dc2626' }}>
                      −{promo.pct}%
                    </span>
                  )}
                  <span style={{ padding: '3px 10px', background: T.muted,
                    border: `1px solid ${T.border}`, borderRadius: T.rPill,
                    fontSize: 11, fontWeight: 500, color: T.text3 }}>
                    {promo.cat}
                  </span>
                </div>
                <div style={{ fontSize: 15, fontWeight: 600, color: T.text, lineHeight: 1.4 }}>
                  {promo.title}
                </div>
                <div style={{ fontSize: 12, color: T.text3 }}>
                  До {promo.until}
                </div>
              </div>

              {/* CTA */}
              <div style={{ padding: '20px 24px', display: 'flex', alignItems: 'center' }}>
                <Btn size="sm" onClick={e => { e.stopPropagation(); setPage('aktsyia'); }}>
                  Переглянути →
                </Btn>
              </div>
            </div>
          ))}

          {filtered.length === 0 && (
            <div style={{ textAlign: 'center', padding: '60px 20px', color: T.text2 }}>
              <div style={{ fontSize: 14, marginBottom: 8 }}>Акцій за вибраними фільтрами не знайдено</div>
              <Btn variant="outline" size="sm" onClick={() => { setSelCat(null); setSelBrand(null); }}>
                Скинути фільтри
              </Btn>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

// ── ROLE SELECTION ────────────────────────────────────────────────
function RoleSelectionPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [selected, setSelected] = useState('client');

  const roles = [
    {
      key: 'client',
      title: 'Клієнт',
      desc: 'Без укладення договору',
      features: ['Перегляд історії замовлень','Підключення до системи бонусів та знижок','Швидке оформлення замовлень'],
      highlight: true,
    },
    {
      key: 'business',
      title: 'Бізнес-клієнт',
      desc: 'З укладенням договору',
      features: ['Партнерські ціни зі знижками до 25%','Персональний менеджер','Відстрочка платежу до 30 днів','Доступ до документообігу онлайн'],
      highlight: false,
    },
    {
      key: 'dealer',
      title: 'Дилер / Дропшипер',
      desc: 'Для реселерів та дропшиперів',
      features: ['Ексклюзивні умови для реселерів','Дропшипінг — відправка від A-green','Маркетингова підтримка','Навчання та сертифікація'],
      highlight: false,
    },
  ];

  return (
    <div style={{ background: T.bg, minHeight: '80vh', display: 'flex',
      flexDirection: 'column', alignItems: 'center', justifyContent: 'center',
      padding: '60px 60px' }}>

      {/* Success header */}
      <div style={{ textAlign: 'center', marginBottom: 48 }}>
        <div style={{ width: 56, height: 56, background: T.bg==='#09090b'?'#052e16':'#f0fdf4',
          border: `1px solid ${T.bg==='#09090b'?'#14532d':'#bbf7d0'}`,
          borderRadius: T.rLg, display: 'flex', alignItems: 'center',
          justifyContent: 'center', margin: '0 auto 20px' }}>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="#16a34a" strokeWidth="2.5">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text,
          letterSpacing: '-0.025em', marginBottom: 8,
          fontFamily: 'Geist, Inter, sans-serif' }}>
          Дякуємо, ваш акаунт активовано!
        </h1>
        <p style={{ fontSize: 15, color: T.text2 }}>
          Оберіть тип персонального кабінету
        </p>
      </div>

      {/* Role cards */}
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: 16,
        width: '100%', maxWidth: 960 }}>
        {roles.map(role => (
          <div key={role.key} onClick={() => setSelected(role.key)}
            style={{ background: T.surface,
              border: `2px solid ${selected===role.key ? T.invBg : T.border}`,
              borderRadius: T.rLg, padding: 28, cursor: 'pointer',
              transition: 'all 0.2s', boxShadow: selected===role.key ? T.shadowMd : T.shadow }}>
            {/* Radio indicator */}
            <div style={{ display: 'flex', justifyContent: 'space-between',
              alignItems: 'flex-start', marginBottom: 16 }}>
              <div>
                <div style={{ fontSize: 16, fontWeight: 700, color: T.text, marginBottom: 4 }}>
                  {role.title}
                </div>
                <div style={{ fontSize: 12, color: T.text3 }}>{role.desc}</div>
              </div>
              <div style={{ width: 20, height: 20, border: `2px solid ${selected===role.key?T.invBg:T.border2}`,
                borderRadius: '50%', flexShrink: 0, display: 'flex',
                alignItems: 'center', justifyContent: 'center', transition: 'all 0.15s' }}>
                {selected===role.key && (
                  <div style={{ width: 8, height: 8, borderRadius: '50%', background: T.invBg }}/>
                )}
              </div>
            </div>

            {/* Features */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
              {role.features.map(f => (
                <div key={f} style={{ display: 'flex', gap: 8, alignItems: 'flex-start' }}>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                    stroke={selected===role.key ? T.text : T.text3}
                    strokeWidth="2" style={{ marginTop: 2, flexShrink: 0, transition: 'stroke 0.15s' }}>
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                  <span style={{ fontSize: 13, color: T.text2, lineHeight: 1.45 }}>{f}</span>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* Continue */}
      <div style={{ marginTop: 32, display: 'flex', gap: 12 }}>
        <Btn size="lg" onClick={() => setPage(selected==='client' ? 'main' : 'ext-dashboard')}>
          Продовжити як {roles.find(r=>r.key===selected)?.title}
        </Btn>
        <Btn variant="ghost" size="lg" onClick={() => setPage('main')}>
          Пропустити
        </Btn>
      </div>
    </div>
  );
}

Object.assign(window, {
  CartEmptyPage, PartnersPopupPage, AktsyiiPage, RoleSelectionPage,
});
