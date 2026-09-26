// ── EXTRA PAGES 7 ─────────────────────────────────────────────────
// Catalog-Subcategory · Services-Study-Center

// ── CATALOG SUBCATEGORY ──────────────────────────────────────────
// Сторінка підкатегорії: хлібні крихти "Каталог – Категорія – Підкатегорія",
// сітка товарів з пагінацією, SEO-текст знизу
function CatalogSubcategoryPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const cat = CATEGORIES[2]; // Дозуюче обладнання
  const subName = 'Для рушників';
  const [view, setView]   = useState('grid');
  const [page_, setPage_] = useState(1);
  const [priceFrom, setPriceFrom] = useState('');
  const [priceTo, setPriceTo]     = useState('');
  const totalPages = 100;

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Каталог', cat.name, subName]} setPage={setPage}/>

      <div style={{ display:'flex', justifyContent:'space-between',
        alignItems:'flex-end', marginBottom:24 }}>
        <div>
          <h1 style={{ fontSize: 26, fontWeight: 700, color: T.text,
            letterSpacing: '-0.025em', fontFamily: 'Geist, Inter, sans-serif',
            marginBottom: 6 }}>{subName}</h1>
          <p style={{ fontSize: 13, color: T.text2 }}>
            {cat.name} · {PRODUCTS.length * 24} товарів
          </p>
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20 }}>
        {/* Filter sidebar */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, height: 'fit-content', overflow: 'hidden' }}>
          <div style={{ padding: '14px 18px', borderBottom: `1px solid ${T.border}`,
            display:'flex', justifyContent:'space-between', alignItems:'center' }}>
            <span style={{ fontSize: 14, fontWeight: 600, color: T.text }}>Фільтри</span>
            <span style={{ fontSize:11, color:T.text3, cursor:'pointer',
              textDecoration:'underline', textUnderlineOffset:2 }}>Очистити</span>
          </div>

          {/* Price */}
          <div style={{ padding:18, borderBottom:`1px solid ${T.border}` }}>
            <div style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:10 }}>Ціна, ₴</div>
            <div style={{ display:'flex', gap:6, alignItems:'center', marginBottom:12 }}>
              <input value={priceFrom} onChange={e=>setPriceFrom(e.target.value)} placeholder="Від"
                style={{ flex:1, padding:'7px 10px', border:`1px solid ${T.border2}`,
                  borderRadius:T.rSm, fontSize:13, background:T.bg, color:T.text,
                  fontFamily:'Geist, Inter, sans-serif', outline:'none', minWidth:0 }}/>
              <span style={{ color:T.text3 }}>—</span>
              <input value={priceTo} onChange={e=>setPriceTo(e.target.value)} placeholder="До"
                style={{ flex:1, padding:'7px 10px', border:`1px solid ${T.border2}`,
                  borderRadius:T.rSm, fontSize:13, background:T.bg, color:T.text,
                  fontFamily:'Geist, Inter, sans-serif', outline:'none', minWidth:0 }}/>
            </div>
            <div style={{ height: 4, background: T.bgAlt, borderRadius: T.rPill, position:'relative' }}>
              <div style={{ position:'absolute', left:'15%', right:'30%', height:'100%',
                background: T.invBg, borderRadius: T.rPill }}/>
              <div style={{ position:'absolute', left:'15%', top:-4, width:12, height:12,
                background:T.surface, border:`2px solid ${T.invBg}`, borderRadius:'50%' }}/>
              <div style={{ position:'absolute', left:'70%', top:-4, width:12, height:12,
                background:T.surface, border:`2px solid ${T.invBg}`, borderRadius:'50%' }}/>
            </div>
          </div>

          {/* Brands */}
          <div style={{ padding:18, borderBottom:`1px solid ${T.border}` }}>
            <div style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:10 }}>Бренд</div>
            {BRANDS.slice(0,5).map((b, i) => (
              <div key={b} style={{ display:'flex', alignItems:'center', gap:10, padding:'5px 0',
                cursor:'pointer', fontSize:13, color: i===0 ? T.text : T.text2 }}>
                <div style={{ width:16, height:16, border:`1.5px solid ${i===0?T.invBg:T.border2}`,
                  borderRadius:4, background: i===0?T.invBg:'transparent',
                  flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center' }}>
                  {i===0 && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                    <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                  </svg>}
                </div>
                <span>{b}</span>
              </div>
            ))}
          </div>

          {/* Availability */}
          <div style={{ padding:18 }}>
            <div style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:10 }}>Наявність</div>
            {['В наявності', 'Під замовлення', 'Акційні'].map((l, i) => (
              <div key={l} style={{ display:'flex', alignItems:'center', gap:10, padding:'5px 0',
                cursor:'pointer', fontSize:13, color: i===0 ? T.text : T.text2 }}>
                <div style={{ width:16, height:16, border:`1.5px solid ${i===0?T.invBg:T.border2}`,
                  borderRadius:4, background: i===0?T.invBg:'transparent', flexShrink:0,
                  display:'flex', alignItems:'center', justifyContent:'center' }}>
                  {i===0 && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                    <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                  </svg>}
                </div>
                <span>{l}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Grid */}
        <div>
          {/* Toolbar */}
          <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center',
            marginBottom: 16 }}>
            <span style={{ fontSize:14, color:T.text2 }}>
              Показано <strong style={{ color:T.text }}>1–24</strong> з{' '}
              <strong style={{ color:T.text }}>{PRODUCTS.length * 24}</strong>
            </span>
            <div style={{ display:'flex', gap:8 }}>
              <select style={{ padding:'7px 10px', border:`1px solid ${T.border2}`,
                borderRadius:T.rSm, fontSize:13, background:T.surface, color:T.text,
                fontFamily:'Geist, Inter, sans-serif', cursor:'pointer' }}>
                <option>Популярні</option>
                <option>Ціна ↑</option>
                <option>Ціна ↓</option>
                <option>Новинки</option>
              </select>
              <div style={{ display:'flex', border:`1px solid ${T.border}`, borderRadius:T.rSm,
                overflow:'hidden' }}>
                {['grid','list'].map(v => (
                  <button key={v} onClick={()=>setView(v)}
                    style={{ padding:'7px 10px', border:'none', cursor:'pointer',
                      background: view===v ? T.invBg : T.surface,
                      color: view===v ? T.invText : T.text2 }}>
                    {v==='grid'
                      ? <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                      : <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {/* Grid */}
          <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:16 }}>
            {PRODUCTS.map(p => (
              <ProductCard key={p.id} product={p}
                onView={()=>setPage('product')} onAdd={addToCart}/>
            ))}
          </div>

          {/* Pagination */}
          <div style={{ display:'flex', justifyContent:'center', alignItems:'center',
            gap:6, marginTop:32 }}>
            <button onClick={()=>setPage_(Math.max(1, page_-1))}
              style={{ width:36, height:36, border:`1px solid ${T.border2}`,
                background:T.surface, color:T.text2, borderRadius:T.rSm, cursor:'pointer',
                display:'flex', alignItems:'center', justifyContent:'center' }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            {[1, 2, 3].map(n => (
              <button key={n} onClick={()=>setPage_(n)}
                style={{ width:36, height:36, border:`1px solid ${page_===n ? T.invBg : T.border2}`,
                  background: page_===n ? T.invBg : T.surface,
                  color: page_===n ? T.invText : T.text, fontWeight: page_===n ? 600 : 400,
                  borderRadius:T.rSm, cursor:'pointer', fontFamily:'Geist, Inter, sans-serif',
                  fontSize:13 }}>{n}</button>
            ))}
            <span style={{ color:T.text3, padding:'0 4px' }}>...</span>
            <button onClick={()=>setPage_(totalPages)}
              style={{ width:36, height:36, border:`1px solid ${T.border2}`,
                background: T.surface, color: T.text,
                borderRadius:T.rSm, cursor:'pointer', fontFamily:'Geist, Inter, sans-serif',
                fontSize:13 }}>{totalPages}</button>
            <button onClick={()=>setPage_(Math.min(totalPages, page_+1))}
              style={{ width:36, height:36, border:`1px solid ${T.border2}`,
                background:T.surface, color:T.text2, borderRadius:T.rSm, cursor:'pointer',
                display:'flex', alignItems:'center', justifyContent:'center' }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>

      {/* SEO text */}
      <div style={{ marginTop:60, paddingTop:32, borderTop:`1px solid ${T.border}` }}>
        <h2 style={{ fontSize:18, fontWeight:600, color:T.text,
          letterSpacing:'-0.02em', marginBottom:14,
          fontFamily:'Geist, Inter, sans-serif' }}>Про підкатегорію «{subName}»</h2>
        <div style={{ fontSize:14, color:T.text2, lineHeight:1.75, maxWidth:920 }}>
          <p style={{ marginBottom:14 }}>
            Сенсорні диспенсери для паперових рушників — це сучасні безконтактні системи,
            які забезпечують ефективну гігієну у громадських приміщеннях. Наша лінійка
            включає моделі провідних світових виробників: Tork, Kimberly-Clark, JOFEL.
          </p>
          <p style={{ marginBottom:14 }}>
            Диспенсери для рушників підходять для встановлення в офісах, торгових центрах,
            закладах громадського харчування, медичних установах та виробничих приміщеннях.
            Виберіть оптимальне рішення для вашого бізнесу — наші менеджери допоможуть з підбором.
          </p>
        </div>
      </div>
    </div>
  );
}

// ── STUDY CENTER ─────────────────────────────────────────────────
// Сторінка навчального центру: банер, опис, програма, переваги, форма
function StudyCenterPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [form, setForm] = useState({ name:'', phone:'', company:'', course:'' });
  const upd = (k,v) => setForm(f => ({...f, [k]:v}));
  const [sent, setSent] = useState(false);

  const courses = [
    { id:1, title:'Базовий курс малярно-кузовного ремонту', dur:'40 годин', level:'Початковий', price:'Безкоштовно' },
    { id:2, title:'Робота з лакофарбовими матеріалами Cromax', dur:'24 години', level:'Середній', price:'4 800 ₴' },
    { id:3, title:'Підбір та змішування кольору', dur:'16 годин', level:'Середній', price:'3 200 ₴' },
    { id:4, title:'Експертний рівень: підготовка поверхонь', dur:'32 години', level:'Експертний', price:'6 400 ₴' },
    { id:5, title:'Технологія полірування Final Finish', dur:'8 годин', level:'Початковий', price:'1 600 ₴' },
    { id:6, title:'Сертифікація майстра з ремонту', dur:'80 годин', level:'Експертний', price:'16 000 ₴' },
  ];

  const benefits = [
    { icon:'👨‍🏫', title:'Викладачі-практики', desc:'Експерти з 15+ років досвіду в галузі' },
    { icon:'🛠️', title:'Реальне обладнання', desc:'Тренування на сучасному професійному обладнанні' },
    { icon:'📜', title:'Сертифікація', desc:'Видаємо сертифікати міжнародного зразка' },
    { icon:'💼', title:'Працевлаштування', desc:'Допомагаємо з пошуком роботи після випуску' },
  ];

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Послуги','Навчальний центр']} setPage={setPage}/>

      {/* Hero banner */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 48, marginBottom: 48, boxShadow: T.shadow,
        display: 'grid', gridTemplateColumns: '1fr 320px', gap: 40, alignItems: 'center' }}>
        <div>
          <span style={{ display:'inline-block', padding:'4px 12px',
            background: T.muted, border: `1px solid ${T.border}`, borderRadius: T.rPill,
            fontSize: 11, fontWeight: 600, color: T.text3, marginBottom: 14,
            textTransform: 'uppercase', letterSpacing: '0.08em' }}>
            Послуги · Навчальний центр
          </span>
          <h1 style={{ fontSize: 32, fontWeight: 700, color: T.text,
            letterSpacing: '-0.03em', marginBottom: 14, lineHeight: 1.2,
            fontFamily: 'Geist, Inter, sans-serif' }}>
            Навчальний центр для<br/>професіоналів вашої галузі
          </h1>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.7, marginBottom: 20, maxWidth: 560 }}>
            Компанія A-green пропонує практичні тренінги, консультації та експертний супровід
            для підприємств малярно-кузовного ремонту, виробництв та гігієнічних об'єктів.
          </p>
          <div style={{ display: 'flex', gap: 12 }}>
            <Btn onClick={() => setPage('contacts')}>Записатись на курс</Btn>
            <Btn variant="outline" onClick={() => setPage('contacts')}>Скачати програму</Btn>
          </div>
        </div>
        <div style={{ background: T.bgAlt, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, padding: 24, display:'flex', flexDirection:'column', gap:14 }}>
          {[
            ['500+', 'випускників щороку'],
            ['12', 'програм навчання'],
            ['98%', 'успішне працевлаштування'],
            ['15+', 'років досвіду'],
          ].map(([num, label]) => (
            <div key={num} style={{ display:'flex', justifyContent:'space-between',
              alignItems:'center', paddingBottom: 12, borderBottom:`1px solid ${T.border}` }}>
              <span style={{ fontSize:13, color:T.text2 }}>{label}</span>
              <span style={{ fontSize:20, fontWeight:700, color:T.text,
                letterSpacing:'-0.025em', fontFamily:'Geist, Inter, sans-serif' }}>{num}</span>
            </div>
          ))}
        </div>
      </div>

      {/* Benefits */}
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text,
        letterSpacing: '-0.025em', marginBottom: 20,
        fontFamily: 'Geist, Inter, sans-serif' }}>Чому обирають нас</h2>
      <div style={{ display:'grid', gridTemplateColumns:'repeat(4, 1fr)', gap:16, marginBottom:48 }}>
        {benefits.map(b => (
          <div key={b.title} style={{ background: T.surface, border: `1px solid ${T.border}`,
            borderRadius: T.rLg, padding: 24, boxShadow: T.shadow }}>
            <div style={{ fontSize: 28, marginBottom: 12 }}>{b.icon}</div>
            <h3 style={{ fontSize: 15, fontWeight: 600, color: T.text, marginBottom: 6 }}>{b.title}</h3>
            <p style={{ fontSize: 12, color: T.text2, lineHeight: 1.6 }}>{b.desc}</p>
          </div>
        ))}
      </div>

      {/* Courses */}
      <h2 style={{ fontSize: 22, fontWeight: 700, color: T.text,
        letterSpacing: '-0.025em', marginBottom: 20,
        fontFamily: 'Geist, Inter, sans-serif' }}>Програми навчання</h2>
      <div style={{ background: T.surface, border:`1px solid ${T.border}`,
        borderRadius: T.rLg, overflow:'hidden', marginBottom: 48 }}>
        {courses.map((c, i) => (
          <div key={c.id} style={{ padding:'18px 24px',
            borderBottom: i < courses.length - 1 ? `1px solid ${T.border}` : 'none',
            display:'grid', gridTemplateColumns:'1fr auto auto auto auto', gap:24, alignItems:'center' }}
            onMouseEnter={e=>e.currentTarget.style.background=T.muted}
            onMouseLeave={e=>e.currentTarget.style.background='transparent'}>
            <div>
              <div style={{ fontSize: 11, color: T.text3, marginBottom: 4,
                textTransform:'uppercase', letterSpacing:'0.06em' }}>Курс №{c.id}</div>
              <div style={{ fontSize: 14, fontWeight: 600, color: T.text }}>{c.title}</div>
            </div>
            <div style={{ fontSize: 12, color: T.text2 }}>{c.dur}</div>
            <span style={{ padding:'3px 10px', background: T.bgAlt,
              border:`1px solid ${T.border}`, borderRadius: T.rPill,
              fontSize: 11, fontWeight: 500, color: T.text2 }}>{c.level}</span>
            <div style={{ fontSize: 14, fontWeight: 700, color: T.text,
              fontFamily:'Geist, Inter, sans-serif', minWidth:100, textAlign:'right' }}>{c.price}</div>
            <Btn size="sm" variant="outline" onClick={()=>upd('course', c.title)}>Записатись</Btn>
          </div>
        ))}
      </div>

      {/* Application form */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 40, marginBottom: 60,
        display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 40 }}>
        <div>
          <h2 style={{ fontSize: 24, fontWeight: 700, color: T.text,
            letterSpacing: '-0.025em', marginBottom: 14,
            fontFamily: 'Geist, Inter, sans-serif' }}>Записатись на навчання</h2>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.7, marginBottom: 20 }}>
            Залиште заявку і наш менеджер зв'яжеться з вами протягом одного робочого дня,
            щоб обговорити деталі та підібрати оптимальну програму.
          </p>
          <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
            {['Підбір індивідуальної програми','Гнучкий графік занять','Знижка 10% для груп від 3 осіб','Можлива оплата частинами'].map(t => (
              <div key={t} style={{ display:'flex', alignItems:'center', gap:10, fontSize:13, color:T.text2 }}>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text}
                  strokeWidth="2.5" style={{ flexShrink:0 }}>
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                {t}
              </div>
            ))}
          </div>
        </div>
        <div>
          {sent ? (
            <div style={{ display:'flex', flexDirection:'column', alignItems:'center',
              justifyContent:'center', textAlign:'center', height:'100%', gap:12 }}>
              <div style={{ fontSize: 40 }}>📚</div>
              <h3 style={{ fontSize:16, fontWeight:600, color:T.text }}>Заявку отримано!</h3>
              <p style={{ fontSize:13, color:T.text2, lineHeight:1.6 }}>
                Менеджер навчального центру зателефонує вам протягом години.
              </p>
            </div>
          ) : (
            <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
              <Field label="Ім'я *" placeholder="Іван Іваненко" value={form.name}
                onChange={e => upd('name', e.target.value)}/>
              <Field label="Телефон *" placeholder="+380 XX XXX XX XX" value={form.phone}
                onChange={e => upd('phone', e.target.value)}/>
              <Field label="Компанія" placeholder="Назва компанії (необов'язково)" value={form.company}
                onChange={e => upd('company', e.target.value)}/>
              <Field label="Курс" placeholder="Який курс цікавить?" value={form.course}
                onChange={e => upd('course', e.target.value)}/>
              <Btn onClick={() => setSent(true)}>Надіслати заявку</Btn>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { CatalogSubcategoryPage, StudyCenterPage });
