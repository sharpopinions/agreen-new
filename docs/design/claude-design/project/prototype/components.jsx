// ── SHARED COMPONENTS · shadcn/ui design system ──────────────────
const { useState, useEffect, useRef, useContext, createContext } = React;

// ── THEME CONTEXT ────────────────────────────────────────────────
const ThemeCtx = createContext({ dark: false, T: {} });

function makeTheme(dark) {
  return dark ? {
    bg:      '#09090b',
    bgAlt:   '#18181b',
    surface: '#09090b',
    border:  '#27272a',
    border2: '#3f3f46',
    text:    '#fafafa',
    text2:   '#a1a1aa',
    text3:   '#71717a',
    inv:     '#fafafa',
    invBg:   '#fafafa',
    invText: '#09090b',
    muted:   '#18181b',
    mutedFg: '#a1a1aa',
    warn:    '#f59e0b',
    danger:  '#ef4444',
    shadow:  '0 1px 3px rgba(0,0,0,0.4)',
    shadowMd:'0 4px 12px rgba(0,0,0,0.5)',
    r:       '10px',
    rSm:     '8px',
    rLg:     '14px',
    rPill:   '9999px',
  } : {
    bg:      '#ffffff',
    bgAlt:   '#f4f4f5',
    surface: '#ffffff',
    border:  '#e4e4e7',
    border2: '#d4d4d8',
    text:    '#09090b',
    text2:   '#71717a',
    text3:   '#a1a1aa',
    inv:     '#09090b',
    invBg:   '#18181b',
    invText: '#fafafa',
    muted:   '#f4f4f5',
    mutedFg: '#71717a',
    warn:    '#b45309',
    danger:  '#dc2626',
    shadow:  '0 1px 3px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04)',
    shadowMd:'0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04)',
    r:       '10px',
    rSm:     '8px',
    rLg:     '14px',
    rPill:   '9999px',
  };
}

// ── DATA ─────────────────────────────────────────────────────────
const CATEGORIES = [
  { id:1, name:'Витратні матеріали',     count:1240, subs:['Ганчірки та серветки','Засоби для прибирання','Паперова продукція','Мішки та пакети'] },
  { id:2, name:'Абразивні матеріали',    count:876,  subs:['Шліфувальні диски','Абразивні круги','Шліфувальна шкурка','Полірувальні пасти'] },
  { id:3, name:'Дозуюче обладнання',     count:342,  subs:['Диспенсери','Дозатори рідини','Автоматичні системи','Аксесуари'] },
  { id:4, name:'Лакофарбові матеріали',  count:2100, subs:['Ґрунтовки','Автолаки','Фарби','Розчинники'] },
  { id:5, name:'Гігієнічна продукція',   count:560,  subs:['Мило та антисептики','Паперові рушники','Туалетний папір','Засоби гігієни'] },
  { id:6, name:'Захисні засоби',         count:430,  subs:['Рукавиці захисні','Захисні комбінезони','Маски та респіратори','Окуляри'] },
  { id:7, name:'Клеї та герметики',      count:280,  subs:['Конструкційні клеї','Силіконові герметики','Монтажна піна','Двосторонні стрічки'] },
  { id:8, name:'Полірувальне обладнання',count:195,  subs:['Полірувальні машини','Полірувальні круги','Паста для полірування','Мікрофібра'] },
];

const PRODUCTS = [
  { id:1, name:'Диспенсер паперових рушників Kimberly-Clark 9960', price:1099, oldPrice:null, rating:4.2, reviews:12, badge:'Хіт',     sku:'SE50281', catId:3, inStock:150 },
  { id:2, name:'Активатор системи дозування X Pro 5л',              price:450,  oldPrice:580,  rating:4.8, reviews:34, badge:'Акція',   sku:'AX-001',  catId:3, inStock:43  },
  { id:3, name:'Абразивний диск Mirka Abranet P120 150мм',          price:89,   oldPrice:null, rating:4.5, reviews:67, badge:null,       sku:'MA-120',  catId:2, inStock:280 },
  { id:4, name:'Захисний комбінезон DuPont Tyvek 400 XL',           price:320,  oldPrice:null, rating:4.1, reviews:23, badge:null,       sku:'DT-400',  catId:6, inStock:62  },
  { id:5, name:'Поліроль 3M Perfect-It III 1000мл',                 price:680,  oldPrice:820,  rating:4.9, reviews:89, badge:'Акція',   sku:'3M-P3',   catId:8, inStock:15  },
  { id:6, name:'Клей-герметик Sikaflex-221 чорний 300мл',           price:520,  oldPrice:null, rating:4.3, reviews:15, badge:null,       sku:'SF-221',  catId:7, inStock:78  },
  { id:7, name:'Рідке мило антибактеріальне Dettol 5л',             price:290,  oldPrice:null, rating:4.6, reviews:45, badge:'Новинка', sku:'DT-5L',   catId:5, inStock:120 },
  { id:8, name:'Ґрунтовка епоксидна Vika П-ЕФ 0.8кг',              price:185,  oldPrice:null, rating:4.0, reviews:8,  badge:null,       sku:'VP-08',   catId:4, inStock:34  },
];

const BRANDS = ['Kimberly-Clark','Mirka','DuPont','3M','Sika','Dettol','Vika','Norton','Tork','Henkel'];

function fmt(p) { return p.toLocaleString('uk-UA') + ' ₴'; }

// ── ATOMS ─────────────────────────────────────────────────────────

function Stars({ rating }) {
  const { T } = useContext(ThemeCtx);
  return (
    <span style={{ display:'inline-flex', gap:2 }}>
      {[1,2,3,4,5].map(i => (
        <svg key={i} width="12" height="12" viewBox="0 0 24 24"
          fill={i <= Math.round(rating) ? '#f59e0b' : T.border2}
          stroke={i <= Math.round(rating) ? '#f59e0b' : T.border2} strokeWidth="0">
          <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>
        </svg>
      ))}
    </span>
  );
}

function Badge({ label }) {
  const { T } = useContext(ThemeCtx);
  const cfg = {
    'Хіт':     { bg: T.invBg,               text: T.invText },
    'Акція':   { bg: '#fef2f2',              text: '#dc2626', border: '#fecaca' },
    'Новинка': { bg: '#f0fdf4',              text: '#16a34a', border: '#bbf7d0' },
  };
  const s = cfg[label] || { bg: T.muted, text: T.text2 };
  // Dark mode adjustments
  const isDark = T.bg === '#09090b';
  const bgFinal = label === 'Акція' ? (isDark ? '#450a0a' : s.bg) :
                  label === 'Новинка' ? (isDark ? '#052e16' : s.bg) : s.bg;
  const borderFinal = s.border ? (isDark ? (label==='Акція'?'#7f1d1d':'#14532d') : s.border) : 'transparent';

  return (
    <span style={{ display:'inline-flex', alignItems:'center', padding:'2px 10px',
      borderRadius: T.rPill, fontSize:12, fontWeight:500, lineHeight:1.4,
      background: bgFinal, color: s.text, border: `1px solid ${borderFinal}` }}>
      {label}
    </span>
  );
}

function Img({ h=200, label='фото товару', seed }) {
  const { T } = useContext(ThemeCtx);
  const isDark = T.bg === '#09090b';
  // Hash from label for deterministic gradient
  const s = seed ?? label;
  let hash = 0;
  for (let i = 0; i < s.length; i++) hash = ((hash << 5) - hash + s.charCodeAt(i)) | 0;
  const hue = Math.abs(hash) % 360;
  const hue2 = (hue + 40) % 360;
  // Subtle, muted gradients
  const sat = isDark ? '18%' : '28%';
  const lig1 = isDark ? '14%' : '93%';
  const lig2 = isDark ? '10%' : '88%';
  const accent = isDark ? `hsl(${hue}, 20%, 22%)` : `hsl(${hue}, 35%, 75%)`;

  // Pattern: dots scaled by hash
  const dotSize = 1.5;
  const dotGap = 14;
  const dotColor = isDark ? 'rgba(255,255,255,0.04)' : 'rgba(0,0,0,0.04)';

  return (
    <div style={{
      height: h, position:'relative', overflow:'hidden',
      background: `linear-gradient(135deg, hsl(${hue}, ${sat}, ${lig1}) 0%, hsl(${hue2}, ${sat}, ${lig2}) 100%)`,
      borderRadius:'inherit', userSelect:'none' }}>
      {/* Dot pattern */}
      <div style={{ position:'absolute', inset:0,
        backgroundImage: `radial-gradient(circle at ${dotSize}px ${dotSize}px, ${dotColor} ${dotSize}px, transparent 0)`,
        backgroundSize: `${dotGap}px ${dotGap}px`, opacity: 0.6 }}/>

      {/* Geometric shape (deterministic from hash) */}
      <svg style={{ position:'absolute', inset:0, width:'100%', height:'100%' }}
        viewBox="0 0 200 200" preserveAspectRatio="xMidYMid slice">
        {/* Soft blob */}
        <ellipse cx={50 + (Math.abs(hash) % 80)} cy={50 + (Math.abs(hash>>3) % 80)}
          rx={40 + (Math.abs(hash>>5) % 30)} ry={30 + (Math.abs(hash>>7) % 30)}
          fill={accent} opacity={isDark ? 0.5 : 0.55}/>
        {/* Secondary circle */}
        <circle cx={140 + (Math.abs(hash>>9) % 30)} cy={130 + (Math.abs(hash>>11) % 20)}
          r={20 + (Math.abs(hash>>13) % 15)}
          fill={isDark ? `hsl(${hue2}, 25%, 32%)` : `hsl(${hue2}, 40%, 80%)`}
          opacity={0.6}/>
      </svg>

      {/* Subtle grain overlay */}
      <div style={{ position:'absolute', inset:0, opacity:0.5, mixBlendMode:'overlay',
        background: isDark
          ? 'radial-gradient(ellipse at top, rgba(255,255,255,0.04), transparent 60%)'
          : 'radial-gradient(ellipse at top, rgba(255,255,255,0.6), transparent 60%)' }}/>

      {/* Label */}
      {label && (
        <div style={{ position:'absolute', bottom:8, right:10,
          fontSize:9, letterSpacing:'0.06em', color: isDark?'rgba(255,255,255,0.35)':'rgba(0,0,0,0.4)',
          fontFamily:'Geist, Inter, sans-serif', fontWeight:500,
          textTransform:'uppercase' }}>
          {label}
        </div>
      )}
    </div>
  );
}

function Btn({ children, variant='primary', onClick, full, size='md', disabled }) {
  const { T } = useContext(ThemeCtx);
  const [hov, setHov] = useState(false);
  const pad = { sm:'6px 14px', md:'9px 20px', lg:'11px 28px' }[size];
  const fs  = { sm:13, md:14, lg:15 }[size];
  const base = {
    display:'inline-flex', alignItems:'center', justifyContent:'center', gap:6,
    border:'1px solid transparent', cursor: disabled?'not-allowed':'pointer',
    fontFamily:'Geist, Inter, sans-serif', fontWeight:500, fontSize:fs,
    lineHeight:'1.43', transition:'all 0.15s', width:full?'100%':'auto',
    whiteSpace:'nowrap', padding:pad, borderRadius: T.rSm, opacity: disabled?0.5:1,
  };
  const v = {
    primary:   { background: hov ? '#27272a' : T.invBg,    color: T.invText,  borderColor: hov?'#27272a':T.invBg },
    secondary: { background: hov ? T.bgAlt : T.surface,    color: T.text,     borderColor: T.border },
    ghost:     { background: hov ? T.muted  : 'transparent',color: T.text2,   borderColor:'transparent' },
    danger:    { background: hov ? '#b91c1c' : T.danger,   color:'#fff',      borderColor:'transparent' },
    outline:   { background:'transparent',                   color: T.text,    borderColor: T.border },
  }[variant] || {};
  return (
    <button style={{...base,...v}} onClick={onClick} disabled={disabled}
      onMouseEnter={()=>setHov(true)} onMouseLeave={()=>setHov(false)}>
      {children}
    </button>
  );
}

function Field({ label, placeholder, type='text', value, onChange, required }) {
  const { T } = useContext(ThemeCtx);
  const [f, setF] = useState(false);
  return (
    <div style={{ display:'flex', flexDirection:'column', gap:6 }}>
      {label && (
        <label style={{ fontSize:14, fontWeight:500, color:T.text, lineHeight:1.43 }}>
          {label}{required && <span style={{ color:T.danger }}> *</span>}
        </label>
      )}
      <input type={type} placeholder={placeholder} value={value} onChange={onChange}
        onFocus={()=>setF(true)} onBlur={()=>setF(false)}
        style={{ background:T.surface, border:`1px solid ${f ? T.text3 : T.border}`,
          borderRadius:T.rSm, padding:'9px 12px', color:T.text, fontSize:14, lineHeight:'1.43',
          outline: f ? `2px solid ${T.invBg}` : 'none', outlineOffset:2,
          transition:'all 0.15s', fontFamily:'Geist, Inter, sans-serif',
          boxShadow: f ? `0 0 0 3px rgba(0,0,0,0.06)` : 'none' }}/>
    </div>
  );
}

function QtyCtrl({ qty, setQty }) {
  const { T } = useContext(ThemeCtx);
  const btn = {
    width:34, height:36, background:T.surface, border:`1px solid ${T.border}`,
    color:T.text2, fontSize:16, cursor:'pointer', display:'flex', alignItems:'center',
    justifyContent:'center', fontFamily:'Geist, Inter, sans-serif', transition:'background 0.12s',
  };
  return (
    <div style={{ display:'flex', alignItems:'center', borderRadius:T.rSm, overflow:'hidden',
      border:`1px solid ${T.border}`, background:T.surface }}>
      <button style={{...btn, borderRight:`1px solid ${T.border}`, borderRadius:0}}
        onClick={()=>setQty(Math.max(1,qty-1))}>−</button>
      <span style={{ width:44, textAlign:'center', fontSize:14, fontWeight:500,
        color:T.text, background:T.surface, fontFamily:'Geist, Inter, sans-serif' }}>{qty}</span>
      <button style={{...btn, borderLeft:`1px solid ${T.border}`, borderRadius:0}}
        onClick={()=>setQty(qty+1)}>+</button>
    </div>
  );
}

// ── PRODUCT CARD ──────────────────────────────────────────────────

function ProductCard({ product, onView, onAdd }) {
  const { T } = useContext(ThemeCtx);
  const [hov, setHov] = useState(false);
  const [wish, setWish] = useState(false);
  return (
    <div data-product-card onClick={()=>onView(product.id)}
      onMouseEnter={()=>setHov(true)} onMouseLeave={()=>setHov(false)}
      style={{ background:T.surface, border:`1px solid ${hov ? T.border2 : T.border}`,
        borderRadius:T.rLg, overflow:'hidden', cursor:'pointer', display:'flex',
        flexDirection:'column', transition:'all 0.2s ease',
        boxShadow: hov ? T.shadowMd : T.shadow }}>
      <div style={{ position:'relative', borderRadius:`${T.rLg} ${T.rLg} 0 0`, overflow:'hidden' }}>
        <Img h={196}/>
        {product.badge && (
          <div style={{ position:'absolute', top:10, left:10 }}><Badge label={product.badge}/></div>
        )}
        <button onClick={e=>{e.stopPropagation();setWish(!wish)}}
          style={{ position:'absolute', top:8, right:8, background:T.surface,
            border:`1px solid ${T.border}`, borderRadius:T.rSm,
            width:32, height:32, display:'flex', alignItems:'center', justifyContent:'center',
            cursor:'pointer', transition:'all 0.15s' }}>
          <svg width="14" height="14" viewBox="0 0 24 24"
            fill={wish?'#ef4444':'none'} stroke={wish?'#ef4444':T.text3} strokeWidth="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
          </svg>
        </button>
      </div>

      <div style={{ padding:'14px 14px 16px', display:'flex', flexDirection:'column', gap:8, flex:1 }}>
        <div style={{ fontSize:12, color:T.text3, fontWeight:400 }}>Арт: {product.sku}</div>
        <div style={{ fontSize:14, color:T.text, lineHeight:1.5, flex:1, fontWeight:400 }}>
          {product.name}
        </div>
        <div style={{ display:'flex', alignItems:'center', gap:6 }}>
          <Stars rating={product.rating}/>
          <span style={{ fontSize:12, color:T.text3 }}>{product.rating} ({product.reviews})</span>
        </div>
        <div style={{ display:'flex', alignItems:'baseline', gap:8 }}>
          <span style={{ fontSize:16, fontWeight:600, color:T.text, letterSpacing:'-0.025em' }}>
            {fmt(product.price)}
          </span>
          {product.oldPrice && (
            <span style={{ fontSize:13, color:T.text3, textDecoration:'line-through' }}>
              {fmt(product.oldPrice)}
            </span>
          )}
        </div>
        <div style={{ fontSize:12, color: product.inStock>20 ? '#16a34a' : T.warn,
          fontWeight:500 }}>
          {product.inStock > 0 ? `В наявності: ${product.inStock} шт` : 'Немає в наявності'}
        </div>
        <Btn variant="primary" full size="sm"
          onClick={e=>{e.stopPropagation();onAdd(product);}}>
          До кошика
        </Btn>
      </div>
    </div>
  );
}

// ── CATEGORY CARD ─────────────────────────────────────────────────

function CatCard({ cat, onSelect }) {
  const { T } = useContext(ThemeCtx);
  const [hov, setHov] = useState(false);
  return (
    <div onClick={()=>onSelect(cat)}
      onMouseEnter={()=>setHov(true)} onMouseLeave={()=>setHov(false)}
      style={{ background: hov ? T.bgAlt : T.surface,
        border:`1px solid ${hov ? T.border2 : T.border}`,
        borderRadius:T.rLg, padding:20, cursor:'pointer',
        transition:'all 0.2s ease', boxShadow: hov ? T.shadowMd : T.shadow }}>
      <div style={{ fontSize:14, fontWeight:600, color:T.text, marginBottom:4, lineHeight:1.43 }}>
        {cat.name}
      </div>
      <div style={{ fontSize:12, color:T.text3, marginBottom:14 }}>
        {cat.count.toLocaleString()} товарів
      </div>
      <div style={{ display:'flex', flexDirection:'column', gap:4 }}>
        {cat.subs.slice(0,3).map(s=>(
          <div key={s} style={{ fontSize:12, color:T.text2 }}>— {s}</div>
        ))}
      </div>
    </div>
  );
}

// ── BREADCRUMBS ───────────────────────────────────────────────────

function Crumbs({ items, setPage }) {
  const { T } = useContext(ThemeCtx);
  return (
    <div style={{ display:'flex', alignItems:'center', gap:6, fontSize:14,
      color:T.text2, marginBottom:24, flexWrap:'wrap', lineHeight:'1.43' }}>
      <span onClick={()=>setPage('main')} style={{ cursor:'pointer', color:T.text2,
        transition:'color 0.12s' }}
        onMouseEnter={e=>e.target.style.color=T.text}
        onMouseLeave={e=>e.target.style.color=T.text2}>
        Головна
      </span>
      {items.map((item,i)=>(
        <React.Fragment key={i}>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="2">
            <path d="m9 18 6-6-6-6"/>
          </svg>
          <span style={{ color: i===items.length-1 ? T.text : T.text2 }}>{item}</span>
        </React.Fragment>
      ))}
    </div>
  );
}

// ── HEADER ────────────────────────────────────────────────────────

function Header({ page, setPage, cartCount, dark, setDark, setCartDrawer, wishlist, onRemoveWish, setAuthOpen }) {
  const { T } = useContext(ThemeCtx);
  const [megaOpen, setMegaOpen] = useState(null);
  const [searchOpen, setSearchOpen] = useState(false);
  const [wishOpen, setWishOpen] = useState(false);
  const navLinks = [
    { label:'Каталог',   key:'catalog',         mega:'catalog'  },
    { label:'Бренди',    key:'brands',          mega:'brands'   },
    { label:'Послуги',   key:'services',        mega:'services' },
    { label:'Партнерам', key:'partners-popup' },
    { label:'Про нас',   key:'about',           mega:'about'    },
    { label:'Блог',      key:'blog'    },
  ];

  return (
    <header style={{ background:T.surface, borderBottom:`1px solid ${T.border}`,
      position:'sticky', top:0, zIndex:200 }}>

      {/* Topbar */}
      <div style={{ borderBottom:`1px solid ${T.border}`, padding:'6px 60px',
        display:'flex', justifyContent:'space-between', alignItems:'center' }}>
        <span style={{ fontSize:12, color:T.text3 }}>
          Пн–Пт 9:00–18:00 &nbsp;·&nbsp; +380 44 123-45-67
        </span>
        <div style={{ display:'flex', gap:14, fontSize:12, color:T.text2, alignItems:'center' }}>
          <span style={{ cursor:'pointer', transition:'color 0.12s' }}
            onMouseEnter={e=>e.target.style.color=T.text}
            onMouseLeave={e=>e.target.style.color=T.text2}
            onClick={()=>setAuthOpen ? setAuthOpen(true) : setPage('client-dashboard')}>
            Особистий кабінет
          </span>
          <span style={{ color:T.border2 }}>·</span>
          <span style={{ cursor:'pointer' }}>UKR</span>
          <span style={{ color:T.border2 }}>·</span>
          <button onClick={()=>setDark(!dark)}
            style={{ background:T.muted, border:`1px solid ${T.border}`,
              borderRadius:T.rPill, padding:'3px 12px', fontSize:11, fontWeight:500,
              color:T.text2, cursor:'pointer', fontFamily:'Geist, Inter, sans-serif',
              display:'flex', alignItems:'center', gap:6, transition:'all 0.15s' }}>
            <span style={{ fontSize:13 }}>{dark?'☀':'◑'}</span>
            {dark?'Світла':'Темна'}
          </button>
        </div>
      </div>

      {/* Main nav */}
      <div style={{ padding:'0 60px', display:'flex', alignItems:'center', height:60, gap:4 }}>
        {/* Logo */}
        <div onClick={()=>setPage('main')} style={{ cursor:'pointer', marginRight:40,
          display:'flex', alignItems:'center', gap:8 }}>
          <div style={{ width:28, height:28, background:T.invBg, borderRadius:8,
            display:'flex', alignItems:'center', justifyContent:'center' }}>
            <span style={{ fontSize:11, fontWeight:700, color:T.invText,
              fontFamily:'Geist, Inter, sans-serif', letterSpacing:'-0.02em' }}>AG</span>
          </div>
          <span style={{ fontWeight:600, fontSize:15, color:T.text, letterSpacing:'-0.025em',
            fontFamily:'Geist, Inter, sans-serif' }}>A-green</span>
        </div>

        {/* Nav links */}
        <nav style={{ display:'flex', flex:1, gap:2, alignItems:'center' }}
          onMouseLeave={()=>setMegaOpen(null)}>
          {navLinks.map(item=>(
            <div key={item.key} style={{ position:'relative' }}
              onMouseEnter={()=>setMegaOpen(item.mega || null)}>
              <button onClick={()=>{setMegaOpen(null); setPage(item.key);}}
                style={{ background: page===item.key ? T.muted : 'none',
                  border:'none', borderRadius:T.rSm,
                  color: page===item.key ? T.text : T.text2,
                  fontSize:14, fontWeight: page===item.key ? 500 : 400,
                  padding:'7px 12px', cursor:'pointer',
                  fontFamily:'Geist, Inter, sans-serif', transition:'all 0.12s',
                  display:'flex', alignItems:'center', gap:4, lineHeight:'1.43' }}
                onMouseEnter={e=>{ if(page!==item.key){e.currentTarget.style.background=T.muted;e.currentTarget.style.color=T.text;} }}
                onMouseLeave={e=>{ if(page!==item.key){e.currentTarget.style.background='none';e.currentTarget.style.color=T.text2;} }}>
                {item.label}
              {item.mega && (
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" strokeWidth="2">
                  <path d="m6 9 6 6 6-6"/>
                </svg>
              )}
              </button>
            </div>
          ))}
        </nav>

        {/* Actions */}
        <div style={{ display:'flex', alignItems:'center', gap:4 }}>
          <button onClick={()=>window.dispatchEvent(new KeyboardEvent('keydown',{key:'k',metaKey:true}))}
            style={{ display:'flex', alignItems:'center', gap:8,
              background: T.surface, border:`1px solid ${T.border}`, borderRadius:T.rSm,
              color:T.text2, cursor:'pointer', padding:'7px 12px 7px 12px',
              fontSize:13, fontFamily:'Geist, Inter, sans-serif',
              minWidth:200, transition:'all 0.12s' }}
            onMouseEnter={e=>e.currentTarget.style.borderColor=T.border2}
            onMouseLeave={e=>e.currentTarget.style.borderColor=T.border}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <span style={{ flex:1, textAlign:'left', color:T.text3 }}>Пошук...</span>
            <span style={{ padding:'1px 6px', background:T.muted, border:`1px solid ${T.border}`,
              borderRadius:3, fontSize:10, fontWeight:500, color:T.text3,
              fontFamily:'monospace' }}>⌘K</span>
          </button>
          <div style={{ position:'relative' }}
            onMouseEnter={()=>setWishOpen(true)}
            onMouseLeave={()=>setWishOpen(false)}>
            <button onClick={()=>setPage('favorites')}
              style={{ background:wishOpen?T.muted:'none', border:'none', borderRadius:T.rSm,
                color:T.text2, cursor:'pointer', padding:8, position:'relative',
                transition:'all 0.12s' }}>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
              {wishlist && wishlist.length>0 && (
                <span style={{ position:'absolute', top:3, right:3, minWidth:14, height:14,
                  background:T.danger, color:'#fff', borderRadius:T.rPill,
                  fontSize:9, fontWeight:700, display:'flex', alignItems:'center',
                  justifyContent:'center', padding:'0 4px', lineHeight:1 }}>
                  {wishlist.length}
                </span>
              )}
            </button>
            {wishOpen && (
              <FavoritePopup items={wishlist || []} setPage={setPage}
                onRemove={onRemoveWish || (()=>{})}
                onClose={()=>setWishOpen(false)}/>
            )}
          </div>
          <button onClick={()=>setPage('compare')}
            style={{ background:'none', border:'none', borderRadius:T.rSm,
              color:T.text2, cursor:'pointer', padding:8, transition:'all 0.12s' }}
            onMouseEnter={e=>e.currentTarget.style.background=T.muted}
            onMouseLeave={e=>e.currentTarget.style.background='none'}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"/>
            </svg>
          </button>
          <button onClick={()=>setCartDrawer ? setCartDrawer(true) : setPage('cart')}
            style={{ display:'flex', alignItems:'center', gap:8, marginLeft:4,
              background: cartCount>0 ? T.invBg : T.surface,
              border:`1px solid ${cartCount>0 ? T.invBg : T.border}`,
              borderRadius:T.rSm, padding:'8px 16px',
              color: cartCount>0 ? T.invText : T.text,
              cursor:'pointer', fontSize:14, fontWeight:500,
              fontFamily:'Geist, Inter, sans-serif', transition:'all 0.2s',
              boxShadow: cartCount>0 ? T.shadow : 'none' }}>            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
              <line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            Кошик {cartCount>0 && (
              <span style={{ background:T.surface, color:T.invBg, borderRadius:T.rPill,
                fontSize:11, fontWeight:600, padding:'1px 7px', minWidth:20, textAlign:'center' }}>
                {cartCount}
              </span>
            )}
          </button>
        </div>
      </div>

      {/* Mega menu (full-width) */}
      {megaOpen && (
        <div onMouseEnter={()=>setMegaOpen(megaOpen)}
          onMouseLeave={()=>setMegaOpen(null)}>
          <MegaMenu kind={megaOpen} setPage={setPage}
            onClose={()=>setMegaOpen(null)}/>
        </div>
      )}

      {/* Search bar */}
      {searchOpen && (
        <div style={{ padding:'10px 60px', borderTop:`1px solid ${T.border}`,
          background:T.surface }}>
          <div style={{ position:'relative' }}>
            <svg style={{ position:'absolute', left:12, top:'50%', transform:'translateY(-50%)',
              color:T.text3, pointerEvents:'none' }}
              width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input autoFocus placeholder="Назва товару, артикул або категорія..."
              style={{ width:'100%', background:T.bgAlt, border:`1px solid ${T.border}`,
                borderRadius:T.rSm, padding:'10px 16px 10px 40px',
                color:T.text, fontSize:14, outline:'none',
                fontFamily:'Geist, Inter, sans-serif' }}
              onKeyDown={e=>e.key==='Escape'&&setSearchOpen(false)}/>
          </div>
        </div>
      )}
    </header>
  );
}

// ── FOOTER ────────────────────────────────────────────────────────

function Footer({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const cols = [
    { title:'Каталог',  links:['Витратні матеріали','Абразивні матеріали','Дозуюче обладнання','Лакофарбові матеріали'], pages:['catalog','catalog','catalog','catalog'] },
    { title:'Послуги',  links:['Навчальний центр','Технічна підтримка','Проектування','Обслуговування'],                pages:['services','services','services','services'] },
    { title:'Компанія', links:['Про нас','Партнерство','Бренди','Блог та новини','Вакансії'],                           pages:['about','partners-popup','brands','blog','vacancies'] },
    { title:'Клієнтам', links:['Доставка та оплата','Повернення та обмін','Публічна оферта','Конфіденційність'],        pages:['delivery','return','public-offer','privacy'] },
  ];
  return (
    <footer style={{ background:T.bgAlt, borderTop:`1px solid ${T.border}`, marginTop:80 }}>
      <div style={{ padding:'48px 60px 32px', display:'grid',
        gridTemplateColumns:'200px 1fr', gap:48 }}>
        <div>
          <div style={{ display:'flex', alignItems:'center', gap:8, marginBottom:16 }}>
            <div style={{ width:24, height:24, background:T.invBg, borderRadius:6,
              display:'flex', alignItems:'center', justifyContent:'center' }}>
              <span style={{ fontSize:9, fontWeight:700, color:T.invText,
                fontFamily:'Geist, Inter, sans-serif' }}>AG</span>
            </div>
            <span style={{ fontWeight:600, fontSize:14, color:T.text,
              fontFamily:'Geist, Inter, sans-serif', letterSpacing:'-0.025em' }}>A-green</span>
          </div>
          <div style={{ fontSize:13, color:T.text2, lineHeight:1.7, marginBottom:20 }}>
            Широкий асортимент продукції для промислових підприємств та кузовного ремонту.
          </div>
          <div style={{ fontSize:12, color:T.text3 }}>info@a-green.com.ua</div>
        </div>
        <div style={{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:24 }}>
          {cols.map(col=>(
            <div key={col.title}>
              <div style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:14,
                lineHeight:'1.43', fontFamily:'Geist, Inter, sans-serif' }}>{col.title}</div>
              <div style={{ display:'flex', flexDirection:'column', gap:10 }}>
                {col.links.map((l,i)=>(
                  <span key={l} onClick={()=>setPage && col.pages && setPage(col.pages[i])}
                    style={{ fontSize:13, color:T.text2, cursor:'pointer', transition:'color 0.12s' }}
                    onMouseEnter={e=>e.target.style.color=T.text}
                    onMouseLeave={e=>e.target.style.color=T.text2}>{l}</span>
                ))}
              </div>
            </div>
          ))}
        </div>
      </div>
      <div style={{ borderTop:`1px solid ${T.border}`, padding:'20px 60px',
        display:'flex', justifyContent:'space-between', alignItems:'center' }}>
        <span style={{ fontSize:13, color:T.text3 }}>© 2026 A-green. Всі права захищені.</span>
        <span style={{ fontSize:13, color:T.text3 }}>02660, м. Київ, вул. Крайня 1</span>
      </div>
    </footer>
  );
}

Object.assign(window, {
  ThemeCtx, makeTheme,
  CATEGORIES, PRODUCTS, BRANDS, fmt,
  Stars, Badge, Img, Btn, Field, QtyCtrl,
  ProductCard, CatCard, Crumbs, Header, Footer,
});
