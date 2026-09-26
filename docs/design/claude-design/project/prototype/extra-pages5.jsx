// ── EXTRA PAGES 5 ─────────────────────────────────────────────────
// Registration Confirmation, Login-Email (OTP), Client Dashboard,
// Cart-Empty-closed (side drawer), Sidebar hover states (mega-menu)

// ── EMAIL CONFIRMATION ────────────────────────────────────────────
function EmailConfirmationPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [code, setCode] = useState(['','','','','','']);
  const [timer, setTimer] = useState(60);
  const [sent, setSent] = useState(false);
  const inputRefs = Array.from({length:6}, () => React.useRef(null));

  React.useEffect(() => {
    const t = timer > 0 && setInterval(() => setTimer(s => s-1), 1000);
    return () => clearInterval(t);
  }, [timer]);

  const handleCode = (i, v) => {
    const next = [...code];
    next[i] = v.slice(-1);
    setCode(next);
    if (v && i < 5) inputRefs[i+1].current?.focus();
  };

  const filled = code.every(c => c !== '');

  if (sent) return (
    <div style={{ background: T.bg, minHeight:'80vh', display:'flex', flexDirection:'column',
      alignItems:'center', justifyContent:'center', gap:16 }}>
      <RoleSelectionPage setPage={setPage}/>
    </div>
  );

  return (
    <div style={{ background: T.bg, minHeight:'80vh', display:'flex', alignItems:'center',
      justifyContent:'center', padding:'40px 60px' }}>
      <div style={{ background: T.surface, border:`1px solid ${T.border}`,
        borderRadius: T.rLg, padding:'48px 40px', width:400, textAlign:'center',
        boxShadow: T.shadowMd }}>
        <button onClick={()=>setPage('login')}
          style={{ position:'absolute', top:16, right:16, background:'none', border:'none',
            cursor:'pointer', color:T.text3 }}>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>

        <div style={{ width:56, height:56, background: T.bg==='#09090b'?'#172554':'#eff6ff',
          border:`1px solid ${T.bg==='#09090b'?'#1e3a8a':'#bfdbfe'}`,
          borderRadius: T.rLg, display:'flex', alignItems:'center', justifyContent:'center',
          margin:'0 auto 20px' }}>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke={T.bg==='#09090b'?'#60a5fa':'#2563eb'} strokeWidth="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </div>

        <h2 style={{ fontSize:22, fontWeight:700, color:T.text, marginBottom:8,
          letterSpacing:'-0.025em', fontFamily:'Geist, Inter, sans-serif' }}>
          Підтвердіть реєстрацію
        </h2>
        <p style={{ fontSize:13, color:T.text2, lineHeight:1.65, marginBottom:8 }}>
          Ми надіслали код підтвердження на вашу email-адресу.
        </p>
        <p style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:28 }}>
          your-email@company.ua
        </p>

        {/* Code input */}
        <div style={{ display:'flex', gap:8, justifyContent:'center', marginBottom:20 }}>
          {code.map((c, i) => (
            <input key={i} ref={inputRefs[i]} value={c}
              onChange={e => handleCode(i, e.target.value)}
              onKeyDown={e => { if(e.key==='Backspace' && !c && i>0) inputRefs[i-1].current?.focus(); }}
              maxLength={1} type="text" inputMode="numeric"
              style={{ width:44, height:48, textAlign:'center', fontSize:20, fontWeight:700,
                color:T.text, background:T.bgAlt,
                border:`1.5px solid ${c?T.invBg:T.border}`,
                borderRadius: T.rSm, outline:'none', fontFamily:'Geist, Inter, sans-serif',
                transition:'border-color 0.15s' }}/>
          ))}
        </div>

        <Btn full disabled={!filled} onClick={()=>setSent(true)}>
          Підтвердити
        </Btn>

        <div style={{ marginTop:20, fontSize:13, color:T.text2 }}>
          {timer > 0 ? (
            <span>Надіслати ще раз через <strong style={{color:T.text}}>{timer} сек</strong></span>
          ) : (
            <span onClick={()=>setTimer(60)} style={{color:T.text, cursor:'pointer', textDecoration:'underline', textUnderlineOffset:2}}>
              Надіслати ще раз
            </span>
          )}
        </div>
        <div style={{ marginTop:10, fontSize:13, color:T.text2 }}>
          або{' '}
          <span style={{color:T.text, cursor:'pointer', textDecoration:'underline', textUnderlineOffset:2}}
            onClick={()=>setPage('login-sms')}>
            підтвердження по SMS
          </span>
        </div>
      </div>
    </div>
  );
}

// ── LOGIN EMAIL (OTP by email) ─────────────────────────────────────
function LoginEmailPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [step, setStep] = useState('email'); // 'email' | 'code'
  const [email, setEmail] = useState('');
  const [code, setCode] = useState(['','','','','','']);
  const inputRefs = Array.from({length:6}, () => React.useRef(null));
  const [timer, setTimer] = useState(0);

  React.useEffect(() => {
    if(step==='code' && timer===0) setTimer(60);
  }, [step]);

  React.useEffect(() => {
    const t = timer>0 && setInterval(()=>setTimer(s=>s-1), 1000);
    return ()=>clearInterval(t);
  }, [timer]);

  const handleCode = (i, v) => {
    const next = [...code];
    next[i] = v.slice(-1);
    setCode(next);
    if(v && i<5) inputRefs[i+1].current?.focus();
  };

  return (
    <div style={{ background:T.bg, minHeight:'80vh', display:'flex', alignItems:'center',
      justifyContent:'center', padding:'40px 60px' }}>
      <div style={{ background:T.surface, border:`1px solid ${T.border}`,
        borderRadius:T.rLg, width:760, overflow:'hidden', boxShadow:T.shadowMd,
        display:'grid', gridTemplateColumns:'1fr 1fr' }}>

        {/* Left */}
        <div style={{ padding:40 }}>
          <div style={{ marginBottom:24 }}>
            <div onClick={()=>setPage('main')} style={{ display:'flex', alignItems:'center', gap:8, cursor:'pointer' }}>
              <div style={{ width:28, height:28, background:T.invBg, borderRadius:8,
                display:'flex', alignItems:'center', justifyContent:'center' }}>
                <span style={{ fontSize:11, fontWeight:700, color:T.invText }}>AG</span>
              </div>
              <span style={{ fontWeight:600, fontSize:15, color:T.text, letterSpacing:'-0.025em' }}>A-green</span>
            </div>
          </div>

          {step==='email' ? (
            <div style={{ display:'flex', flexDirection:'column', gap:16 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Вхід</h2>
              <p style={{ fontSize:13, color:T.text2, lineHeight:1.6 }}>
                Введіть email або телефон — надішлемо код входу без пароля
              </p>
              <Field label="Email або телефон" placeholder="+380 або email@company.ua"
                value={email} onChange={e=>setEmail(e.target.value)}/>
              <Btn full onClick={()=>setStep('code')}>Отримати код</Btn>
              <div style={{ display:'flex', alignItems:'center', gap:10 }}>
                <div style={{ flex:1, height:1, background:T.border }}/>
                <span style={{ fontSize:11, color:T.text3 }}>або</span>
                <div style={{ flex:1, height:1, background:T.border }}/>
              </div>
              <Btn variant="outline" full onClick={()=>setPage('login')}>Увійти з паролем</Btn>
            </div>
          ) : (
            <div style={{ display:'flex', flexDirection:'column', gap:20 }}>
              <div>
                <button onClick={()=>setStep('email')} style={{ background:'none', border:'none',
                  cursor:'pointer', color:T.text2, fontSize:13, display:'flex',
                  alignItems:'center', gap:4, padding:0, marginBottom:16 }}>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="m15 18-6-6 6-6"/>
                  </svg>
                  Назад
                </button>
                <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
                  fontFamily:'Geist, Inter, sans-serif' }}>Введіть код</h2>
                <p style={{ fontSize:13, color:T.text2, marginTop:6, lineHeight:1.6 }}>
                  Код надіслано на <strong style={{color:T.text}}>{email || '+380 97 075-71-70'}</strong>
                </p>
              </div>
              <div style={{ display:'flex', gap:8 }}>
                {code.map((c,i)=>(
                  <input key={i} ref={inputRefs[i]} value={c}
                    onChange={e=>handleCode(i,e.target.value)}
                    onKeyDown={e=>{if(e.key==='Backspace'&&!c&&i>0)inputRefs[i-1].current?.focus();}}
                    maxLength={1} type="text" inputMode="numeric"
                    style={{ width:44, height:48, textAlign:'center', fontSize:20, fontWeight:700,
                      color:T.text, background:T.bgAlt,
                      border:`1.5px solid ${c?T.invBg:T.border}`,
                      borderRadius:T.rSm, outline:'none',
                      fontFamily:'Geist, Inter, sans-serif', transition:'border-color 0.15s' }}/>
                ))}
              </div>
              <Btn full disabled={!code.every(c=>c)} onClick={()=>setPage('ext-dashboard')}>
                Увійти
              </Btn>
              <div style={{ fontSize:13, color:T.text2 }}>
                {timer>0
                  ? <span>Надіслати ще раз через <strong style={{color:T.text}}>{timer} сек</strong></span>
                  : <span onClick={()=>setTimer(60)} style={{color:T.text, cursor:'pointer', textDecoration:'underline'}}>Надіслати ще раз</span>}
              </div>
            </div>
          )}
        </div>

        {/* Right */}
        <div style={{ background:T.bgAlt, borderLeft:`1px solid ${T.border}`, padding:40,
          display:'flex', flexDirection:'column', justifyContent:'center', gap:20 }}>
          <Img h={180} label="особистий кабінет партнера"/>
          <div style={{ fontSize:15, fontWeight:700, color:T.text }}>Кабінет бізнес-партнера</div>
          <div style={{ fontSize:13, color:T.text2, lineHeight:1.7 }}>
            Доступ до партнерських цін, управління замовленнями та документообігу.
          </div>
          {[['Знижки до 25%','від роздрібної ціни'],
            ['Дропшипінг','відправка від A-green'],
            ['Документи','онлайн в кабінеті'],
            ['Підтримка','персональний менеджер']].map(([t,s])=>(
            <div key={t} style={{ display:'flex', gap:8, alignItems:'flex-start' }}>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                stroke={T.text} strokeWidth="2.5" style={{marginTop:2, flexShrink:0}}>
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <div>
                <span style={{fontSize:13,fontWeight:600,color:T.text}}>{t}</span>
                <span style={{fontSize:13,color:T.text2}}> — {s}</span>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

// ── CLIENT DASHBOARD ──────────────────────────────────────────────
function ClientDashboardPage({ setPage, addToCart }) {
  const { T } = useContext(ThemeCtx);
  const [sec, setSec] = useState('catalog');

  const navItems = [
    { key:'catalog',  icon:'⊞', label:'Каталог'       },
    { key:'orders',   icon:'◫', label:'Замовлення'     },
    { key:'wishlist', icon:'♡', label:'Обрані товари'  },
    { key:'profile',  icon:'◎', label:'Профіль'       },
  ];

  const CLIENT_ORDERS = [
    { id:'#C-2026-04220', date:'22 квіт 2026', status:'processing', total:1099, items:1 },
    { id:'#C-2026-03800', date:'10 бер 2026',  status:'delivered',  total:2198, items:2 },
  ];

  return (
    <div style={{ background:T.bg, minHeight:'calc(100vh - 52px)',
      display:'grid', gridTemplateColumns:'200px 1fr' }}>

      {/* Sidebar */}
      <div style={{ background:T.surface, borderRight:`1px solid ${T.border}`,
        padding:'20px 0', display:'flex', flexDirection:'column' }}>
        <div style={{ padding:'0 16px 20px', borderBottom:`1px solid ${T.border}`, marginBottom:8 }}>
          <div style={{ width:44, height:44, background:T.muted, borderRadius:T.rLg,
            display:'flex', alignItems:'center', justifyContent:'center', marginBottom:10, fontSize:20 }}>
            👤
          </div>
          <div style={{ fontSize:13, fontWeight:600, color:T.text }}>Іван Іваненко</div>
          <div style={{ fontSize:11, color:T.text3, marginTop:2 }}>Клієнт</div>
        </div>
        <div style={{ flex:1 }}>
          {navItems.map(item=>(
            <div key={item.key} onClick={()=>setSec(item.key)}
              style={{ padding:'9px 16px', fontSize:14, cursor:'pointer',
                fontWeight: sec===item.key?500:400,
                color: sec===item.key?T.text:T.text2,
                background: sec===item.key?T.muted:'transparent',
                borderLeft:`2px solid ${sec===item.key?T.invBg:'transparent'}`,
                transition:'all 0.12s', display:'flex', alignItems:'center', gap:10 }}>
              <span style={{ fontSize:15 }}>{item.icon}</span>{item.label}
            </div>
          ))}
        </div>
        {/* Upgrade CTA */}
        <div style={{ margin:'0 12px 16px', background: T.bg==='#09090b'?'#172554':'#eff6ff',
          border:`1px solid ${T.bg==='#09090b'?'#1e3a8a':'#bfdbfe'}`,
          borderRadius:T.rSm, padding:'14px 12px' }}>
          <div style={{ fontSize:11, fontWeight:600, color: T.bg==='#09090b'?'#93c5fd':'#1d4ed8',
            marginBottom:6 }}>Стати партнером</div>
          <div style={{ fontSize:11, color: T.bg==='#09090b'?'#bfdbfe':'#3b82f6',
            marginBottom:8, lineHeight:1.5 }}>Отримайте знижки до 25%</div>
          <button onClick={()=>setPage('role-selection')}
            style={{ width:'100%', background: T.bg==='#09090b'?'#1d4ed8':'#2563eb',
              border:'none', borderRadius:T.rSm, padding:'6px 10px',
              fontSize:11, fontWeight:600, color:'#fff', cursor:'pointer' }}>
            Дізнатись більше
          </button>
        </div>
        <div style={{ padding:'12px 16px', borderTop:`1px solid ${T.border}` }}>
          <span onClick={()=>setPage('main')} style={{ fontSize:13, color:T.text3, cursor:'pointer' }}
            onMouseEnter={e=>e.target.style.color=T.text2} onMouseLeave={e=>e.target.style.color=T.text3}>
            ← Магазин
          </span>
        </div>
      </div>

      {/* Content */}
      <div style={{ padding:32 }}>
        {sec==='catalog' && (
          <div>
            <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center', marginBottom:24 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Каталог</h2>
            </div>
            <div style={{ position:'relative', marginBottom:24 }}>
              <svg style={{ position:'absolute', left:12, top:'50%', transform:'translateY(-50%)', color:T.text3 }}
                width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
              </svg>
              <input placeholder="Пошук по каталогу..."
                style={{ width:'100%', background:T.surface, border:`1px solid ${T.border}`,
                  borderRadius:T.rSm, padding:'10px 16px 10px 38px',
                  color:T.text, fontSize:14, outline:'none', fontFamily:'Geist, Inter, sans-serif' }}/>
            </div>
            <div style={{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:12 }}>
              {CATEGORIES.map(cat=>(
                <CatCard key={cat.id} cat={cat} onSelect={()=>setPage('catalog')}/>
              ))}
            </div>
          </div>
        )}

        {sec==='orders' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
              marginBottom:24, fontFamily:'Geist, Inter, sans-serif' }}>Мої замовлення</h2>
            {CLIENT_ORDERS.length===0 ? (
              <div style={{ textAlign:'center', padding:'60px 20px' }}>
                <div style={{ fontSize:14, color:T.text2, marginBottom:12 }}>Замовлень ще немає</div>
                <Btn onClick={()=>setPage('catalog')}>До каталогу</Btn>
              </div>
            ) : (
              <div style={{ display:'flex', flexDirection:'column', gap:8 }}>
                {CLIENT_ORDERS.map(o=>(
                  <div key={o.id} style={{ background:T.surface, border:`1px solid ${T.border}`,
                    borderRadius:T.rLg, padding:'14px 18px', display:'flex',
                    alignItems:'center', gap:16, boxShadow:T.shadow }}>
                    <div style={{ flex:1 }}>
                      <div style={{ fontSize:14, fontWeight:600, color:T.text }}>{o.id}</div>
                      <div style={{ fontSize:12, color:T.text2, marginTop:2 }}>{o.date} · {o.items} шт</div>
                    </div>
                    <span style={{ padding:'4px 12px', borderRadius:T.rPill, fontSize:12, fontWeight:500,
                      background: o.status==='delivered'?(T.bg==='#09090b'?'#052e16':'#f0fdf4'):T.muted,
                      color: o.status==='delivered'?'#16a34a':T.text2,
                      border:`1px solid ${o.status==='delivered'?(T.bg==='#09090b'?'#14532d':'#bbf7d0'):T.border}` }}>
                      {o.status==='delivered'?'Доставлено':'В обробці'}
                    </span>
                    <div style={{ fontSize:16, fontWeight:700, color:T.text, letterSpacing:'-0.025em' }}>
                      {fmt(o.total)}
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}

        {sec==='wishlist' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
              marginBottom:24, fontFamily:'Geist, Inter, sans-serif' }}>Обрані товари</h2>
            <div style={{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:16 }}>
              {PRODUCTS.slice(0,4).map(p=>(
                <ProductCard key={p.id} product={p} onView={()=>setPage('product')} onAdd={addToCart}/>
              ))}
            </div>
          </div>
        )}

        {sec==='profile' && (
          <div>
            <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
              marginBottom:24, fontFamily:'Geist, Inter, sans-serif' }}>Мій профіль</h2>
            <div style={{ background:T.surface, border:`1px solid ${T.border}`,
              borderRadius:T.rLg, padding:28, maxWidth:480 }}>
              <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
                <Field label="Ім'я та прізвище" placeholder="Іван Іваненко"/>
                <Field label="Email" placeholder="ivan@gmail.com" type="email"/>
                <Field label="Телефон" placeholder="+380 XX XXX XX XX"/>
                <div style={{ paddingTop:4 }}><Btn>Зберегти зміни</Btn></div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

// ── CART EMPTY CLOSED (side drawer) ──────────────────────────────
// This is the "mini cart" state — catalog page showing empty cart drawer
// We implement it as a floating side drawer component
function CartDrawer({ setPage, cart, onClose }) {
  const { T } = useContext(ThemeCtx);
  const total = cart.reduce((s,i)=>s+i.product.price*i.qty, 0);

  return (
    <div style={{ position:'fixed', top:0, right:0, bottom:0, zIndex:8500,
      display:'flex' }}>
      {/* Backdrop */}
      <div onClick={onClose} style={{ position:'fixed', inset:0,
        background:'rgba(0,0,0,0.4)', backdropFilter:'blur(2px)' }}/>
      {/* Drawer */}
      <div style={{ position:'relative', width:400, background:T.surface,
        borderLeft:`1px solid ${T.border}`, marginLeft:'auto',
        display:'flex', flexDirection:'column',
        boxShadow:'-8px 0 32px rgba(0,0,0,0.15)', animation:'slideInFromRight 0.25s cubic-bezier(0.16,1,0.3,1)' }}>
        <style>{`@keyframes slideInFromRight{from{transform:translateX(100%)}to{transform:translateX(0)}}`}</style>

        {/* Header */}
        <div style={{ padding:'18px 20px', borderBottom:`1px solid ${T.border}`,
          display:'flex', alignItems:'center', justifyContent:'space-between' }}>
          <div style={{ fontSize:16, fontWeight:600, color:T.text }}>
            Кошик {cart.length>0 && `(${cart.reduce((s,i)=>s+i.qty,0)})`}
          </div>
          <button onClick={onClose} style={{ background:'none', border:'none',
            cursor:'pointer', color:T.text3, padding:4 }}>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>

        {cart.length===0 ? (
          /* Empty state */
          <div style={{ flex:1, display:'flex', flexDirection:'column',
            alignItems:'center', justifyContent:'center', gap:12, padding:32 }}>
            <div style={{ width:56, height:56, background:T.muted, borderRadius:T.rLg,
              display:'flex', alignItems:'center', justifyContent:'center' }}>
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke={T.text3} strokeWidth="1.5">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
              </svg>
            </div>
            <div style={{ fontSize:15, fontWeight:600, color:T.text }}>Кошик порожній</div>
            <div style={{ fontSize:13, color:T.text2, textAlign:'center', lineHeight:1.6 }}>
              Додайте товари, щоб оформити замовлення
            </div>
            <Btn onClick={()=>{ onClose(); setPage('catalog'); }}>До каталогу</Btn>
          </div>
        ) : (
          <>
            {/* Items */}
            <div style={{ flex:1, overflow:'auto', padding:16, display:'flex', flexDirection:'column', gap:10 }}>
              {cart.map(item=>(
                <div key={item.product.id} style={{ display:'flex', gap:12, alignItems:'center',
                  background:T.bgAlt, border:`1px solid ${T.border}`, borderRadius:T.rSm, padding:12 }}>
                  <div style={{ width:56, height:56, flexShrink:0, borderRadius:T.rSm, overflow:'hidden' }}>
                    <Img h={56} label=""/>
                  </div>
                  <div style={{ flex:1, minWidth:0 }}>
                    <div style={{ fontSize:12, color:T.text, lineHeight:1.4,
                      overflow:'hidden', textOverflow:'ellipsis', whiteSpace:'nowrap' }}>
                      {item.product.name}
                    </div>
                    <div style={{ fontSize:11, color:T.text3, marginTop:2 }}>
                      {item.qty} шт × {fmt(item.product.price)}
                    </div>
                  </div>
                  <div style={{ fontSize:13, fontWeight:600, color:T.text, flexShrink:0 }}>
                    {fmt(item.product.price*item.qty)}
                  </div>
                </div>
              ))}
            </div>
            {/* Footer */}
            <div style={{ padding:16, borderTop:`1px solid ${T.border}` }}>
              <div style={{ display:'flex', justifyContent:'space-between', marginBottom:14,
                fontSize:16, fontWeight:700, color:T.text, letterSpacing:'-0.025em' }}>
                <span>Разом:</span><span>{fmt(total)}</span>
              </div>
              <Btn full onClick={()=>{ onClose(); setPage('checkout'); }}>Оформити замовлення</Btn>
              <div style={{ marginTop:8 }}>
                <Btn variant="ghost" full onClick={()=>{ onClose(); setPage('cart'); }}>
                  Перейти до кошика
                </Btn>
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  );
}

Object.assign(window, {
  EmailConfirmationPage, LoginEmailPage, ClientDashboardPage, CartDrawer,
});
