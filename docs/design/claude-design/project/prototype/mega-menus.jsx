// ── MEGA MENUS ────────────────────────────────────────────────────
// Hover dropdowns from header nav: Каталог / Бренди / Послуги / Про нас

function MegaMenu({ kind, setPage, onClose }) {
  const { T } = useContext(ThemeCtx);

  if (kind === 'catalog') {
    return (
      <div style={{ background:T.surface, borderTop:`1px solid ${T.border}`,
        boxShadow: T.shadowMd, padding:'24px 60px',
        display:'grid', gridTemplateColumns:'2fr 1fr', gap:32 }}>
        <div>
          <div style={{ fontSize:11, fontWeight:600, color:T.text3,
            textTransform:'uppercase', letterSpacing:'0.05em', marginBottom:14 }}>
            Категорії товарів
          </div>
          <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:4 }}>
            {CATEGORIES.map(cat=>(
              <div key={cat.id} onClick={()=>{setPage('catalog'); onClose();}}
                style={{ display:'flex', alignItems:'center', gap:12,
                  padding:'10px 12px', borderRadius:T.rSm, cursor:'pointer',
                  transition:'background 0.12s' }}
                onMouseEnter={e=>e.currentTarget.style.background=T.muted}
                onMouseLeave={e=>e.currentTarget.style.background='transparent'}>
                <div style={{ width:36, height:36, background:T.bgAlt,
                  border:`1px solid ${T.border}`, borderRadius:T.rSm,
                  flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center',
                  fontSize:13, fontWeight:600, color:T.text2 }}>
                  {cat.name[0]}
                </div>
                <div style={{ minWidth:0, flex:1 }}>
                  <div style={{ fontSize:13, fontWeight:500, color:T.text }}>{cat.name}</div>
                  <div style={{ fontSize:11, color:T.text3, marginTop:2 }}>
                    {cat.count.toLocaleString()} товарів
                  </div>
                </div>
              </div>
            ))}
          </div>
          <div style={{ marginTop:16, paddingTop:14, borderTop:`1px solid ${T.border}` }}>
            <span onClick={()=>{setPage('catalog'); onClose();}} style={{ fontSize:13, fontWeight:500,
              color:T.text, cursor:'pointer', display:'inline-flex', alignItems:'center', gap:6 }}>
              Весь каталог
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" strokeWidth="2">
                <path d="M5 12h14M13 5l7 7-7 7"/>
              </svg>
            </span>
          </div>
        </div>
        {/* Promo card */}
        <div onClick={()=>{setPage('aktsyii'); onClose();}}
          style={{ background:T.bgAlt, border:`1px solid ${T.border}`,
            borderRadius:T.rLg, padding:20, cursor:'pointer', display:'flex',
            flexDirection:'column', justifyContent:'space-between',
            transition:'all 0.2s', minHeight:240 }}
          onMouseEnter={e=>{ e.currentTarget.style.borderColor=T.border2; e.currentTarget.style.boxShadow=T.shadowMd; }}
          onMouseLeave={e=>{ e.currentTarget.style.borderColor=T.border; e.currentTarget.style.boxShadow='none'; }}>
          <div>
            <span style={{ display:'inline-block', padding:'2px 8px',
              background: T.bg==='#09090b'?'#450a0a':'#fef2f2',
              border:`1px solid ${T.bg==='#09090b'?'#7f1d1d':'#fecaca'}`,
              borderRadius:T.rPill, fontSize:10, fontWeight:600, color:'#dc2626',
              marginBottom:10 }}>
              Акції
            </span>
            <div style={{ fontSize:18, fontWeight:700, color:T.text,
              letterSpacing:'-0.025em', marginBottom:6, lineHeight:1.3,
              fontFamily:'Geist, Inter, sans-serif' }}>
              Знижки до −20%
            </div>
            <div style={{ fontSize:12, color:T.text2, lineHeight:1.5 }}>
              Спеціальні пропозиції на бренди Tork, Mirka, 3M та інші
            </div>
          </div>
          <div style={{ marginTop:16 }}>
            <Btn variant="outline" size="sm" onClick={e=>{e.stopPropagation(); setPage('aktsyii'); onClose();}}>
              Дивитись акції →
            </Btn>
          </div>
        </div>
      </div>
    );
  }

  if (kind === 'brands') {
    return (
      <div style={{ background:T.surface, borderTop:`1px solid ${T.border}`,
        boxShadow: T.shadowMd, padding:'24px 60px',
        display:'grid', gridTemplateColumns:'2fr 1fr', gap:32 }}>
        <div>
          <div style={{ fontSize:11, fontWeight:600, color:T.text3,
            textTransform:'uppercase', letterSpacing:'0.05em', marginBottom:14 }}>
            Популярні бренди
          </div>
          <div style={{ display:'grid', gridTemplateColumns:'repeat(5,1fr)', gap:8 }}>
            {BRANDS.map(b=>(
              <div key={b} onClick={()=>{setPage('brand-products'); onClose();}}
                style={{ background:T.bgAlt, border:`1px solid ${T.border}`,
                  borderRadius:T.rSm, padding:'14px 10px', textAlign:'center',
                  cursor:'pointer', transition:'all 0.15s' }}
                onMouseEnter={e=>{ e.currentTarget.style.borderColor=T.border2; e.currentTarget.style.background=T.muted; }}
                onMouseLeave={e=>{ e.currentTarget.style.borderColor=T.border; e.currentTarget.style.background=T.bgAlt; }}>
                <div style={{ width:32, height:32, background:T.muted,
                  border:`1px solid ${T.border}`, borderRadius:T.rSm,
                  margin:'0 auto 6px', display:'flex', alignItems:'center',
                  justifyContent:'center', fontSize:12, fontWeight:700, color:T.text2 }}>
                  {b[0]}
                </div>
                <div style={{ fontSize:11, fontWeight:600, color:T.text }}>{b}</div>
              </div>
            ))}
          </div>
          <div style={{ marginTop:16, paddingTop:14, borderTop:`1px solid ${T.border}` }}>
            <span onClick={()=>{setPage('brands'); onClose();}} style={{ fontSize:13, fontWeight:500,
              color:T.text, cursor:'pointer', display:'inline-flex', alignItems:'center', gap:6 }}>
              Усі бренди
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" strokeWidth="2">
                <path d="M5 12h14M13 5l7 7-7 7"/>
              </svg>
            </span>
          </div>
        </div>
        <div style={{ background:T.bgAlt, border:`1px solid ${T.border}`,
          borderRadius:T.rLg, padding:20, display:'flex',
          flexDirection:'column', justifyContent:'space-between', minHeight:200 }}>
          <div>
            <div style={{ fontSize:11, fontWeight:600, color:T.text3,
              textTransform:'uppercase', letterSpacing:'0.05em', marginBottom:8 }}>
              Стати дилером
            </div>
            <div style={{ fontSize:16, fontWeight:700, color:T.text,
              letterSpacing:'-0.025em', marginBottom:6, lineHeight:1.35,
              fontFamily:'Geist, Inter, sans-serif' }}>
              Партнерська програма
            </div>
            <div style={{ fontSize:12, color:T.text2, lineHeight:1.5 }}>
              Знижки до 25%, дропшипінг, маркетингова підтримка
            </div>
          </div>
          <div style={{ marginTop:16 }}>
            <Btn size="sm" onClick={()=>{setPage('partners-popup'); onClose();}}>
              Дізнатись →
            </Btn>
          </div>
        </div>
      </div>
    );
  }

  if (kind === 'services') {
    const services = [
      { icon:'🎓', title:'Навчальний центр',     desc:'Курси та сертифікація' },
      { icon:'🛠️', title:'Сервісні роботи',       desc:'Ремонт обладнання' },
      { icon:'🚚', title:'Доставка',              desc:'По Україні та Європі' },
      { icon:'📋', title:'Технічна консультація', desc:'Підбір рішень' },
      { icon:'🎨', title:'Підбір кольору',        desc:'Лабораторія A-green' },
      { icon:'📦', title:'Дропшипінг',             desc:'Для дилерів' },
    ];
    return (
      <div style={{ background:T.surface, borderTop:`1px solid ${T.border}`,
        boxShadow: T.shadowMd, padding:'24px 60px',
        display:'grid', gridTemplateColumns:'2fr 1fr', gap:32 }}>
        <div>
          <div style={{ fontSize:11, fontWeight:600, color:T.text3,
            textTransform:'uppercase', letterSpacing:'0.05em', marginBottom:14 }}>
            Що ми робимо
          </div>
          <div style={{ display:'grid', gridTemplateColumns:'repeat(2,1fr)', gap:6 }}>
            {services.map(s=>(
              <div key={s.title} onClick={()=>{setPage('services'); onClose();}}
                style={{ display:'flex', gap:12, padding:'10px 12px',
                  borderRadius:T.rSm, cursor:'pointer', transition:'background 0.12s' }}
                onMouseEnter={e=>e.currentTarget.style.background=T.muted}
                onMouseLeave={e=>e.currentTarget.style.background='transparent'}>
                <div style={{ width:36, height:36, background:T.bgAlt,
                  border:`1px solid ${T.border}`, borderRadius:T.rSm,
                  flexShrink:0, display:'flex', alignItems:'center',
                  justifyContent:'center', fontSize:16 }}>
                  {s.icon}
                </div>
                <div>
                  <div style={{ fontSize:13, fontWeight:500, color:T.text }}>{s.title}</div>
                  <div style={{ fontSize:11, color:T.text3, marginTop:2 }}>{s.desc}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
        <div style={{ background:T.bgAlt, border:`1px solid ${T.border}`,
          borderRadius:T.rLg, padding:20, display:'flex',
          flexDirection:'column', justifyContent:'space-between', minHeight:200 }}>
          <div>
            <div style={{ fontSize:16, fontWeight:700, color:T.text,
              letterSpacing:'-0.025em', marginBottom:6, lineHeight:1.35,
              fontFamily:'Geist, Inter, sans-serif' }}>
              Замовити сервіс
            </div>
            <div style={{ fontSize:12, color:T.text2, lineHeight:1.5 }}>
              Залиште заявку — наш менеджер зв'яжеться протягом 1 робочого дня
            </div>
          </div>
          <div style={{ marginTop:16 }}>
            <Btn size="sm" onClick={()=>{setPage('contacts'); onClose();}}>
              Залишити заявку →
            </Btn>
          </div>
        </div>
      </div>
    );
  }

  if (kind === 'about') {
    const links = [
      { title:'Про компанію', desc:'Історія, місія, цінності', page:'about' },
      { title:'Бренди',       desc:'Наші партнери',             page:'brands' },
      { title:'Партнерство',  desc:'Стати дилером A-green',     page:'partners-popup' },
      { title:'Доставка',     desc:'Умови та терміни',          page:'delivery' },
      { title:'Контакти',     desc:'Адреси та зв\'язок',         page:'contacts' },
      { title:'Вакансії',     desc:'Приєднайся до команди',     page:'vacancies' },
    ];
    return (
      <div style={{ background:T.surface, borderTop:`1px solid ${T.border}`,
        boxShadow: T.shadowMd, padding:'24px 60px',
        display:'grid', gridTemplateColumns:'2fr 1fr', gap:32 }}>
        <div>
          <div style={{ fontSize:11, fontWeight:600, color:T.text3,
            textTransform:'uppercase', letterSpacing:'0.05em', marginBottom:14 }}>
            Компанія
          </div>
          <div style={{ display:'grid', gridTemplateColumns:'repeat(2,1fr)', gap:6 }}>
            {links.map(l=>(
              <div key={l.page} onClick={()=>{setPage(l.page); onClose();}}
                style={{ padding:'10px 12px', borderRadius:T.rSm, cursor:'pointer',
                  transition:'background 0.12s' }}
                onMouseEnter={e=>e.currentTarget.style.background=T.muted}
                onMouseLeave={e=>e.currentTarget.style.background='transparent'}>
                <div style={{ fontSize:13, fontWeight:500, color:T.text }}>{l.title}</div>
                <div style={{ fontSize:11, color:T.text3, marginTop:2 }}>{l.desc}</div>
              </div>
            ))}
          </div>
        </div>
        <div style={{ background:T.bgAlt, border:`1px solid ${T.border}`,
          borderRadius:T.rLg, padding:20 }}>
          <div style={{ fontSize:11, fontWeight:600, color:T.text3,
            textTransform:'uppercase', letterSpacing:'0.05em', marginBottom:10 }}>
            Контакти
          </div>
          <div style={{ display:'flex', flexDirection:'column', gap:10, fontSize:13 }}>
            <div>
              <div style={{ color:T.text3, fontSize:11, marginBottom:2 }}>Телефон</div>
              <div style={{ color:T.text, fontWeight:500 }}>+380 67 075-71-70</div>
            </div>
            <div>
              <div style={{ color:T.text3, fontSize:11, marginBottom:2 }}>Email</div>
              <div style={{ color:T.text, fontWeight:500 }}>info@a-green.ua</div>
            </div>
            <div>
              <div style={{ color:T.text3, fontSize:11, marginBottom:2 }}>Графік</div>
              <div style={{ color:T.text, fontWeight:500 }}>Пн–Пт · 9:00–18:00</div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return null;
}

Object.assign(window, { MegaMenu });
