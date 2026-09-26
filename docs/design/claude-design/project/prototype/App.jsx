const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{"darkMode": false}/*EDITMODE-END*/;

function TweaksPanel({ dark, setDark }) {
  const { T } = useContext(ThemeCtx);
  const [vis, setVis] = useState(false);

  useEffect(() => {
    const h = e => {
      if (e.data?.type === '__activate_edit_mode')   setVis(true);
      if (e.data?.type === '__deactivate_edit_mode') setVis(false);
    };
    window.addEventListener('message', h);
    window.parent.postMessage({ type: '__edit_mode_available' }, '*');
    return () => window.removeEventListener('message', h);
  }, []);

  if (!vis) return null;

  return (
    <div style={{ position:'fixed', bottom:24, right:24, zIndex:9999,
      background:T.surface, border:`1px solid ${T.border}`, borderRadius:T.rLg,
      padding:20, width:220,
      boxShadow: dark ? '0 16px 48px rgba(0,0,0,0.6)' : '0 16px 48px rgba(0,0,0,0.12)' }}>
      <div style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:16,
        letterSpacing:'-0.01em' }}>Tweaks</div>
      <div style={{ fontSize:12, color:T.text2, marginBottom:10, fontWeight:500 }}>Тема</div>
      <div style={{ display:'flex', gap:4, background:T.muted, borderRadius:T.rSm,
        border:`1px solid ${T.border}`, padding:3 }}>
        {[['light','☀ Світла'],['dark','◑ Темна']].map(([k,l])=>(
          <button key={k} onClick={()=>{
            const d = k==='dark';
            setDark(d);
            window.parent.postMessage({ type:'__edit_mode_set_keys', edits:{darkMode:d} }, '*');
          }} style={{ flex:1, padding:'7px 8px', borderRadius:T.rSm,
            background: (k==='dark')===dark ? T.surface : 'transparent',
            border: (k==='dark')===dark ? `1px solid ${T.border}` : '1px solid transparent',
            color: (k==='dark')===dark ? T.text : T.text2, fontSize:12, fontWeight:500,
            cursor:'pointer', fontFamily:'Geist, Inter, sans-serif',
            boxShadow: (k==='dark')===dark ? T.shadow : 'none', transition:'all 0.15s' }}>
            {l}
          </button>
        ))}
      </div>
    </div>
  );
}

function App() {
  const [dark, setDark] = useState(() => localStorage.getItem('ag_dark') === 'true');
  const [page, setPage] = useState(() => localStorage.getItem('ag_page') || 'main');
  const [cart, setCart] = useState(() => {
    try { return JSON.parse(localStorage.getItem('ag_cart') || '[]'); } catch { return []; }
  });

  useEffect(() => { localStorage.setItem('ag_dark', dark); }, [dark]);
  useEffect(() => {
    localStorage.setItem('ag_page', page);
    window.scrollTo({ top:0, behavior:'instant' });
  }, [page]);
  useEffect(() => { localStorage.setItem('ag_cart', JSON.stringify(cart)); }, [cart]);

  const T = makeTheme(dark);

  const [wishlist, setWishlist] = useState(() => {
    try { return JSON.parse(localStorage.getItem('ag_wishlist') || '[]'); } catch { return []; }
  });
  useEffect(() => { localStorage.setItem('ag_wishlist', JSON.stringify(wishlist)); }, [wishlist]);
  const toggleWish = (p) => setWishlist(prev =>
    prev.find(i => i.id === p.id)
      ? prev.filter(i => i.id !== p.id)
      : [...prev, p]
  );
  const removeWish = (id) => setWishlist(p => p.filter(i => i.id !== id));

  const [cartDrawer, setCartDrawer] = useState(false);
  const [cmdOpen, setCmdOpen] = useState(false);
  const [authOpen, setAuthOpen] = useState(false);

  // Global Cmd+K listener
  useEffect(() => {
    const handler = (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setCmdOpen(o => !o);
      }
    };
    window.addEventListener('keydown', handler);
    return () => window.removeEventListener('keydown', handler);
  }, []);
  const [cartPopup, setCartPopup] = useState(null); // { product }

  const addToCart = p => {
    setCart(prev => {
      const ex = prev.find(i => i.product.id === p.id);
      if (ex) return prev.map(i => i.product.id===p.id ? {...i, qty:i.qty+1} : i);
      return [...prev, { product:p, qty:1 }];
    });
    setCartPopup({ product: p });
    setTimeout(() => setCartPopup(null), 4000);
  };
  const updateQty   = (id, qty) => setCart(p => p.map(i => i.product.id===id ? {...i,qty} : i));
  const removeItem  = id        => setCart(p => p.filter(i => i.product.id!==id));
  const clearCart   = ()        => setCart([]);
  const cartCount   = cart.reduce((s,i) => s+i.qty, 0);
  const isDashboard = page === 'dashboard' || page === 'ext-dashboard'
    || page === 'rules-program' || page === 'support-list' || page === 'support-thread';
  const isOwnLayout = page === 'client-dashboard'; // має власну шапку

  const knownPages = ['main','catalog','product','product-business','product-order',
    'product-modified','product-brand','cart','checkout','dashboard',
    '404','about','brands','brand-products','blog','blog-post','services','contacts',
    'favorites','sale','partners','delivery','compare','vacancies','login',
    'thank-order','thank-form','privacy','public-offer','return',
    'ext-dashboard','cart-empty','partners-popup','aktsyii','aktsyia','role-selection',
    'email-confirm','login-email','client-dashboard','catalog-sub','study-center',
    'brand-about','brand-media','catalog-category',
    'blog-event','blog-news','blog-events','blog-about',
    'services-popup','brands-category','brand-subcategory',
    'rules-program','support-list','support-thread','role-confirmed'];

  return (
    <ThemeCtx.Provider value={{ dark, T }}>
      <div style={{ background:T.bg, minHeight:'100vh', color:T.text,
        fontFamily:'Geist, Inter, -apple-system, sans-serif',
        transition:'background 0.3s ease, color 0.25s ease' }}>

        {isOwnLayout ? null : isDashboard ? (          /* Dashboard mini-header */
          <div style={{ background:T.surface, borderBottom:`1px solid ${T.border}`,
            padding:'0 24px', height:52,
            display:'flex', alignItems:'center', gap:12 }}>
            <div style={{ width:24, height:24, background:T.invBg, borderRadius:6,
              display:'flex', alignItems:'center', justifyContent:'center' }}>
              <span style={{ fontSize:9, fontWeight:700, color:T.invText }}>AG</span>
            </div>
            <span style={{ fontSize:14, fontWeight:700, color:T.text, letterSpacing:'-0.025em' }}>
              A-green
            </span>
            <span style={{ color:T.border2, fontSize:14 }}>/</span>
            <span style={{ fontSize:13, color:T.text2, fontWeight:500 }}>Особистий кабінет</span>
            <div style={{ flex:1 }}/>
            <button onClick={()=>setDark(!dark)}
              style={{ background:T.muted, border:`1px solid ${T.border}`,
                borderRadius:T.rPill, padding:'4px 12px', fontSize:12, fontWeight:500,
                color:T.text2, cursor:'pointer', fontFamily:'Geist, Inter, sans-serif',
                display:'flex', alignItems:'center', gap:6, transition:'all 0.15s' }}>
              <span>{dark?'☀':'◑'}</span>
              {dark?'Світла':'Темна'}
            </button>
            <span onClick={()=>setPage('main')}
              style={{ fontSize:13, fontWeight:500, color:T.text2, cursor:'pointer',
                marginLeft:8, transition:'color 0.12s' }}
              onMouseEnter={e=>e.target.style.color=T.text}
              onMouseLeave={e=>e.target.style.color=T.text2}>
              Магазин ↗
            </span>
          </div>
        ) : (
          <Header page={page} setPage={setPage} cartCount={cartCount} dark={dark} setDark={setDark} setCartDrawer={setCartDrawer} wishlist={wishlist} onRemoveWish={removeWish} setAuthOpen={setAuthOpen}/>
        )}

        {/* Page router */}
        {page==='main'     && <MainPage     setPage={setPage} addToCart={addToCart}/>}
        {page==='catalog'  && <CatalogPage  setPage={setPage} addToCart={addToCart}/>}
        {page==='product'  && <ProductPage  setPage={setPage} addToCart={addToCart}/>}
        {page==='product-business' && <ProductPage setPage={setPage} addToCart={addToCart} mode="business"/>}
        {page==='product-order'    && <ProductPage setPage={setPage} addToCart={addToCart} mode="to-order"/>}
        {page==='product-modified' && <ProductPage setPage={setPage} addToCart={addToCart} mode="modified"/>}
        {page==='product-brand'    && <ProductPage setPage={setPage} addToCart={addToCart} mode="brand"/>}
        {page==='cart'     && <CartPage     setPage={setPage} cart={cart} updateQty={updateQty} removeItem={removeItem}/>}
        {page==='checkout' && <CheckoutPage setPage={setPage} cart={cart} clearCart={clearCart}/>}
        {page==='dashboard'   && <DashboardPage setPage={setPage}/>}
        {page==='404'         && <Page404        setPage={setPage}/>}
        {page==='about'       && <AboutPage      setPage={setPage}/>}
        {page==='brands'      && <BrandsPage     setPage={setPage}/>}
        {page==='brand-products' && <BrandsPage  setPage={setPage}/>}
        {page==='blog'        && <BlogPage       setPage={setPage}/>}
        {page==='blog-post'   && <BlogPostPage   setPage={setPage}/>}
        {page==='services'    && <ServicesPage   setPage={setPage}/>}
        {page==='contacts'    && <ContactsPage   setPage={setPage}/>}
        {page==='favorites'   && <FavoritesPage  setPage={setPage} addToCart={addToCart}/>}
        {page==='sale'        && <SalePage       setPage={setPage} addToCart={addToCart}/>}
        {page==='partners'    && <PartnersPage   setPage={setPage}/>}
        {page==='delivery'    && <DeliveryPage       setPage={setPage}/>}
        {page==='compare'     && <ComparePage       setPage={setPage} addToCart={addToCart}/>}
        {page==='vacancies'   && <VacanciesPage     setPage={setPage}/>}
        {page==='login'       && <LoginPage         setPage={setPage}/>}
        {page==='thank-order' && <ThankOrderPage    setPage={setPage}/>}
        {page==='thank-form'  && <ThankFormPage     setPage={setPage}/>}
        {page==='privacy'     && <PrivacyPolicyPage setPage={setPage}/>}
        {page==='public-offer'&& <PublicOfferPage   setPage={setPage}/>}
        {page==='return'        && <ReturnPage          setPage={setPage}/>}
        {page==='brand-products'&& <BrandProductsPage  setPage={setPage} addToCart={addToCart}/>}
        {page==='ext-dashboard'   && <ExtendedDashboard  setPage={setPage}/>}
        {page==='cart-empty'      && <CartEmptyPage      setPage={setPage} addToCart={addToCart}/>}
        {page==='partners-popup'  && <PartnersPopupPage  setPage={setPage}/>}
        {page==='aktsyii'         && <AktsyiiPage        setPage={setPage} addToCart={addToCart}/>}
        {page==='aktsyia'         && <AktsyiaPage        setPage={setPage} addToCart={addToCart}/>}
        {page==='catalog-sub'     && <CatalogSubcategoryPage setPage={setPage} addToCart={addToCart}/>}
        {page==='study-center'    && <StudyCenterPage    setPage={setPage}/>}
        {page==='brand-about'     && <BrandDetailPage   setPage={setPage} defaultTab="about"/>}
        {page==='brand-media'     && <BrandDetailPage   setPage={setPage} defaultTab="media"/>}
        {page==='catalog-category'&& <CatalogCategoryPage setPage={setPage} addToCart={addToCart}/>}
        {page==='blog-event'      && <BlogEventPage    setPage={setPage}/>}
        {page==='blog-news'       && <BlogFilteredPage setPage={setPage} defaultCat="Новини ринку"/>}
        {page==='blog-events'     && <BlogFilteredPage setPage={setPage} defaultCat="ЗМІ про нас"/>}
        {page==='blog-about'      && <BlogFilteredPage setPage={setPage} defaultCat="Поради"/>}
        {page==='services-popup'  && <ServicesPopupPage  setPage={setPage}/>}
        {page==='brands-category' && <BrandsCategoryPage setPage={setPage}/>}
        {page==='brand-subcategory' && <BrandSubcategoryPage setPage={setPage} addToCart={addToCart}/>}
        {page==='rules-program'   && <DashboardRulesProgram   setPage={setPage}/>}
        {page==='support-list'    && <DashboardSupportList    setPage={setPage}/>}
        {page==='support-thread'  && <DashboardSupportThread  setPage={setPage}/>}
        {page==='role-confirmed'  && <RoleSelectionConfirmedPage setPage={setPage}/>}
        {page==='role-selection'   && <RoleSelectionPage     setPage={setPage}/>}
        {page==='email-confirm'    && <EmailConfirmationPage setPage={setPage}/>}
        {page==='login-email'      && <LoginEmailPage        setPage={setPage}/>}
        {page==='client-dashboard' && <ClientCabinet setPage={setPage} addToCart={addToCart} cartCount={cartCount} setCartDrawer={setCartDrawer}/>}

        {/* Fallback */}
        {!knownPages.includes(page) && (
          <div style={{ minHeight:'55vh', display:'flex', flexDirection:'column',
            alignItems:'center', justifyContent:'center', gap:14 }}>
            <div style={{ fontSize:18, fontWeight:600, color:T.text }}>Сторінка «{page}»</div>
            <div style={{ fontSize:14, color:T.text2 }}>У прототипі ще не реалізована</div>
            <Btn variant="outline" onClick={()=>setPage('main')}>На головну</Btn>
          </div>
        )}

        {!isDashboard && !isOwnLayout && page!=='checkout' && <Footer setPage={setPage}/>}

        <TweaksPanel dark={dark} setDark={setDark}/>
        <CommandPalette open={cmdOpen} onClose={()=>setCmdOpen(false)} setPage={setPage}/>
        <AuthModal open={authOpen} onClose={()=>setAuthOpen(false)} setPage={setPage}/>

        {cartDrawer && (
          <CartDrawer
            setPage={setPage}
            cart={cart}
            onClose={() => setCartDrawer(false)}
          />
        )}
        {/* ── CART ADDING POPUP ── */}
        {cartPopup && (
          <div style={{ position:'fixed', top:80, right:20, zIndex:9000, width:420,
            background: T.surface, border:`1px solid ${T.border}`,
            borderRadius: T.rLg, boxShadow: T.shadowMd,
            animation:'slideInRight 0.25s cubic-bezier(0.16,1,0.3,1)' }}>
            <style>{`@keyframes slideInRight{from{opacity:0;transform:translateX(24px)}to{opacity:1;transform:translateX(0)}}`}</style>
            {/* Header */}
            <div style={{ padding:'14px 16px', borderBottom:`1px solid ${T.border}`,
              display:'flex', alignItems:'center', justifyContent:'space-between' }}>
              <div style={{ display:'flex', alignItems:'center', gap:8 }}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                  stroke="#16a34a" strokeWidth="2.5">
                  <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span style={{ fontSize:14, fontWeight:600, color:T.text }}>
                  Додано до кошика
                </span>
              </div>
              <button onClick={()=>setCartPopup(null)}
                style={{ background:'none', border:'none', cursor:'pointer',
                  color:T.text3, padding:4, borderRadius:T.rSm,
                  display:'flex', alignItems:'center', justifyContent:'center' }}>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" strokeWidth="2">
                  <line x1="18" y1="6" x2="6" y2="18"/>
                  <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>

            {/* Added product */}
            <div style={{ padding:'14px 16px', display:'flex', gap:12,
              alignItems:'center', borderBottom:`1px solid ${T.border}` }}>
              <div style={{ width:72, height:72, flexShrink:0, borderRadius:T.rSm,
                overflow:'hidden' }}>
                <Img h={72} label=""/>
              </div>
              <div style={{ flex:1, minWidth:0 }}>
                <div style={{ fontSize:12, color:T.text3, marginBottom:3 }}>
                  Арт: {cartPopup.product.sku}
                </div>
                <div style={{ fontSize:13, fontWeight:500, color:T.text,
                  lineHeight:1.4, marginBottom:6,
                  overflow:'hidden', textOverflow:'ellipsis',
                  display:'-webkit-box', WebkitLineClamp:2, WebkitBoxOrient:'vertical' }}>
                  {cartPopup.product.name}
                </div>
                <div style={{ fontSize:15, fontWeight:700, color:T.text,
                  letterSpacing:'-0.025em' }}>
                  {fmt(cartPopup.product.price)}
                </div>
              </div>
            </div>

            {/* Recommended */}
            <div style={{ padding:'12px 16px', borderBottom:`1px solid ${T.border}` }}>
              <div style={{ fontSize:11, fontWeight:600, color:T.text3,
                textTransform:'uppercase', letterSpacing:'0.08em', marginBottom:10 }}>
                Рекомендуємо також
              </div>
              <div style={{ display:'flex', gap:8 }}>
                {PRODUCTS.filter(p=>p.id!==cartPopup.product.id).slice(0,2).map(p=>(
                  <div key={p.id} style={{ flex:1, background:T.bgAlt,
                    border:`1px solid ${T.border}`, borderRadius:T.rSm, padding:10,
                    cursor:'pointer' }}
                    onMouseEnter={e=>e.currentTarget.style.borderColor=T.border2}
                    onMouseLeave={e=>e.currentTarget.style.borderColor=T.border}>
                    <Img h={60} label=""/>
                    <div style={{ fontSize:11, color:T.text, marginTop:6,
                      lineHeight:1.4,
                      overflow:'hidden', textOverflow:'ellipsis',
                      display:'-webkit-box', WebkitLineClamp:2,
                      WebkitBoxOrient:'vertical' }}>
                      {p.name}
                    </div>
                    <div style={{ fontSize:12, fontWeight:600, color:T.text,
                      marginTop:4 }}>{fmt(p.price)}</div>
                  </div>
                ))}
              </div>
            </div>

            {/* Actions */}
            <div style={{ padding:'12px 16px', display:'flex', gap:8 }}>
              <Btn variant="secondary" size="sm"
                onClick={()=>setCartPopup(null)}>
                Продовжити
              </Btn>
              <Btn size="sm" full
                onClick={()=>{ setCartPopup(null); setPage('cart'); }}>
                Оформити кошик ({cart.reduce((s,i)=>s+i.qty,0)})
              </Btn>
            </div>
          </div>
        )}
      </div>
    </ThemeCtx.Provider>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App />);
