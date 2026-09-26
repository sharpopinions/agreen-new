// ── AKTSYIA (single promo page) ───────────────────────────────────
// Сторінка детальної акції — банер бренду + товари зі знижкою + фільтри

function AktsyiaPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [priceRange, setPriceRange] = useState(['', '']);
  const [onlyPromo, setOnlyPromo] = useState(true);
  const [onlyOrder, setOnlyOrder] = useState(false);

  const promoProducts = PRODUCTS.slice(0, 8).map(p => ({
    ...p,
    oldPrice: p.price,
    price: Math.round(p.price * 0.9),
    discount: 10,
  }));

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Акції','-10% на Дозуюче обладнання Tork']} setPage={setPage}/>

      {/* Hero banner */}
      <div style={{ background: T.surface, border: `1px solid ${T.border}`,
        borderRadius: T.rLg, padding: 32, marginBottom: 28,
        display: 'grid', gridTemplateColumns: '1fr auto', gap: 32,
        alignItems: 'center', boxShadow: T.shadow }}>
        <div>
          <div style={{ display:'flex', gap:8, marginBottom:14 }}>
            <span style={{ padding: '4px 12px',
              background: T.bg==='#09090b'?'#450a0a':'#fef2f2',
              border: `1px solid ${T.bg==='#09090b'?'#7f1d1d':'#fecaca'}`,
              borderRadius: T.rPill, fontSize: 12, fontWeight: 600, color: '#dc2626' }}>
              −10%
            </span>
            <span style={{ padding: '4px 12px', background: T.muted,
              border: `1px solid ${T.border}`, borderRadius: T.rPill,
              fontSize: 11, fontWeight: 500, color: T.text3 }}>
              До 30 травня 2026
            </span>
          </div>
          <h1 style={{ fontSize: 28, fontWeight: 700, color: T.text,
            letterSpacing: '-0.025em', marginBottom: 12,
            fontFamily: 'Geist, Inter, sans-serif', lineHeight: 1.2 }}>
            −10% на дозуюче обладнання<br/>від бренду Tork
          </h1>
          <p style={{ fontSize: 14, color: T.text2, lineHeight: 1.7, marginBottom: 20, maxWidth: 600 }}>
            Спеціальна пропозиція на всю лінійку дозуючого обладнання Tork — диспенсери для
            рушників, мила, серветок та туалетного паперу. Знижка діє при замовленні від 1 шт.
          </p>
          <div style={{ display: 'flex', gap: 24, alignItems: 'center', fontSize: 13, color: T.text2 }}>
            <div>
              <div style={{ fontSize: 11, color: T.text3, marginBottom: 2 }}>Бренд</div>
              <div style={{ fontWeight: 600, color: T.text }}>Tork</div>
            </div>
            <div style={{ width: 1, height: 28, background: T.border }}/>
            <div>
              <div style={{ fontSize: 11, color: T.text3, marginBottom: 2 }}>Категорія</div>
              <div style={{ fontWeight: 600, color: T.text }}>Дозуюче обладнання</div>
            </div>
            <div style={{ width: 1, height: 28, background: T.border }}/>
            <div>
              <div style={{ fontSize: 11, color: T.text3, marginBottom: 2 }}>Товарів</div>
              <div style={{ fontWeight: 600, color: T.text }}>{promoProducts.length}</div>
            </div>
          </div>
        </div>
        <div style={{ width: 200, height: 200, background: T.bgAlt,
          border: `1px solid ${T.border}`, borderRadius: T.rLg,
          display: 'flex', alignItems: 'center', justifyContent: 'center',
          fontSize: 48, fontWeight: 700, color: T.text3,
          fontFamily: 'Geist, Inter, sans-serif' }}>
          T
        </div>
      </div>

      {/* Content with sidebar */}
      <div style={{ display: 'grid', gridTemplateColumns: '260px 1fr', gap: 20 }}>
        {/* Filters */}
        <div style={{ background: T.surface, border: `1px solid ${T.border}`,
          borderRadius: T.rLg, height: 'fit-content', overflow: 'hidden' }}>
          <div style={{ padding: '14px 18px', borderBottom: `1px solid ${T.border}`,
            fontSize: 14, fontWeight: 600, color: T.text }}>
            Фільтри
          </div>
          <div style={{ padding: 18, borderBottom: `1px solid ${T.border}` }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 10 }}>
              Ціна, ₴
            </div>
            <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
              <input type="text" placeholder="Від" value={priceRange[0]}
                onChange={e=>setPriceRange([e.target.value, priceRange[1]])}
                style={{ flex:1, padding:'7px 10px', border:`1px solid ${T.border2}`,
                  borderRadius:T.rSm, fontSize:13, background:T.bg, color:T.text,
                  fontFamily:'Geist, Inter, sans-serif', outline:'none' }}/>
              <span style={{ color: T.text3 }}>—</span>
              <input type="text" placeholder="До" value={priceRange[1]}
                onChange={e=>setPriceRange([priceRange[0], e.target.value])}
                style={{ flex:1, padding:'7px 10px', border:`1px solid ${T.border2}`,
                  borderRadius:T.rSm, fontSize:13, background:T.bg, color:T.text,
                  fontFamily:'Geist, Inter, sans-serif', outline:'none' }}/>
            </div>
          </div>
          <div style={{ padding: 18 }}>
            <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 10 }}>
              Наявність
            </div>
            {[
              ['Тільки акційні пропозиції', onlyPromo, setOnlyPromo],
              ['Під замовлення', onlyOrder, setOnlyOrder],
            ].map(([label, val, setter]) => (
              <div key={label} onClick={()=>setter(!val)}
                style={{ display:'flex', alignItems:'center', gap:10, padding:'5px 0',
                  cursor:'pointer', fontSize:13, color: val?T.text:T.text2 }}>
                <div style={{ width:16, height:16, border:`1.5px solid ${val?T.invBg:T.border2}`,
                  borderRadius:4, background: val?T.invBg:'transparent',
                  flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center' }}>
                  {val && <svg width="9" height="7" viewBox="0 0 9 7" fill="none">
                    <path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/>
                  </svg>}
                </div>
                {label}
              </div>
            ))}
          </div>
        </div>

        {/* Product grid */}
        <div>
          <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center',
            marginBottom: 16 }}>
            <span style={{ fontSize:14, color:T.text2 }}>
              Знайдено: <strong style={{ color:T.text }}>{promoProducts.length} товарів</strong>
            </span>
            <select style={{ padding:'7px 10px', border:`1px solid ${T.border2}`,
              borderRadius:T.rSm, fontSize:13, background:T.surface, color:T.text,
              fontFamily:'Geist, Inter, sans-serif', cursor:'pointer' }}>
              <option>За знижкою</option>
              <option>За ціною</option>
              <option>За назвою</option>
            </select>
          </div>
          <div style={{ display:'grid', gridTemplateColumns:'repeat(3,1fr)', gap:16 }}>
            {promoProducts.map(p => (
              <ProductCard key={p.id} product={p}
                onView={()=>setPage('product')} onAdd={addToCart}/>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

Object.assign(window, { AktsyiaPage });
