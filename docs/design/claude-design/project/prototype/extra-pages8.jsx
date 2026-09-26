// ── EXTRA PAGES 8 ─────────────────────────────────────────────────
// Brand-About + Brand-Media (об'єднано в BrandDetail з табами)
// Catalog-category (окрема сторінка категорії)

// ── BRAND DETAIL (About / Media tabs) ─────────────────────────────
function BrandDetailPage({ setPage, defaultTab = 'about' }) {
  const { T } = useContext(ThemeCtx);
  const [tab, setTab] = useState(defaultTab);
  const brand = 'Tork';

  const tabs = [
    { key:'products', label:'Продукція',     count: 1483 },
    { key:'about',    label:'Про бренд' },
    { key:'media',    label:'Медіабанк' },
  ];

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Бренди', `Продукція ${brand}`,
        tab==='about' ? 'Про бренд' : tab==='media' ? 'Медіабанк' : '']
        .filter(Boolean)} setPage={setPage}/>

      {/* Brand header (shared) */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding:28, marginBottom:24,
        display:'grid', gridTemplateColumns:'160px 1fr auto', gap:32, alignItems:'center',
        boxShadow: T.shadow }}>
        <div style={{ width:160, height:108, background: T.bgAlt,
          border:`1px solid ${T.border}`, borderRadius: T.rLg,
          display:'flex', alignItems:'center', justifyContent:'center',
          fontSize:48, fontWeight:800, color:T.text2,
          letterSpacing:'-0.04em', fontFamily:'Geist, Inter, sans-serif' }}>
          T
        </div>
        <div>
          <h1 style={{ fontSize:24, fontWeight:700, color:T.text,
            letterSpacing:'-0.025em', marginBottom:8,
            fontFamily:'Geist, Inter, sans-serif' }}>
            {brand}
          </h1>
          <p style={{ fontSize:13, color:T.text2, lineHeight:1.65, maxWidth:640 }}>
            {brand} — бренд компанії Essity, яка посідає чільне місце у світі серед виробників
            медико-гігієнічної продукції. Щодня виробами, комплексами й послугами користується
            мільярд людей з усього світу.
          </p>
        </div>
        <Btn variant="outline" size="sm" onClick={()=>setPage('partners-popup')}>
          Стати дилером →
        </Btn>
      </div>

      {/* Tabs */}
      <div style={{ borderBottom: `1px solid ${T.border}`, marginBottom: 32, display: 'flex', gap: 2 }}>
        {tabs.map(({key, label, count}) => (
          <button key={key} onClick={() => key==='products' ? setPage('brand-products') : setTab(key)}
            style={{ padding: '10px 18px', background: 'none', border: 'none', cursor: 'pointer',
              fontSize: 14, fontFamily: 'Geist, Inter, sans-serif',
              fontWeight: tab === key ? 600 : 400,
              color: tab === key ? T.text : T.text2,
              borderBottom: `2px solid ${tab === key ? T.text : 'transparent'}`,
              marginBottom: -1, transition: 'all 0.15s',
              display:'flex', alignItems:'center', gap:8 }}>
            {label}
            {count && (
              <span style={{ padding:'1px 8px', background: T.muted,
                border:`1px solid ${T.border}`, borderRadius: T.rPill,
                fontSize: 11, fontWeight: 500, color: T.text3 }}>
                {count}
              </span>
            )}
          </button>
        ))}
      </div>

      {/* About tab */}
      {tab === 'about' && (
        <div style={{ display:'grid', gridTemplateColumns:'1fr 360px', gap:32, marginBottom:60 }}>
          <div>
            <h2 style={{ fontSize:18, fontWeight:600, color:T.text,
              letterSpacing:'-0.01em', marginBottom:16 }}>Про компанію Essity</h2>
            {[
              `Tork — це провідний бренд професійної гігієни компанії Essity. Створений у Швеції,
               Tork є експертом у сфері гігієнічних рішень для роботи та громадських місць.`,
              `Лінійка продукції охоплює диспенсери для рушників, паперові серветки, мило та
               антисептики, туалетний папір — все для забезпечення стандартів чистоти та гігієни.`,
              `Tork представлений у більш ніж 110 країнах світу. Компанія активно інвестує
               у розробку інноваційних рішень, які поєднують ефективність, екологічність та зручність.`,
            ].map((t, i) => (
              <p key={i} style={{ fontSize:14, color:T.text2, lineHeight:1.8, marginBottom:14 }}>{t}</p>
            ))}

            {/* Other brands */}
            <h3 style={{ fontSize:16, fontWeight:600, color:T.text,
              letterSpacing:'-0.01em', marginTop:32, marginBottom:14 }}>
              Інші бренди компанії
            </h3>
            <div style={{ display:'flex', gap:10, flexWrap:'wrap' }}>
              {['SCA','Tork','Zewa','Velvet','Tena','Libresse'].map(b=>(
                <div key={b} style={{ padding:'10px 18px', background:T.surface,
                  border:`1px solid ${T.border}`, borderRadius:T.rSm,
                  fontSize:13, color:T.text, cursor:'pointer',
                  transition:'border-color 0.12s' }}
                  onMouseEnter={e=>e.currentTarget.style.borderColor=T.border2}
                  onMouseLeave={e=>e.currentTarget.style.borderColor=T.border}>
                  {b}
                </div>
              ))}
            </div>
          </div>

          {/* Stats */}
          <div style={{ display:'flex', flexDirection:'column', gap:12 }}>
            {[
              ['110+', 'країн присутності'],
              ['1+ млрд', 'споживачів щодня'],
              ['1929', 'рік заснування'],
              ['48 000+', 'співробітників'],
              ['#1', 'у Європі за гігієною'],
            ].map(([num, label]) => (
              <div key={label} style={{ background: T.surface, border: `1px solid ${T.border}`,
                borderRadius: T.rLg, padding:'14px 18px', display:'flex',
                justifyContent:'space-between', alignItems:'center' }}>
                <span style={{ fontSize:13, color: T.text2 }}>{label}</span>
                <span style={{ fontSize:18, fontWeight:700, color:T.text,
                  letterSpacing:'-0.02em', fontFamily:'Geist, Inter, sans-serif' }}>
                  {num}
                </span>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Media tab */}
      {tab === 'media' && (
        <div style={{ marginBottom: 60 }}>
          {/* Video player */}
          <div style={{ borderRadius: T.rLg, overflow:'hidden', marginBottom:24,
            position:'relative', background: T.bgAlt, border:`1px solid ${T.border}` }}>
            <Img h={400} label="Корпоративне відео Tork — Про компанію"/>
            <button style={{ position:'absolute', top:'50%', left:'50%',
              transform:'translate(-50%, -50%)', width:64, height:64, borderRadius:'50%',
              background: 'rgba(0,0,0,0.6)', border:'none', cursor:'pointer',
              display:'flex', alignItems:'center', justifyContent:'center', backdropFilter:'blur(4px)' }}>
              <svg width="22" height="22" viewBox="0 0 24 24" fill="#fff">
                <path d="M8 5v14l11-7L8 5z"/>
              </svg>
            </button>
          </div>

          {/* Media grid */}
          <h3 style={{ fontSize:16, fontWeight:600, color:T.text,
            letterSpacing:'-0.01em', marginBottom:14 }}>
            Медіа-матеріали ({16})
          </h3>
          <div style={{ display:'grid', gridTemplateColumns:'repeat(4, 1fr)', gap:14, marginBottom:32 }}>
            {Array.from({length: 8}).map((_, i) => (
              <div key={i} style={{ background:T.surface, border:`1px solid ${T.border}`,
                borderRadius:T.rLg, overflow:'hidden', cursor:'pointer',
                transition:'all 0.15s' }}
                onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.boxShadow=T.shadowMd;}}
                onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.boxShadow='none';}}>
                <Img h={140} label={i % 2 === 0 ? `Фото ${i+1}` : `Постер ${i+1}`}/>
                <div style={{ padding:'12px 14px' }}>
                  <div style={{ fontSize:13, fontWeight:500, color:T.text, marginBottom:4 }}>
                    {i % 2 === 0 ? `Brand book ${i+1}` : `Product poster ${i+1}`}
                  </div>
                  <div style={{ fontSize:11, color:T.text3, display:'flex',
                    justifyContent:'space-between', alignItems:'center' }}>
                    <span>{i % 2 === 0 ? 'PDF · 3.2 MB' : 'JPEG · 1.8 MB'}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" strokeWidth="2">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                    </svg>
                  </div>
                </div>
              </div>
            ))}
          </div>

          {/* Print materials */}
          <h3 style={{ fontSize:16, fontWeight:600, color:T.text,
            letterSpacing:'-0.01em', marginBottom:14 }}>
            Друковані матеріали (8)
          </h3>
          <div style={{ background:T.surface, border:`1px solid ${T.border}`,
            borderRadius:T.rLg, overflow:'hidden' }}>
            {[
              ['Каталог продукції Tork 2026',     'PDF · 12.4 MB', '01 квіт 2026'],
              ['Прайс-лист (квітень 2026)',       'PDF · 2.1 MB',  '01 квіт 2026'],
              ['Технічна документація диспенсерів','PDF · 5.8 MB',  '15 бер 2026'],
              ['Інструкція з монтажу',            'PDF · 3.2 MB',  '01 бер 2026'],
              ['Сертифікати якості',              'ZIP · 4.5 MB',  '15 лют 2026'],
            ].map((row, i, arr) => (
              <div key={row[0]} style={{ padding:'14px 20px',
                borderBottom: i < arr.length-1 ? `1px solid ${T.border}` : 'none',
                display:'grid', gridTemplateColumns:'auto 1fr auto auto',
                gap:16, alignItems:'center' }}>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                  stroke={T.text2} strokeWidth="1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
                <div>
                  <div style={{ fontSize:14, fontWeight:500, color:T.text }}>{row[0]}</div>
                  <div style={{ fontSize:11, color:T.text3, marginTop:2 }}>{row[1]} · {row[2]}</div>
                </div>
                <Btn size="sm" variant="outline">Завантажити</Btn>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}

// ── CATALOG CATEGORY (окрема сторінка категорії) ─────────────────
function CatalogCategoryPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const cat = CATEGORIES[2]; // Дозуюче обладнання

  const subcategories = [
    { id:1, name:'Диспенсери для рушників',   count: 312, img:'🧻' },
    { id:2, name:'Диспенсери для мила',       count: 184, img:'🧼' },
    { id:3, name:'Диспенсери для серветок',   count: 156, img:'🧻' },
    { id:4, name:'Тримачі для туалетного паперу', count: 98, img:'🧻' },
    { id:5, name:'Освіжувачі повітря',         count: 64, img:'🌸' },
    { id:6, name:'Покриття на унітаз',         count: 42, img:'🪑' },
  ];

  const popularProducts = PRODUCTS.slice(0, 4);

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Каталог', cat.name]} setPage={setPage}/>

      {/* Hero */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 32, marginBottom: 32, boxShadow: T.shadow,
        display:'grid', gridTemplateColumns: '1fr auto', gap: 32, alignItems: 'center' }}>
        <div>
          <h1 style={{ fontSize: 30, fontWeight: 700, color: T.text,
            letterSpacing: '-0.03em', marginBottom: 12,
            fontFamily: 'Geist, Inter, sans-serif' }}>
            {cat.name}
          </h1>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.7, maxWidth: 580, marginBottom: 16 }}>
            Професійне обладнання для дозування гігієнічної продукції — диспенсери, дозатори,
            тримачі. Сучасні рішення від провідних світових виробників для офісів, виробництв
            та закладів громадського харчування.
          </p>
          <div style={{ display: 'flex', gap: 24, fontSize: 13, color: T.text2, alignItems:'center' }}>
            <span><strong style={{ color: T.text }}>{cat.count}</strong> товарів</span>
            <span style={{ color: T.border2 }}>·</span>
            <span><strong style={{ color: T.text }}>{subcategories.length}</strong> підкатегорій</span>
            <span style={{ color: T.border2 }}>·</span>
            <span><strong style={{ color: T.text }}>12</strong> брендів</span>
          </div>
        </div>
      </div>

      {/* Subcategories grid */}
      <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text,
        letterSpacing: '-0.01em', marginBottom: 14 }}>Підкатегорії</h2>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 14, marginBottom: 48 }}>
        {subcategories.map(sub => (
          <div key={sub.id} onClick={() => setPage('catalog-sub')}
            style={{ background: T.surface, border: `1px solid ${T.border}`,
              borderRadius: T.rLg, padding: '20px 24px', cursor: 'pointer',
              boxShadow: T.shadow, transition: 'all 0.15s',
              display: 'flex', gap: 16, alignItems: 'center' }}
            onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2; e.currentTarget.style.boxShadow=T.shadowMd;}}
            onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border; e.currentTarget.style.boxShadow=T.shadow;}}>
            <div style={{ width: 48, height: 48, background: T.bgAlt,
              border: `1px solid ${T.border}`, borderRadius: T.rSm,
              display: 'flex', alignItems: 'center', justifyContent: 'center',
              fontSize: 22, flexShrink: 0 }}>
              {sub.img}
            </div>
            <div style={{ flex: 1, minWidth: 0 }}>
              <div style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 2 }}>{sub.name}</div>
              <div style={{ fontSize: 12, color: T.text3 }}>{sub.count} товарів</div>
            </div>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke={T.text3} strokeWidth="2">
              <path d="m9 18 6-6-6-6"/>
            </svg>
          </div>
        ))}
      </div>

      {/* Popular brands */}
      <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text,
        letterSpacing: '-0.01em', marginBottom: 14 }}>Популярні бренди</h2>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(6, 1fr)', gap: 10, marginBottom: 48 }}>
        {BRANDS.slice(0, 6).map(b => (
          <div key={b} onClick={()=>setPage('brand-products')}
            style={{ background: T.surface, border: `1px solid ${T.border}`,
              borderRadius: T.rLg, padding: '20px 14px', textAlign: 'center',
              cursor: 'pointer', transition: 'all 0.15s' }}
            onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.background=T.muted;}}
            onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.background=T.surface;}}>
            <div style={{ width: 36, height: 36, background: T.bgAlt,
              border: `1px solid ${T.border}`, borderRadius: T.rSm,
              margin: '0 auto 8px', display: 'flex',
              alignItems: 'center', justifyContent: 'center',
              fontSize: 14, fontWeight: 700, color: T.text2 }}>
              {b[0]}
            </div>
            <div style={{ fontSize: 12, fontWeight: 500, color: T.text }}>{b}</div>
          </div>
        ))}
      </div>

      {/* Popular products */}
      <div style={{ display:'flex', justifyContent:'space-between', alignItems:'baseline', marginBottom:14 }}>
        <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text,
          letterSpacing: '-0.01em' }}>Популярне у категорії</h2>
        <Btn variant="ghost" size="sm" onClick={()=>setPage('catalog-sub')}>
          Весь каталог →
        </Btn>
      </div>
      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(4, 1fr)', gap: 16, marginBottom: 60 }}>
        {popularProducts.map(p => (
          <ProductCard key={p.id} product={p}
            onView={()=>setPage('product')} onAdd={addToCart}/>
        ))}
      </div>
    </div>
  );
}

Object.assign(window, { BrandDetailPage, CatalogCategoryPage });
