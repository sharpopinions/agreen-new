// ── EXTRA PAGES 3 ─────────────────────────────────────────────────
// Brand-Products, Extended Dashboard (orders, documents, rules, drop)

// ── BRAND PRODUCTS PAGE ───────────────────────────────────────────
function BrandProductsPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const brand = 'Tork';
  const [selCat, setSelCat] = useState(null);
  const [priceFrom, setPriceFrom] = useState('');
  const [priceTo, setPriceTo] = useState('');
  const [onlyPromo, setOnlyPromo] = useState(false);
  const [onlyOrder, setOnlyOrder] = useState(false);
  const [sort, setSort] = useState('popular');
  const [view, setView] = useState('grid');

  const brandCats = [
    { id:1, name:'Полотенця для рук',      count:937 },
    { id:2, name:'Туалетний папір',        count:182 },
    { id:3, name:'Рідке мило',             count:182 },
    { id:4, name:'Серветки для обличчя',   count:182 },
    { id:5, name:'Освіжувачі повітря',     count:182 },
    { id:6, name:'Покриття на унітаз',     count:182 },
    { id:7, name:'Інша категорія',         count:182 },
  ];

  const otherBrands = BRANDS.filter(b => b !== brand).slice(0,4);
  const products = PRODUCTS.filter(p => onlyPromo ? (p.badge==='Акція'||p.oldPrice) : true);

  return (
    <div style={{ background: T.bg, padding:'28px 60px' }}>
      <Crumbs items={['Бренди', `Продукція ${brand}`]} setPage={setPage}/>

      {/* Brand hero */}
      <div style={{ background: T.surface, border:`1px solid ${T.border}`, borderRadius: T.rLg,
        padding:'32px 40px', marginBottom:28,
        display:'grid', gridTemplateColumns:'120px 1fr 280px', gap:32, alignItems:'center' }}>
        {/* Logo */}
        <div style={{ width:120, height:80, background: T.bgAlt, border:`1px solid ${T.border}`,
          borderRadius: T.rLg, display:'flex', alignItems:'center', justifyContent:'center',
          fontSize:24, fontWeight:800, color: T.text2, letterSpacing:'-0.02em' }}>
          {brand}
        </div>
        {/* Info */}
        <div>
          <h1 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
            marginBottom:8, fontFamily:'Geist, Inter, sans-serif' }}>
            Продукція {brand}
          </h1>
          <p style={{ fontSize:13, color: T.text2, lineHeight:1.7, maxWidth:520 }}>
            {brand} — світовий лідер у сфері гігієнічних рішень для бізнесу. Продукція представлена
            у більш ніж 110 країнах. Системний підхід до гігієни в офісах, виробництвах та
            громадських місцях.
          </p>
        </div>
        {/* Stats */}
        <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:12 }}>
          {[['1 483','Товарів'],['110+','Країн'],['150+','Років',''],['Top 1','В категорії']].map(([n,l])=>(
            <div key={l} style={{ background: T.bgAlt, border:`1px solid ${T.border}`,
              borderRadius: T.rSm, padding:'12px 14px', textAlign:'center' }}>
              <div style={{ fontSize:20, fontWeight:800, color: T.text,
                letterSpacing:'-0.03em', fontFamily:'Geist, Inter, sans-serif' }}>{n}</div>
              <div style={{ fontSize:11, color: T.text3, marginTop:2 }}>{l}</div>
            </div>
          ))}
        </div>
      </div>

      <div style={{ display:'grid', gridTemplateColumns:'260px 1fr', gap:20 }}>
        {/* Filter sidebar */}
        <div style={{ background: T.surface, border:`1px solid ${T.border}`,
          borderRadius: T.rLg, overflow:'hidden', height:'fit-content' }}>

          {/* Header */}
          <div style={{ padding:'14px 18px', borderBottom:`1px solid ${T.border}`,
            fontSize:14, fontWeight:600, color: T.text }}>
            Фільтри
          </div>

          {/* Price */}
          <div style={{ borderBottom:`1px solid ${T.border}` }}>
            <div style={{ padding:'12px 18px', fontSize:13, fontWeight:600, color: T.text }}>Ціна, ₴</div>
            <div style={{ padding:'0 18px 14px', display:'flex', gap:8 }}>
              <input value={priceFrom} onChange={e=>setPriceFrom(e.target.value)} placeholder="від"
                style={{ flex:1, background: T.bgAlt, border:`1px solid ${T.border}`,
                  borderRadius: T.rSm, padding:'7px 10px', color: T.text, fontSize:13,
                  outline:'none', minWidth:0, fontFamily:'Geist, Inter, sans-serif' }}/>
              <span style={{ display:'flex', alignItems:'center', color: T.text3, fontSize:13 }}>—</span>
              <input value={priceTo} onChange={e=>setPriceTo(e.target.value)} placeholder="до"
                style={{ flex:1, background: T.bgAlt, border:`1px solid ${T.border}`,
                  borderRadius: T.rSm, padding:'7px 10px', color: T.text, fontSize:13,
                  outline:'none', minWidth:0, fontFamily:'Geist, Inter, sans-serif' }}/>
            </div>
          </div>

          {/* Наявність */}
          <div style={{ borderBottom:`1px solid ${T.border}` }}>
            <div style={{ padding:'12px 18px', fontSize:13, fontWeight:600, color: T.text }}>Наявність</div>
            <div style={{ padding:'0 18px 14px', display:'flex', flexDirection:'column', gap:8 }}>
              {[
                ['Є в наявності', false, ()=>{}],
                ['Під замовлення', onlyOrder, ()=>setOnlyOrder(!onlyOrder)],
              ].map(([l, active, toggle]) => (
                <div key={l} onClick={toggle}
                  style={{ display:'flex', alignItems:'center', gap:10, cursor:'pointer',
                    fontSize:13, color: active ? T.text : T.text2 }}>
                  <div style={{ width:16, height:16, border:`1.5px solid ${active?T.invBg:T.border2}`,
                    borderRadius:4, background: active?T.invBg:'transparent', flexShrink:0,
                    display:'flex', alignItems:'center', justifyContent:'center', transition:'all 0.15s' }}>
                    {active && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                      <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                    </svg>}
                  </div>
                  {l}
                </div>
              ))}
            </div>
          </div>

          {/* Акції */}
          <div style={{ borderBottom:`1px solid ${T.border}` }}>
            <div style={{ padding:'12px 18px', fontSize:13, fontWeight:600, color: T.text }}>Акції</div>
            <div style={{ padding:'0 18px 14px' }}>
              <div onClick={()=>setOnlyPromo(!onlyPromo)}
                style={{ display:'flex', alignItems:'center', gap:10, cursor:'pointer',
                  fontSize:13, color: onlyPromo ? T.text : T.text2 }}>
                <div style={{ width:16, height:16, border:`1.5px solid ${onlyPromo?T.invBg:T.border2}`,
                  borderRadius:4, background: onlyPromo?T.invBg:'transparent', flexShrink:0,
                  display:'flex', alignItems:'center', justifyContent:'center', transition:'all 0.15s' }}>
                  {onlyPromo && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                    <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                  </svg>}
                </div>
                Показати лише акційні пропозиції
              </div>
            </div>
          </div>

          {/* Категорія */}
          <div style={{ borderBottom:`1px solid ${T.border}` }}>
            <div style={{ padding:'12px 18px', fontSize:13, fontWeight:600, color: T.text }}>Категорія</div>
            <div style={{ padding:'0 18px 14px' }}>
              {brandCats.map(cat=>(
                <div key={cat.id} onClick={()=>setSelCat(selCat===cat.id?null:cat.id)}
                  style={{ display:'flex', justifyContent:'space-between', alignItems:'center',
                    padding:'5px 0', cursor:'pointer', fontSize:13,
                    color: selCat===cat.id ? T.text : T.text2 }}>
                  <div style={{ display:'flex', alignItems:'center', gap:8 }}>
                    <div style={{ width:14, height:14, border:`1px solid ${selCat===cat.id?T.invBg:T.border2}`,
                      borderRadius:3, background: selCat===cat.id?T.invBg:'transparent',
                      flexShrink:0, transition:'all 0.15s' }}/>
                    {cat.name}
                  </div>
                  <span style={{ fontSize:11, color: T.text3 }}>({cat.count})</span>
                </div>
              ))}
            </div>
          </div>

          {/* Інші бренди */}
          <div>
            <div style={{ padding:'12px 18px', fontSize:13, fontWeight:600, color: T.text }}>Інші бренди</div>
            <div style={{ padding:'0 18px 16px', display:'flex', flexDirection:'column', gap:6 }}>
              {otherBrands.map(b=>(
                <div key={b} onClick={()=>setPage('brand-products')}
                  style={{ fontSize:13, color: T.text2, cursor:'pointer', padding:'3px 0',
                    transition:'color 0.12s' }}
                  onMouseEnter={e=>e.target.style.color=T.text}
                  onMouseLeave={e=>e.target.style.color=T.text2}>
                  {b}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Products */}
        <div>
          {/* Toolbar */}
          <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:16 }}>
            <div style={{ fontSize:14, color: T.text2 }}>
              Знайдено: <strong style={{ color: T.text }}>{products.length}</strong> товарів
              {selCat && <span style={{ color: T.text2 }}> · {brandCats.find(c=>c.id===selCat)?.name}</span>}
            </div>
            <div style={{ display:'flex', gap:8 }}>
              <select value={sort} onChange={e=>setSort(e.target.value)}
                style={{ background: T.surface, border:`1px solid ${T.border}`,
                  borderRadius: T.rSm, padding:'7px 10px', color: T.text2, fontSize:13,
                  outline:'none', fontFamily:'Geist, Inter, sans-serif', cursor:'pointer' }}>
                <option value="popular">Популярні</option>
                <option value="asc">Ціна ↑</option>
                <option value="desc">Ціна ↓</option>
                <option value="rating">Рейтинг</option>
              </select>
              <div style={{ display:'flex', gap:2, background: T.muted, borderRadius: T.rSm,
                border:`1px solid ${T.border}`, padding:3 }}>
                {['grid','list'].map(v=>(
                  <button key={v} onClick={()=>setView(v)}
                    style={{ padding:'5px 8px', borderRadius: T.rSm,
                      background: view===v ? T.surface : 'transparent',
                      border: view===v ? `1px solid ${T.border}` : '1px solid transparent',
                      cursor:'pointer', color: view===v ? T.text : T.text2,
                      boxShadow: view===v ? T.shadow : 'none', transition:'all 0.12s' }}>
                    {v==='grid'
                      ? <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/><rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg>
                      : <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor"><rect x="1" y="2" width="14" height="2.5" rx="1"/><rect x="1" y="6.75" width="14" height="2.5" rx="1"/><rect x="1" y="11.5" width="14" height="2.5" rx="1"/></svg>}
                  </button>
                ))}
              </div>
            </div>
          </div>

          {view==='grid' ? (
            <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:16 }}>
              {products.map(p=>(
                <ProductCard key={p.id} product={p} onView={()=>setPage('product')} onAdd={addToCart}/>
              ))}
            </div>
          ) : (
            <div style={{ display:'flex', flexDirection:'column', gap:10 }}>
              {products.map(p=>(
                <div key={p.id} onClick={()=>setPage('product')}
                  style={{ background: T.surface, border:`1px solid ${T.border}`,
                    borderRadius: T.rLg, padding:16, display:'flex', gap:16,
                    cursor:'pointer', transition:'all 0.15s', boxShadow: T.shadow }}
                  onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.boxShadow=T.shadowMd;}}
                  onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.boxShadow=T.shadow;}}>
                  <div style={{ flexShrink:0, borderRadius: T.rSm, overflow:'hidden', width:90 }}>
                    <Img h={90} label=""/>
                  </div>
                  <div style={{ flex:1 }}>
                    <div style={{ fontSize:12, color: T.text3, marginBottom:4 }}>Арт: {p.sku}</div>
                    <div style={{ fontSize:14, color: T.text, lineHeight:1.5, marginBottom:6 }}>{p.name}</div>
                    <Stars rating={p.rating}/>
                  </div>
                  <div style={{ display:'flex', flexDirection:'column', justifyContent:'space-between',
                    alignItems:'flex-end', minWidth:140 }}>
                    <div style={{ fontSize:17, fontWeight:700, color: T.text,
                      letterSpacing:'-0.025em' }}>{fmt(p.price)}</div>
                    <Btn size="sm" onClick={e=>{e.stopPropagation();addToCart(p);}}>До кошика</Btn>
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

// ── EXTENDED DASHBOARD ────────────────────────────────────────────
function ExtendedDashboard({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [sec, setSec] = useState('catalog');

  // Dashboard nav items — matches Figma menu-dashboard
  const navItems = [
    { key:'catalog',   icon:'⊞', label:'Каталог'         },
    { key:'orders',    icon:'◫', label:'Замовлення'       },
    { key:'drop',      icon:'📦', label:'Дропшипінг'      },
    { key:'documents', icon:'⊟', label:'Документи'       },
    { key:'rules',     icon:'◈', label:'Знижки'          },
    { key:'profile',   icon:'◎', label:'Профіль'         },
    { key:'support',   icon:'◉', label:'Підтримка'       },
    { key:'study',     icon:'🎓', label:'Навч. центр'     },
  ];

  const ORDERS = [
    { id:'#A-2026-04220', date:'22 квіт 2026', status:'processing', total:3297, items:3,
      delivery:'Нова Пошта', track:'59000123456789' },
    { id:'#A-2026-04100', date:'10 квіт 2026', status:'delivered',  total:1099, items:1,
      delivery:'Нова Пошта', track:'59000987654321' },
    { id:'#A-2026-03880', date:'28 бер 2026',  status:'delivered',  total:2450, items:4,
      delivery:'Кур\'єр',   track:'' },
    { id:'#A-2026-03150', date:'15 бер 2026',  status:'delivered',  total:5640, items:7,
      delivery:'Самовивіз', track:'' },
    { id:'#A-2026-02900', date:'01 бер 2026',  status:'delivered',  total:890,  items:2,
      delivery:'Укрпошта',  track:'0103219876543' },
  ];

  const DOCS = [
    { id:1, name:'Рахунок-фактура #2026-04', date:'22 квіт 2026', type:'Рахунок', size:'128 KB' },
    { id:2, name:'Акт виконаних робіт #2026-04', date:'22 квіт 2026', type:'Акт', size:'96 KB' },
    { id:3, name:'Прайс-лист партнера (квітень 2026)', date:'01 квіт 2026', type:'Прайс', size:'2.4 MB' },
    { id:4, name:'Договір поставки #2025-001', date:'15 січ 2025', type:'Договір', size:'512 KB' },
    { id:5, name:'Видаткова накладна #2026-03', date:'15 бер 2026', type:'Накладна', size:'84 KB' },
    { id:6, name:'Сертифікати якості (Kimberly-Clark)', date:'10 лют 2026', type:'Сертифікат', size:'3.1 MB' },
  ];

  const DISCOUNTS = [
    { name:'Базова знижка', pct:15, from:'01 січ 2026', desc:'На весь асортимент' },
    { name:'Персональна знижка #1', pct:20, from:'01 квіт 2026', desc:'Абразивні матеріали Mirka' },
    { name:'Персональна знижка #2', pct:25, from:'15 квіт 2026', desc:'Дозуюче обладнання Kimberly-Clark' },
  ];

  const [orderFilter, setOrderFilter] = useState('all');
  const [docSearch, setDocSearch] = useState('');
  const [openOrder, setOpenOrder] = useState(null);
  const [dropForm, setDropForm] = useState({ sender:'', recipient:'', city:'', branch:'', phone:'' });
  const updDrop = (k,v) => setDropForm(f=>({...f,[k]:v}));

  const filteredOrders = orderFilter==='all' ? ORDERS : ORDERS.filter(o=>o.status===orderFilter);
  const filteredDocs   = DOCS.filter(d => d.name.toLowerCase().includes(docSearch.toLowerCase()));

  return (
    <div style={{ background: T.bg, minHeight:'calc(100vh - 52px)',
      display:'grid', gridTemplateColumns:'220px 1fr' }}>

      {/* Sidebar */}
      <div style={{ background: T.surface, borderRight:`1px solid ${T.border}`,
        padding:'20px 0', display:'flex', flexDirection:'column' }}>
        <div style={{ padding:'0 16px 20px', borderBottom:`1px solid ${T.border}`, marginBottom:8 }}>
          <div style={{ width:44, height:44, background: T.muted, borderRadius: T.rLg,
            display:'flex', alignItems:'center', justifyContent:'center', marginBottom:10, fontSize:20 }}>
            👤
          </div>
          <div style={{ fontSize:13, fontWeight:600, color: T.text, letterSpacing:'-0.01em' }}>
            ТОВ "Авто Сервіс"
          </div>
          <div style={{ fontSize:11, color: T.text3, marginTop:2 }}>Бізнес-партнер · Рівень Gold</div>
          {/* Discount badge */}
          <div style={{ marginTop:8, padding:'4px 10px', background: T.bg==='#09090b'?'#052e16':'#f0fdf4',
            border:`1px solid ${T.bg==='#09090b'?'#14532d':'#bbf7d0'}`,
            borderRadius: T.rPill, display:'inline-flex', alignItems:'center', gap:6 }}>
            <span style={{ fontSize:11, fontWeight:600, color:'#16a34a' }}>−15%</span>
            <span style={{ fontSize:10, color: T.bg==='#09090b'?'#4ade80':'#15803d' }}>базова знижка</span>
          </div>
        </div>
        <div style={{ flex:1 }}>
          {navItems.map(item=>(
            <div key={item.key} onClick={()=>setSec(item.key)}
              style={{ padding:'9px 16px', fontSize:14, cursor:'pointer',
                fontWeight: sec===item.key ? 500 : 400,
                color: sec===item.key ? T.text : T.text2,
                background: sec===item.key ? T.muted : 'transparent',
                borderLeft:`2px solid ${sec===item.key ? T.invBg : 'transparent'}`,
                transition:'all 0.12s', display:'flex', alignItems:'center', gap:10 }}>
              <span style={{ fontSize:15 }}>{item.icon}</span>
              {item.label}
            </div>
          ))}
        </div>
        <div style={{ padding:'16px', borderTop:`1px solid ${T.border}` }}>
          <span onClick={()=>setPage('main')} style={{ fontSize:13, color: T.text3, cursor:'pointer',
            transition:'color 0.12s' }}
            onMouseEnter={e=>e.target.style.color=T.text2}
            onMouseLeave={e=>e.target.style.color=T.text3}>
            ← Магазин
          </span>
        </div>
      </div>

      {/* Content area */}
      <div style={{ padding:32, overflow:'auto' }}>

        {/* ── CATALOG ── */}
        {sec==='catalog' && (
          <div>
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:24 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Каталог</h2>
              <span style={{ padding:'4px 12px', background: T.muted, border:`1px solid ${T.border}`,
                borderRadius: T.rPill, fontSize:11, fontWeight:600, color: T.text2,
                textTransform:'uppercase', letterSpacing:'0.08em' }}>
                Партнерські ціни
              </span>
            </div>
            <div style={{ position:'relative', marginBottom:24 }}>
              <svg style={{ position:'absolute', left:12, top:'50%', transform:'translateY(-50%)', color: T.text3 }}
                width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
              </svg>
              <input placeholder="Пошук по каталогу..."
                style={{ width:'100%', background: T.surface, border:`1px solid ${T.border}`,
                  borderRadius: T.rSm, padding:'10px 16px 10px 38px',
                  color: T.text, fontSize:14, outline:'none', fontFamily:'Geist, Inter, sans-serif' }}/>
            </div>
            <div style={{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:12 }}>
              {CATEGORIES.map(cat=>(
                <CatCard key={cat.id} cat={cat} onSelect={()=>setPage('brand-products')}/>
              ))}
            </div>
          </div>
        )}

        {/* ── ORDERS ── */}
        {sec==='orders' && (
          <div>
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:20 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Замовлення</h2>
              <div style={{ display:'flex', gap:4, background: T.muted, border:`1px solid ${T.border}`,
                borderRadius: T.rSm, padding:3 }}>
                {[['all','Всі'],['processing','В обробці'],['delivered','Доставлено']].map(([k,l])=>(
                  <button key={k} onClick={()=>setOrderFilter(k)}
                    style={{ padding:'6px 14px', borderRadius: T.rSm, fontSize:12, fontWeight: orderFilter===k?500:400,
                      background: orderFilter===k ? T.surface : 'transparent',
                      border: orderFilter===k ? `1px solid ${T.border}` : '1px solid transparent',
                      color: orderFilter===k ? T.text : T.text2, cursor:'pointer',
                      fontFamily:'Geist, Inter, sans-serif', transition:'all 0.12s' }}>
                    {l}
                  </button>
                ))}
              </div>
            </div>

            <div style={{ display:'flex', flexDirection:'column', gap:8 }}>
              {filteredOrders.map(o=>(
                <div key={o.id} style={{ background: T.surface, border:`1px solid ${openOrder===o.id?T.border2:T.border}`,
                  borderRadius: T.rLg, overflow:'hidden', transition:'all 0.15s', boxShadow: T.shadow }}>
                  {/* Row */}
                  <div onClick={()=>setOpenOrder(openOrder===o.id?null:o.id)}
                    style={{ padding:'14px 20px', display:'flex', alignItems:'center', gap:16, cursor:'pointer' }}>
                    <div style={{ flex:1 }}>
                      <div style={{ fontSize:14, fontWeight:600, color: T.text }}>{o.id}</div>
                      <div style={{ fontSize:12, color: T.text2, marginTop:2 }}>
                        {o.date} · {o.items} {o.items===1?'товар':'товари'} · {o.delivery}
                      </div>
                    </div>
                    <span style={{ padding:'4px 12px', borderRadius: T.rPill, fontSize:12, fontWeight:500,
                      background: o.status==='delivered'?(T.bg==='#09090b'?'#052e16':'#f0fdf4'):T.muted,
                      color: o.status==='delivered'?'#16a34a':T.text2,
                      border:`1px solid ${o.status==='delivered'?(T.bg==='#09090b'?'#14532d':'#bbf7d0'):T.border}` }}>
                      {o.status==='delivered' ? 'Доставлено' : 'В обробці'}
                    </span>
                    <div style={{ fontSize:16, fontWeight:700, color: T.text,
                      letterSpacing:'-0.025em', minWidth:100, textAlign:'right' }}>
                      {fmt(o.total)}
                    </div>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2"
                      style={{ transform: openOrder===o.id?'rotate(180deg)':'none', transition:'transform 0.2s', flexShrink:0 }}>
                      <path d="m6 9 6 6 6-6"/>
                    </svg>
                  </div>
                  {/* Expanded */}
                  {openOrder===o.id && (
                    <div style={{ borderTop:`1px solid ${T.border}`, padding:'16px 20px',
                      display:'grid', gridTemplateColumns:'1fr 1fr', gap:20 }}>
                      <div>
                        <div style={{ fontSize:12, fontWeight:600, color: T.text2,
                          textTransform:'uppercase', letterSpacing:'0.08em', marginBottom:12 }}>
                          Товари
                        </div>
                        {PRODUCTS.slice(0, o.items).map(p=>(
                          <div key={p.id} style={{ display:'flex', gap:10, marginBottom:10,
                            paddingBottom:10, borderBottom:`1px solid ${T.border}` }}>
                            <div style={{ width:48, height:48, flexShrink:0, borderRadius: T.rSm,
                              overflow:'hidden' }}><Img h={48} label=""/></div>
                            <div style={{ flex:1 }}>
                              <div style={{ fontSize:12, color: T.text, lineHeight:1.4 }}>{p.name}</div>
                              <div style={{ fontSize:11, color: T.text3, marginTop:2 }}>1 шт · {fmt(p.price)}</div>
                            </div>
                          </div>
                        ))}
                      </div>
                      <div>
                        <div style={{ fontSize:12, fontWeight:600, color: T.text2,
                          textTransform:'uppercase', letterSpacing:'0.08em', marginBottom:12 }}>
                          Доставка
                        </div>
                        <div style={{ background: T.bgAlt, border:`1px solid ${T.border}`,
                          borderRadius: T.rSm, padding:'12px 14px', marginBottom:10 }}>
                          <div style={{ fontSize:13, fontWeight:500, color: T.text,
                            marginBottom:4 }}>{o.delivery}</div>
                          {o.track && <div style={{ fontSize:12, color: T.text2 }}>
                            ТТН: <span style={{ fontFamily:'monospace' }}>{o.track}</span>
                          </div>}
                        </div>
                        <div style={{ display:'flex', gap:8 }}>
                          <Btn variant="outline" size="sm">Повторити замовлення</Btn>
                          {o.status==='delivered' && <Btn variant="ghost" size="sm">Повернення</Btn>}
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ── DROP ── */}
        {sec==='drop' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
              marginBottom:8, fontFamily:'Geist, Inter, sans-serif' }}>Дропшипінг</h2>
            <p style={{ fontSize:14, color: T.text2, marginBottom:24, lineHeight:1.6 }}>
              Оформіть замовлення напряму від імені вашого клієнта. Відправник — A-green, отримувач — ваш клієнт.
            </p>
            <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:20 }}>
              {/* Відправка від */}
              <div style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding:24 }}>
                <div style={{ fontSize:12, fontWeight:600, color: T.text3, textTransform:'uppercase',
                  letterSpacing:'0.08em', marginBottom:14 }}>Відправка від</div>
                <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                  <div style={{ background: T.bgAlt, border:`1px solid ${T.border}`,
                    borderRadius: T.rSm, padding:'12px 14px' }}>
                    <div style={{ fontSize:14, fontWeight:600, color: T.text }}>A-green</div>
                    <div style={{ fontSize:12, color: T.text2, marginTop:2 }}>м. Київ, вул. Крайня 1</div>
                  </div>
                  <Field label="Прізвище ім'я по-батькові відправника"
                    placeholder="A-green (заповнюється автоматично)" value={dropForm.sender}
                    onChange={e=>updDrop('sender',e.target.value)}/>
                </div>
              </div>
              {/* Отримувач */}
              <div style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding:24 }}>
                <div style={{ fontSize:12, fontWeight:600, color: T.text3, textTransform:'uppercase',
                  letterSpacing:'0.08em', marginBottom:14 }}>Отримувач</div>
                <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                  <Field label="Ім'я отримувача *" placeholder="Іван Іваненко"
                    value={dropForm.recipient} onChange={e=>updDrop('recipient',e.target.value)}/>
                  <Field label="Телефон *" placeholder="+380 XX XXX XX XX"
                    value={dropForm.phone} onChange={e=>updDrop('phone',e.target.value)}/>
                  <Field label="Місто *" placeholder="Київ"
                    value={dropForm.city} onChange={e=>updDrop('city',e.target.value)}/>
                  <Field label="Відділення Нової Пошти *" placeholder="Відділення №5"
                    value={dropForm.branch} onChange={e=>updDrop('branch',e.target.value)}/>
                </div>
              </div>
            </div>

            {/* Товари */}
            <div style={{ background: T.surface, border:`1px solid ${T.border}`,
              borderRadius: T.rLg, padding:24, marginTop:16 }}>
              <div style={{ fontSize:14, fontWeight:600, color: T.text, marginBottom:16 }}>
                Товари для відправки
              </div>
              <div style={{ display:'flex', flexDirection:'column', gap:8, marginBottom:16 }}>
                {PRODUCTS.slice(0,2).map(p=>(
                  <div key={p.id} style={{ display:'flex', gap:12, alignItems:'center',
                    padding:'12px 14px', background: T.bgAlt, border:`1px solid ${T.border}`,
                    borderRadius: T.rSm }}>
                    <div style={{ width:48, height:48, flexShrink:0, borderRadius: T.rSm,
                      overflow:'hidden' }}><Img h={48} label=""/></div>
                    <div style={{ flex:1 }}>
                      <div style={{ fontSize:13, color: T.text }}>{p.name}</div>
                      <div style={{ fontSize:11, color: T.text3, marginTop:2 }}>Арт: {p.sku}</div>
                    </div>
                    <div style={{ display:'flex', gap:8, alignItems:'center' }}>
                      <QtyCtrl qty={1} setQty={()=>{}}/>
                      <div style={{ fontSize:14, fontWeight:700, color: T.text, minWidth:80,
                        textAlign:'right' }}>{fmt(p.price)}</div>
                    </div>
                  </div>
                ))}
              </div>
              <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center' }}>
                <span style={{ fontSize:14, color: T.text2 }}>Підсумок: <strong style={{ color: T.text }}>{fmt(PRODUCTS[0].price+PRODUCTS[1].price)}</strong></span>
                <Btn onClick={()=>setPage('thank-order')}>Оформити дропшипінг</Btn>
              </div>
            </div>
          </div>
        )}

        {/* ── DOCUMENTS ── */}
        {sec==='documents' && (
          <div>
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:20 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Документи</h2>
            </div>
            {/* Search */}
            <div style={{ position:'relative', marginBottom:20 }}>
              <svg style={{ position:'absolute', left:12, top:'50%', transform:'translateY(-50%)', color: T.text3 }}
                width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
              </svg>
              <input value={docSearch} onChange={e=>setDocSearch(e.target.value)}
                placeholder="Пошук по документах..."
                style={{ width:'100%', background: T.surface, border:`1px solid ${T.border}`,
                  borderRadius: T.rSm, padding:'10px 16px 10px 38px',
                  color: T.text, fontSize:14, outline:'none', fontFamily:'Geist, Inter, sans-serif' }}/>
            </div>

            {/* Table header */}
            <div style={{ display:'grid', gridTemplateColumns:'1fr 100px 120px 80px 80px',
              padding:'8px 16px', fontSize:12, fontWeight:600, color: T.text3,
              textTransform:'uppercase', letterSpacing:'0.08em',
              borderBottom:`1px solid ${T.border}` }}>
              <span>Назва</span><span>Тип</span><span>Дата</span>
              <span style={{ textAlign:'center' }}>Розмір</span>
              <span style={{ textAlign:'center' }}>Дії</span>
            </div>

            <div style={{ display:'flex', flexDirection:'column', gap:0 }}>
              {filteredDocs.map((d,i)=>(
                <div key={d.id} style={{ display:'grid', gridTemplateColumns:'1fr 100px 120px 80px 80px',
                  padding:'12px 16px', alignItems:'center', fontSize:13,
                  background: i%2===0 ? T.surface : T.bgAlt,
                  borderBottom:`1px solid ${T.border}`, transition:'background 0.12s',
                  cursor:'pointer' }}
                  onMouseEnter={e=>e.currentTarget.style.background=T.muted}
                  onMouseLeave={e=>e.currentTarget.style.background=i%2===0?T.surface:T.bgAlt}>
                  <div style={{ display:'flex', gap:10, alignItems:'center' }}>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.5">
                      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                      <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <span style={{ color: T.text, fontWeight:500 }}>{d.name}</span>
                  </div>
                  <span style={{ padding:'3px 8px', background: T.muted,
                    border:`1px solid ${T.border}`, borderRadius: T.rPill,
                    fontSize:11, fontWeight:500, color: T.text2 }}>{d.type}</span>
                  <span style={{ color: T.text3 }}>{d.date}</span>
                  <span style={{ color: T.text3, textAlign:'center' }}>{d.size}</span>
                  <div style={{ display:'flex', justifyContent:'center' }}>
                    <button style={{ background: T.muted, border:`1px solid ${T.border}`,
                      borderRadius: T.rSm, padding:'5px 10px', fontSize:11, color: T.text2,
                      cursor:'pointer', fontFamily:'Geist, Inter, sans-serif' }}>
                      ↓ PDF
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ── RULES / DISCOUNTS ── */}
        {sec==='rules' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
              marginBottom:8, fontFamily:'Geist, Inter, sans-serif' }}>Знижки та умови</h2>
            <p style={{ fontSize:14, color: T.text2, marginBottom:28, lineHeight:1.6 }}>
              Ваші персональні умови співпраці з A-green
            </p>

            {/* Discount tabs */}
            <div style={{ display:'flex', gap:8, marginBottom:24 }}>
              {[['discounts','Знижки'],['loyalty','Програма лояльності'],['bonus','Бонусні програми']].map(([k,l])=>(
                <button key={k} onClick={()=>{}}
                  style={{ padding:'8px 20px', borderRadius: T.rPill,
                    background: k==='discounts' ? T.invBg : T.surface,
                    border:`1px solid ${k==='discounts' ? T.invBg : T.border}`,
                    color: k==='discounts' ? T.invText : T.text2,
                    fontSize:13, fontWeight: k==='discounts'?600:400,
                    cursor:'pointer', fontFamily:'Geist, Inter, sans-serif' }}>
                  {l}
                </button>
              ))}
            </div>

            {/* Discounts grid */}
            <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:14, marginBottom:28 }}>
              {DISCOUNTS.map(d=>(
                <div key={d.name} style={{ background: T.surface, border:`1px solid ${T.border}`,
                  borderRadius: T.rLg, padding:24, boxShadow: T.shadow }}>
                  <div style={{ display:'flex', justifyContent:'space-between', alignItems:'flex-start',
                    marginBottom:14 }}>
                    <div style={{ fontSize:13, fontWeight:600, color: T.text }}>{d.name}</div>
                    <div style={{ fontSize:28, fontWeight:800, color:'#16a34a',
                      letterSpacing:'-0.04em', fontFamily:'Geist, Inter, sans-serif' }}>
                      −{d.pct}%
                    </div>
                  </div>
                  <div style={{ fontSize:12, color: T.text2, marginBottom:6 }}>{d.desc}</div>
                  <div style={{ fontSize:11, color: T.text3 }}>Діє з {d.from}</div>
                </div>
              ))}
            </div>

            {/* Pagination */}
            <div style={{ display:'flex', justifyContent:'center', gap:4 }}>
              {[1,2,3,'...',100].map((p,i)=>(
                <button key={i} style={{ width:32, height:32, background: p===1?T.invBg:T.surface,
                  border:`1px solid ${T.border}`, borderRadius: T.rSm,
                  color: p===1?T.invText:T.text2, fontSize:13, cursor:'pointer',
                  fontFamily:'Geist, Inter, sans-serif' }}>{p}</button>
              ))}
              <button style={{ height:32, padding:'0 12px', background: T.surface,
                border:`1px solid ${T.border}`, borderRadius: T.rSm,
                color: T.text2, fontSize:13, cursor:'pointer',
                fontFamily:'Geist, Inter, sans-serif' }}>→</button>
            </div>
          </div>
        )}

        {/* ── PROFILE ── */}
        {sec==='profile' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
              marginBottom:24, fontFamily:'Geist, Inter, sans-serif' }}>Профіль</h2>
            <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:20 }}>
              <div style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding:28 }}>
                <div style={{ fontSize:14, fontWeight:600, color: T.text, marginBottom:20 }}>
                  Дані компанії
                </div>
                <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                  <Field label="Назва компанії" placeholder="ТОВ 'Авто Сервіс'"/>
                  <Field label="ЄДРПОУ" placeholder="12345678"/>
                  <Field label="ІПН/КПП" placeholder="123456789"/>
                  <Field label="Юридична адреса" placeholder="02660, м. Київ, вул. Крайня 1"/>
                </div>
              </div>
              <div style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding:28 }}>
                <div style={{ fontSize:14, fontWeight:600, color: T.text, marginBottom:20 }}>
                  Контактна особа
                </div>
                <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                  <Field label="Ім'я та прізвище" placeholder="Іван Іваненко"/>
                  <Field label="Email" placeholder="ivan@company.ua" type="email"/>
                  <Field label="Телефон" placeholder="+380 XX XXX XX XX"/>
                  <Field label="Посада" placeholder="Директор"/>
                  <Btn>Зберегти зміни</Btn>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ── SUPPORT ── */}
        {sec==='support' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
              marginBottom:24, fontFamily:'Geist, Inter, sans-serif' }}>Підтримка</h2>
            <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:20 }}>
              {/* Manager */}
              <div style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding:28 }}>
                <div style={{ display:'flex', gap:14, alignItems:'center', marginBottom:20 }}>
                  <div style={{ width:56, height:56, background: T.muted, borderRadius:'50%',
                    display:'flex', alignItems:'center', justifyContent:'center', fontSize:24 }}>👩</div>
                  <div>
                    <div style={{ fontSize:15, fontWeight:600, color: T.text }}>Марина Коваленко</div>
                    <div style={{ fontSize:12, color: T.text2, marginTop:2 }}>Ваш персональний менеджер</div>
                  </div>
                </div>
                {[['Телефон','+380 44 123-45-68'],['Email','marina@a-green.com.ua'],['Час роботи','Пн–Пт 9:00–18:00']].map(([k,v])=>(
                  <div key={k} style={{ display:'flex', justifyContent:'space-between',
                    padding:'8px 0', borderBottom:`1px solid ${T.border}`, fontSize:13 }}>
                    <span style={{ color: T.text2 }}>{k}</span>
                    <span style={{ color: T.text, fontWeight:500 }}>{v}</span>
                  </div>
                ))}
                <div style={{ marginTop:16, display:'flex', gap:8 }}>
                  <Btn variant="outline" size="sm" full>📞 Зателефонувати</Btn>
                  <Btn size="sm" full>✉ Написати</Btn>
                </div>
              </div>
              {/* Ticket form */}
              <div style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding:28 }}>
                <div style={{ fontSize:14, fontWeight:600, color: T.text, marginBottom:16 }}>
                  Нове звернення
                </div>
                <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                  <Field label="Тема" placeholder="Коротко опишіть питання..."/>
                  <div>
                    <label style={{ fontSize:14, fontWeight:500, color: T.text,
                      display:'block', marginBottom:6 }}>Повідомлення</label>
                    <textarea placeholder="Детально опишіть вашу ситуацію..."
                      style={{ width:'100%', background: T.bgAlt, border:`1px solid ${T.border}`,
                        borderRadius: T.rSm, padding:'10px 12px', color: T.text, fontSize:14,
                        outline:'none', resize:'vertical', minHeight:120,
                        fontFamily:'Geist, Inter, sans-serif' }}/>
                  </div>
                  <Btn>Надіслати</Btn>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* ── STUDY ── */}
        {sec==='study' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color: T.text, letterSpacing:'-0.025em',
              marginBottom:24, fontFamily:'Geist, Inter, sans-serif' }}>Навчальний центр</h2>
            <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:12 }}>
              {[
                { title:'Робота з диспенсерами',                     type:'Відеокурс',   dur:'45 хв',   isNew:true  },
                { title:'Абразивні матеріали: вибір та застосування', type:'Вебінар',     dur:'1.5 год', isNew:false },
                { title:'Лакофарбові роботи: базовий курс',           type:'Відеокурс',   dur:'3 год',   isNew:false },
                { title:'Сертифікація технічного персоналу',          type:'Іспит',       dur:'2 год',   isNew:true  },
                { title:'Зберігання хімічних матеріалів',             type:'Документ',    dur:'15 хв',   isNew:false },
                { title:'Нове від Kimberly-Clark 2026',                type:'Презентація', dur:'30 хв',   isNew:true  },
              ].map(c=>(
                <div key={c.title} style={{ background: T.surface, border:`1px solid ${T.border}`,
                  borderRadius: T.rLg, padding:20, cursor:'pointer',
                  transition:'all 0.2s', boxShadow: T.shadow }}
                  onMouseEnter={e=>{e.currentTarget.style.borderColor=T.border2;e.currentTarget.style.boxShadow=T.shadowMd;}}
                  onMouseLeave={e=>{e.currentTarget.style.borderColor=T.border;e.currentTarget.style.boxShadow=T.shadow;}}>
                  <div style={{ display:'flex', justifyContent:'space-between', marginBottom:10 }}>
                    <span style={{ fontSize:11, fontWeight:600, color: T.text3,
                      textTransform:'uppercase', letterSpacing:'0.08em' }}>{c.type}</span>
                    {c.isNew && <Badge label="Новинка"/>}
                  </div>
                  <div style={{ fontSize:14, fontWeight:500, color: T.text,
                    lineHeight:1.5, marginBottom:10 }}>{c.title}</div>
                  <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center' }}>
                    <span style={{ fontSize:12, color: T.text3 }}>⏱ {c.dur}</span>
                    <Btn variant="ghost" size="sm">Розпочати →</Btn>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

Object.assign(window, { BrandProductsPage, ExtendedDashboard });
