// ── CLIENT CABINET (повний кабінет клієнта за ТЗ) ────────────────
// Sidebar: Каталог, Замовлення, Документообіг, Умови співпраці,
// Навчання, Техпідтримка, Перс. дані, Налаштування, Вихід

const ORDER_STATUSES = {
  pending:   { label:'Очікує підтвердження', color:'#b45309', bg:'#fffbeb', bd:'#fde68a', dbg:'#451a03', dbd:'#7c2d12' },
  confirmed: { label:'Підтверджене',          color:'#2563eb', bg:'#eff6ff', bd:'#bfdbfe', dbg:'#172554', dbd:'#1e3a8a' },
  payment:   { label:'Очікує оплати',         color:'#7c3aed', bg:'#f5f3ff', bd:'#ddd6fe', dbg:'#2e1065', dbd:'#5b21b6' },
  processing:{ label:'В обробці',              color:'#0891b2', bg:'#ecfeff', bd:'#a5f3fc', dbg:'#083344', dbd:'#155e75' },
  shipped:   { label:'Відвантажено',           color:'#ca8a04', bg:'#fefce8', bd:'#fef08a', dbg:'#422006', dbd:'#854d0e' },
  delivered: { label:'Доставлено',             color:'#16a34a', bg:'#f0fdf4', bd:'#bbf7d0', dbg:'#052e16', dbd:'#14532d' },
  completed: { label:'Завершено',              color:'#71717a', bg:'#f4f4f5', bd:'#e4e4e7', dbg:'#18181b', dbd:'#27272a' },
};

function StatusPill({ status }) {
  const { T } = useContext(ThemeCtx);
  const isDark = T.bg === '#09090b';
  const s = ORDER_STATUSES[status] || ORDER_STATUSES.pending;
  return (
    <span style={{ padding:'4px 12px', borderRadius:T.rPill, fontSize:11, fontWeight:600,
      whiteSpace:'nowrap', color:s.color,
      background: isDark ? s.dbg : s.bg,
      border:`1px solid ${isDark ? s.dbd : s.bd}` }}>
      {s.label}
    </span>
  );
}

function ClientCabinet({ setPage, addToCart, cartCount, setCartDrawer }) {
  const { T } = useContext(ThemeCtx);
  const [sec, setSec] = useState('catalog');
  const [lang, setLang] = useState('УКР');
  const [langOpen, setLangOpen] = useState(false);
  const [openOrder, setOpenOrder] = useState(null);

  const nav = [
    { key:'catalog',   icon:'⊞',  label:'Каталог' },
    { key:'orders',    icon:'◫',  label:'Замовлення' },
    { key:'docs',      icon:'⊟',  label:'Документообіг' },
    { key:'terms',     icon:'◈',  label:'Умови співпраці' },
    { key:'study',     icon:'🎓', label:'Навчання' },
    { key:'support',   icon:'◉',  label:'Техпідтримка' },
    { key:'personal',  icon:'◎',  label:'Персональні дані' },
    { key:'settings',  icon:'⚙',  label:'Налаштування' },
  ];

  const ORDERS = [
    { id:'#A-2026-04220', date:'22 квіт 2026', status:'processing', total:3297, items:3 },
    { id:'#A-2026-04100', date:'15 квіт 2026', status:'shipped',    total:1099, items:1 },
    { id:'#A-2026-03990', date:'08 квіт 2026', status:'delivered',  total:5640, items:7 },
    { id:'#A-2026-03700', date:'25 бер 2026',  status:'completed',  total:890,  items:2 },
    { id:'#A-2026-03500', date:'18 бер 2026',  status:'pending',    total:2150, items:4 },
  ];

  const LANGS = ['УКР','ENG','POL','РУС'];

  return (
    <div style={{ background:T.bg, minHeight:'100vh' }}>
      {/* ── HEADER ── */}
      <div style={{ background:T.surface, borderBottom:`1px solid ${T.border}`,
        padding:'0 32px', height:64, display:'flex', alignItems:'center', gap:20 }}>
        <div onClick={()=>setPage('main')} style={{ display:'flex', alignItems:'center', gap:8, cursor:'pointer' }}>
          <div style={{ width:28, height:28, background:T.invBg, borderRadius:8,
            display:'flex', alignItems:'center', justifyContent:'center' }}>
            <span style={{ fontSize:11, fontWeight:700, color:T.invText }}>AG</span>
          </div>
          <span style={{ fontWeight:600, fontSize:15, color:T.text, letterSpacing:'-0.025em' }}>A-green</span>
        </div>

        {/* Greeting */}
        <div style={{ marginLeft:8 }}>
          <div style={{ fontSize:13, fontWeight:600, color:T.text }}>Вітаємо, Іване! 👋</div>
          <div style={{ fontSize:11, color:T.text3 }}>Особистий кабінет клієнта</div>
        </div>

        <div style={{ flex:1 }}/>

        {/* Search */}
        <div style={{ position:'relative', width:280 }}>
          <svg style={{ position:'absolute', left:12, top:'50%', transform:'translateY(-50%)', color:T.text3, pointerEvents:'none' }}
            width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <input placeholder="Пошук по кабінету..."
            style={{ width:'100%', background:T.bgAlt, border:`1px solid ${T.border}`,
              borderRadius:T.rSm, padding:'8px 12px 8px 36px', color:T.text, fontSize:13,
              outline:'none', fontFamily:'Geist, Inter, sans-serif' }}/>
        </div>

        {/* Language */}
        <div style={{ position:'relative' }}>
          <button onClick={()=>setLangOpen(!langOpen)}
            style={{ display:'flex', alignItems:'center', gap:6, background:T.bgAlt,
              border:`1px solid ${T.border}`, borderRadius:T.rSm, padding:'7px 12px',
              fontSize:12, fontWeight:500, color:T.text, cursor:'pointer', fontFamily:'Geist, Inter, sans-serif' }}>
            🌐 {lang}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          {langOpen && (
            <div style={{ position:'absolute', top:'calc(100% + 4px)', right:0, background:T.surface,
              border:`1px solid ${T.border}`, borderRadius:T.rSm, boxShadow:T.shadowMd, overflow:'hidden', zIndex:100 }}>
              {LANGS.map(l=>(
                <div key={l} onClick={()=>{setLang(l); setLangOpen(false);}}
                  style={{ padding:'8px 18px', fontSize:13, cursor:'pointer',
                    color: l===lang?T.text:T.text2, background:l===lang?T.muted:'transparent', whiteSpace:'nowrap' }}
                  onMouseEnter={e=>e.currentTarget.style.background=T.muted}
                  onMouseLeave={e=>e.currentTarget.style.background=l===lang?T.muted:'transparent'}>
                  {l}
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Cart */}
        <button onClick={()=>setCartDrawer ? setCartDrawer(true) : setPage('cart')}
          style={{ position:'relative', background:T.bgAlt, border:`1px solid ${T.border}`,
            borderRadius:T.rSm, padding:8, cursor:'pointer', color:T.text2 }}>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
          {cartCount>0 && (
            <span style={{ position:'absolute', top:-4, right:-4, minWidth:16, height:16, background:T.danger,
              color:'#fff', borderRadius:T.rPill, fontSize:9, fontWeight:700, display:'flex',
              alignItems:'center', justifyContent:'center', padding:'0 4px' }}>{cartCount}</span>
          )}
        </button>
      </div>

      {/* ── BODY ── */}
      <div style={{ display:'grid', gridTemplateColumns:'240px 1fr' }}>
        {/* Sidebar */}
        <div style={{ background:T.surface, borderRight:`1px solid ${T.border}`,
          padding:'16px 0', minHeight:'calc(100vh - 64px)', display:'flex', flexDirection:'column' }}>
          <div style={{ flex:1 }}>
            {nav.map(item=>(
              <div key={item.key} onClick={()=>setSec(item.key)}
                style={{ padding:'10px 20px', fontSize:14, cursor:'pointer',
                  fontWeight: sec===item.key?500:400,
                  color: sec===item.key?T.text:T.text2,
                  background: sec===item.key?T.muted:'transparent',
                  borderLeft:`2px solid ${sec===item.key?T.invBg:'transparent'}`,
                  transition:'all 0.12s', display:'flex', alignItems:'center', gap:12 }}>
                <span style={{ fontSize:15, width:18, textAlign:'center' }}>{item.icon}</span>
                {item.label}
              </div>
            ))}
          </div>
          <div style={{ borderTop:`1px solid ${T.border}`, paddingTop:8 }}>
            <div onClick={()=>setPage('main')}
              style={{ padding:'10px 20px', fontSize:14, color:T.danger, cursor:'pointer',
                display:'flex', alignItems:'center', gap:12 }}
              onMouseEnter={e=>e.currentTarget.style.background=T.muted}
              onMouseLeave={e=>e.currentTarget.style.background='transparent'}>
              <span style={{ fontSize:15, width:18, textAlign:'center' }}>⏻</span>
              Вихід
            </div>
          </div>
        </div>

        {/* Content */}
        <div style={{ padding:32, overflow:'auto' }}>

          {/* CATALOG */}
          {sec==='catalog' && (
            <div>
              <h2 style={sectionTitle(T)}>Каталог</h2>
              <p style={{ fontSize:14, color:T.text2, marginBottom:24 }}>Замовляйте товари за вашими персональними цінами</p>
              <div style={{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:12 }}>
                {CATEGORIES.map(c=> <CatCard key={c.id} cat={c} onSelect={()=>setPage('catalog')}/> )}
              </div>
            </div>
          )}

          {/* ORDERS */}
          {sec==='orders' && (
            <div>
              <h2 style={sectionTitle(T)}>Замовлення</h2>
              <div style={{ display:'flex', flexDirection:'column', gap:8, marginTop:20 }}>
                {ORDERS.map(o=>(
                  <div key={o.id} style={{ background:T.surface, border:`1px solid ${openOrder===o.id?T.border2:T.border}`,
                    borderRadius:T.rLg, overflow:'hidden', boxShadow:T.shadow, transition:'all 0.15s' }}>
                    <div onClick={()=>setOpenOrder(openOrder===o.id?null:o.id)}
                      style={{ padding:'16px 20px', display:'flex', alignItems:'center', gap:16, cursor:'pointer' }}>
                      <div style={{ flex:1 }}>
                        <div style={{ fontSize:14, fontWeight:600, color:T.text }}>{o.id}</div>
                        <div style={{ fontSize:12, color:T.text2, marginTop:2 }}>{o.date} · {o.items} тов.</div>
                      </div>
                      <StatusPill status={o.status}/>
                      <div style={{ fontSize:16, fontWeight:700, color:T.text, letterSpacing:'-0.025em', minWidth:90, textAlign:'right' }}>{fmt(o.total)}</div>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                        style={{ transform: openOrder===o.id?'rotate(180deg)':'none', transition:'transform 0.2s' }}><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                    {openOrder===o.id && (
                      <div style={{ borderTop:`1px solid ${T.border}`, padding:'16px 20px' }}>
                        <div style={{ display:'flex', flexDirection:'column', gap:10, marginBottom:16 }}>
                          {PRODUCTS.slice(0,o.items).map(p=>(
                            <div key={p.id} style={{ display:'flex', gap:10, alignItems:'center' }}>
                              <div style={{ width:40, height:40, borderRadius:T.rSm, overflow:'hidden', flexShrink:0 }}><Img h={40} label=""/></div>
                              <div style={{ flex:1, fontSize:12, color:T.text }}>{p.name}</div>
                              <div style={{ fontSize:12, color:T.text2 }}>1 × {fmt(p.price)}</div>
                            </div>
                          ))}
                        </div>
                        <div style={{ display:'flex', gap:8 }}>
                          <Btn size="sm" onClick={()=>{ PRODUCTS.slice(0,o.items).forEach(p=>addToCart(p)); setPage('cart'); }}>↻ Повторити замовлення</Btn>
                          <Btn size="sm" variant="outline">↓ Рахунок-фактура</Btn>
                          <Btn size="sm" variant="ghost">Деталі</Btn>
                        </div>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* DOCS */}
          {sec==='docs' && (
            <div>
              <h2 style={sectionTitle(T)}>Документообіг</h2>
              <div style={{ display:'flex', gap:8, marginTop:20, marginBottom:20 }}>
                {['Усі','Рахунки','Акти','Накладні','Договори'].map((f,i)=>(
                  <button key={f} style={{ padding:'7px 16px', borderRadius:T.rPill,
                    background: i===0?T.invBg:T.surface, border:`1px solid ${i===0?T.invBg:T.border}`,
                    color: i===0?T.invText:T.text2, fontSize:13, fontWeight:i===0?600:400,
                    cursor:'pointer', fontFamily:'Geist, Inter, sans-serif' }}>{f}</button>
                ))}
              </div>
              <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, overflow:'hidden' }}>
                {[
                  ['Рахунок-фактура #2026-04','Рахунок','22 квіт 2026','PDF · 128 KB'],
                  ['Акт виконаних робіт #04','Акт','22 квіт 2026','PDF · 96 KB'],
                  ['Видаткова накладна #2026-03','Накладна','15 бер 2026','PDF · 84 KB'],
                  ['Договір поставки #2025-001','Договір','15 січ 2025','PDF · 512 KB'],
                ].map((d,i,a)=>(
                  <div key={d[0]} style={{ padding:'14px 20px', borderBottom:i<a.length-1?`1px solid ${T.border}`:'none',
                    display:'grid', gridTemplateColumns:'auto 1fr 110px 120px auto', gap:16, alignItems:'center' }}>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke={T.text2} strokeWidth="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span style={{ fontSize:14, fontWeight:500, color:T.text }}>{d[0]}</span>
                    <span style={{ fontSize:12, color:T.text2 }}>{d[1]}</span>
                    <span style={{ fontSize:12, color:T.text3 }}>{d[2]}</span>
                    <Btn size="sm" variant="outline">↓ {d[3].split(' · ')[0]}</Btn>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* TERMS — кешбек */}
          {sec==='terms' && (
            <div>
              <h2 style={sectionTitle(T)}>Умови співпраці</h2>
              {/* Cashback card */}
              <div style={{ background:'linear-gradient(135deg,#1e293b,#0f172a)', borderRadius:T.rLg,
                padding:32, marginTop:20, marginBottom:24, color:'#fff', position:'relative', overflow:'hidden' }}>
                <div style={{ position:'absolute', top:-40, right:-40, width:200, height:200, borderRadius:'50%',
                  background:'radial-gradient(circle,rgba(34,197,94,0.18),transparent 70%)' }}/>
                <div style={{ position:'relative', display:'flex', justifyContent:'space-between', alignItems:'flex-start' }}>
                  <div>
                    <div style={{ fontSize:11, fontWeight:600, letterSpacing:'0.1em', color:'rgba(255,255,255,0.6)', textTransform:'uppercase', marginBottom:8 }}>Кешбек-баланс</div>
                    <div style={{ fontSize:40, fontWeight:800, letterSpacing:'-0.03em', color:'#4ade80', fontFamily:'Geist, Inter, sans-serif' }}>1 240 ₴</div>
                    <div style={{ fontSize:13, color:'rgba(255,255,255,0.7)', marginTop:6 }}>Поточний кешбек 3% від суми замовлень</div>
                  </div>
                  <div style={{ textAlign:'right' }}>
                    <div style={{ fontSize:11, fontWeight:600, letterSpacing:'0.1em', color:'rgba(255,255,255,0.6)', textTransform:'uppercase', marginBottom:8 }}>Рівень</div>
                    <div style={{ fontSize:24, fontWeight:800, fontFamily:'Geist, Inter, sans-serif' }}>Срібний</div>
                  </div>
                </div>
                <div style={{ marginTop:24, position:'relative' }}>
                  <div style={{ display:'flex', justifyContent:'space-between', fontSize:12, marginBottom:6, color:'rgba(255,255,255,0.85)' }}>
                    <span>До Золотого рівня (кешбек 5%)</span><span>62 000 / 100 000 ₴</span>
                  </div>
                  <div style={{ height:6, background:'rgba(255,255,255,0.15)', borderRadius:T.rPill }}>
                    <div style={{ width:'62%', height:'100%', background:'linear-gradient(90deg,#4ade80,#22c55e)', borderRadius:T.rPill }}/>
                  </div>
                </div>
              </div>
              {/* History */}
              <h3 style={{ fontSize:16, fontWeight:600, color:T.text, marginBottom:14 }}>Історія нарахувань</h3>
              <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, overflow:'hidden' }}>
                {[
                  ['+ 99 ₴','Кешбек за #A-2026-04220','22 квіт 2026','#16a34a'],
                  ['+ 33 ₴','Кешбек за #A-2026-04100','15 квіт 2026','#16a34a'],
                  ['− 500 ₴','Списано при оплаті','08 квіт 2026','#dc2626'],
                  ['+ 169 ₴','Кешбек за #A-2026-03990','08 квіт 2026','#16a34a'],
                ].map((r,i,a)=>(
                  <div key={i} style={{ padding:'14px 20px', borderBottom:i<a.length-1?`1px solid ${T.border}`:'none',
                    display:'grid', gridTemplateColumns:'90px 1fr auto', gap:16, alignItems:'center' }}>
                    <span style={{ fontSize:15, fontWeight:700, color:r[3], fontFamily:'Geist, Inter, sans-serif' }}>{r[0]}</span>
                    <span style={{ fontSize:13, color:T.text }}>{r[1]}</span>
                    <span style={{ fontSize:12, color:T.text3 }}>{r[2]}</span>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* STUDY */}
          {sec==='study' && (
            <div>
              <h2 style={sectionTitle(T)}>Навчання</h2>
              <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:12, marginTop:20 }}>
                {[
                  {t:'Робота з диспенсерами',type:'Відеокурс',dur:'45 хв',isNew:true},
                  {t:'Абразивні матеріали',type:'Вебінар',dur:'1.5 год',isNew:false},
                  {t:'Лакофарбові роботи',type:'Відеокурс',dur:'3 год',isNew:false},
                  {t:'Сертифікація майстра',type:'Іспит',dur:'2 год',isNew:true},
                  {t:'Зберігання хімматеріалів',type:'Документ',dur:'15 хв',isNew:false},
                  {t:'Новинки Kimberly-Clark',type:'Презентація',dur:'30 хв',isNew:true},
                ].map(c=>(
                  <div key={c.t} style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, padding:20, cursor:'pointer', boxShadow:T.shadow }}
                    onMouseEnter={e=>e.currentTarget.style.borderColor=T.border2}
                    onMouseLeave={e=>e.currentTarget.style.borderColor=T.border}>
                    <div style={{ display:'flex', justifyContent:'space-between', marginBottom:10 }}>
                      <span style={{ fontSize:11, fontWeight:600, color:T.text3, textTransform:'uppercase', letterSpacing:'0.08em' }}>{c.type}</span>
                      {c.isNew && <Badge label="Новинка"/>}
                    </div>
                    <div style={{ fontSize:14, fontWeight:500, color:T.text, lineHeight:1.5, marginBottom:10 }}>{c.t}</div>
                    <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center' }}>
                      <span style={{ fontSize:12, color:T.text3 }}>⏱ {c.dur}</span>
                      <Btn variant="ghost" size="sm">Розпочати →</Btn>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* SUPPORT — запити з типами */}
          {sec==='support' && (
            <div>
              <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:20 }}>
                <h2 style={{...sectionTitle(T), marginBottom:0}}>Техпідтримка</h2>
                <Btn>+ Новий запит</Btn>
              </div>
              {/* Request types */}
              <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:12, marginBottom:24 }}>
                {[
                  ['💬','Консультація','Питання щодо товарів та послуг'],
                  ['🔧','Сервіс','Ремонт та обслуговування обладнання'],
                  ['📍','Виклик спеціаліста','Виїзд майстра на об\'єкт'],
                ].map(([icon,t,d])=>(
                  <div key={t} style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, padding:20, cursor:'pointer', boxShadow:T.shadow }}
                    onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.boxShadow=T.shadowMd;}}
                    onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.boxShadow=T.shadow;}}>
                    <div style={{ fontSize:24, marginBottom:10 }}>{icon}</div>
                    <div style={{ fontSize:14, fontWeight:600, color:T.text, marginBottom:4 }}>{t}</div>
                    <div style={{ fontSize:12, color:T.text2, lineHeight:1.5 }}>{d}</div>
                  </div>
                ))}
              </div>
              {/* Tickets */}
              <h3 style={{ fontSize:16, fontWeight:600, color:T.text, marginBottom:14 }}>Мої запити</h3>
              <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, overflow:'hidden' }}>
                {[
                  ['#T-0042','Консультація щодо диспенсера','open'],
                  ['#T-0038','Сервіс обладнання','pending'],
                  ['#T-0035','Виклик спеціаліста','resolved'],
                ].map((t,i,a)=>{
                  const sc = { open:['Відкритий','#dc2626','#fef2f2','#fecaca'], pending:['В обробці','#b45309','#fffbeb','#fde68a'], resolved:['Вирішений','#16a34a','#f0fdf4','#bbf7d0'] }[t[2]];
                  const isDark = T.bg==='#09090b';
                  return (
                    <div key={t[0]} style={{ padding:'14px 20px', borderBottom:i<a.length-1?`1px solid ${T.border}`:'none',
                      display:'grid', gridTemplateColumns:'90px 1fr auto', gap:16, alignItems:'center', cursor:'pointer' }}
                      onMouseEnter={e=>e.currentTarget.style.background=T.muted}
                      onMouseLeave={e=>e.currentTarget.style.background='transparent'}>
                      <span style={{ fontSize:13, fontWeight:600, color:T.text, fontFamily:'monospace' }}>{t[0]}</span>
                      <span style={{ fontSize:13, color:T.text2 }}>{t[1]}</span>
                      <span style={{ padding:'4px 12px', borderRadius:T.rPill, fontSize:11, fontWeight:600,
                        color:sc[1], background:isDark?'transparent':sc[2], border:`1px solid ${sc[3]}` }}>{sc[0]}</span>
                    </div>
                  );
                })}
              </div>
            </div>
          )}

          {/* PERSONAL — доставка/оплата */}
          {sec==='personal' && (
            <div>
              <h2 style={sectionTitle(T)}>Персональні дані</h2>
              <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:20, marginTop:20 }}>
                {/* Profile */}
                <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, padding:24 }}>
                  <div style={{ fontSize:14, fontWeight:600, color:T.text, marginBottom:16 }}>Контактні дані</div>
                  <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                    <Field label="Ім'я" placeholder="Іван"/>
                    <Field label="Прізвище" placeholder="Іваненко"/>
                    <Field label="Телефон" placeholder="+380 XX XXX XX XX"/>
                    <Field label="Email" placeholder="ivan@example.com" type="email"/>
                  </div>
                </div>
                {/* Delivery & payment */}
                <div style={{ display:'flex', flexDirection:'column', gap:20 }}>
                  <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, padding:24 }}>
                    <div style={{ fontSize:14, fontWeight:600, color:T.text, marginBottom:14 }}>Спосіб доставки за замовчуванням</div>
                    <div style={{ display:'flex', flexDirection:'column', gap:8 }}>
                      {['Нова Пошта','Укрпошта','Justin','Meest','Самовивіз','Кур\'єр'].map((d,i)=>(
                        <label key={d} style={{ display:'flex', alignItems:'center', gap:10, cursor:'pointer', fontSize:13, color:T.text2 }}>
                          <div style={{ width:16, height:16, borderRadius:'50%', border:`1.5px solid ${i===0?T.invBg:T.border2}`,
                            flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center' }}>
                            {i===0 && <div style={{ width:8, height:8, borderRadius:'50%', background:T.invBg }}/>}
                          </div>
                          {d}
                        </label>
                      ))}
                    </div>
                  </div>
                  <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, padding:24 }}>
                    <div style={{ fontSize:14, fontWeight:600, color:T.text, marginBottom:14 }}>Спосіб оплати</div>
                    <div style={{ display:'flex', flexDirection:'column', gap:8 }}>
                      {['Картка онлайн','Безготівковий розрахунок','Оплата при отриманні'].map((p,i)=>(
                        <label key={p} style={{ display:'flex', alignItems:'center', gap:10, cursor:'pointer', fontSize:13, color:T.text2 }}>
                          <div style={{ width:16, height:16, borderRadius:'50%', border:`1.5px solid ${i===0?T.invBg:T.border2}`,
                            flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center' }}>
                            {i===0 && <div style={{ width:8, height:8, borderRadius:'50%', background:T.invBg }}/>}
                          </div>
                          {p}
                        </label>
                      ))}
                    </div>
                  </div>
                </div>
              </div>
              <div style={{ marginTop:20 }}><Btn>Зберегти зміни</Btn></div>
            </div>
          )}

          {/* SETTINGS */}
          {sec==='settings' && (
            <div>
              <h2 style={sectionTitle(T)}>Налаштування</h2>
              <div style={{ background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg, padding:24, marginTop:20, maxWidth:560 }}>
                {[
                  ['Email-сповіщення','Отримувати листи про статус замовлень'],
                  ['SMS-сповіщення','Сповіщення про доставку по SMS'],
                  ['Розсилка акцій','Новини, акції та спецпропозиції'],
                  ['Двофакторна автентифікація','Додатковий захист акаунту'],
                ].map(([t,d],i,a)=>(
                  <div key={t} style={{ display:'flex', justifyContent:'space-between', alignItems:'center',
                    padding:'14px 0', borderBottom:i<a.length-1?`1px solid ${T.border}`:'none' }}>
                    <div>
                      <div style={{ fontSize:14, fontWeight:500, color:T.text }}>{t}</div>
                      <div style={{ fontSize:12, color:T.text2, marginTop:2 }}>{d}</div>
                    </div>
                    <ToggleSwitch on={i<2}/>
                  </div>
                ))}
              </div>
              <div style={{ marginTop:20, display:'flex', gap:10 }}>
                <Btn variant="outline">Змінити пароль</Btn>
                <Btn variant="ghost" style={{ color:T.danger }}>Видалити акаунт</Btn>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

function ToggleSwitch({ on }) {
  const { T } = useContext(ThemeCtx);
  const [active, setActive] = useState(on);
  return (
    <div onClick={()=>setActive(!active)}
      style={{ width:40, height:22, borderRadius:T.rPill, background: active?T.invBg:T.border2,
        cursor:'pointer', transition:'background 0.2s', position:'relative', flexShrink:0 }}>
      <div style={{ width:16, height:16, borderRadius:'50%', background:'#fff', position:'absolute',
        top:3, left: active?21:3, transition:'left 0.2s', boxShadow:'0 1px 2px rgba(0,0,0,0.2)' }}/>
    </div>
  );
}

function sectionTitle(T) {
  return { fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
    marginBottom:8, fontFamily:'Geist, Inter, sans-serif' };
}

Object.assign(window, { ClientCabinet, StatusPill });
