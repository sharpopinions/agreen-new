// ── AUTH MODAL (Вхід / Реєстрація / Підтвердження / Вибір ролі) ──
// Реалізація за ТЗ: модальний попап overlay, split-layout

function AuthModal({ open, onClose, setPage }) {
  const { T } = useContext(ThemeCtx);
  const [step, setStep] = useState('login'); // login | register | confirm | role
  const [form, setForm] = useState({
    login:'', pass:'', remember:false,
    name:'', surname:'', phone:'', email:'', pass1:'', pass2:'', policy:false,
  });
  const [role, setRole] = useState('client');
  const [code, setCode] = useState(['','','','','','']);
  const [timer, setTimer] = useState(60);
  const codeRefs = Array.from({length:6}, () => React.useRef(null));
  const upd = (k,v) => setForm(f => ({...f,[k]:v}));

  useEffect(() => { if(open){ setStep('login'); } }, [open]);
  useEffect(() => {
    if(step!=='confirm') return;
    setTimer(60);
    const t = setInterval(()=>setTimer(s=>s>0?s-1:0), 1000);
    return ()=>clearInterval(t);
  }, [step]);

  if(!open) return null;

  const benefits = {
    login: ['Перегляд історії замовлень','Бонуси та персональні знижки','Швидке повторне замовлення','Особистий менеджер'],
    register: ['Лояльні ціни для постійних клієнтів','Накопичувальна система кешбеку','Доступ до закритих акцій','Документообіг онлайн'],
  };

  const handleCode = (i,v) => {
    const next=[...code]; next[i]=v.slice(-1); setCode(next);
    if(v && i<5) codeRefs[i+1].current?.focus();
  };

  const ROLES = [
    { key:'client', title:'Стати клієнтом', feats:['Без заключення договору','Перегляд історії замовлень','Підключення до системи бонусів та знижок'] },
    { key:'business', title:'Стати бізнес-клієнтом', feats:['Заключення договору','Закупівля продукції гуртом','Дистриб’юторські умови','Додавання співробітників','Персональний менеджер','Навчання та техпідтримка'] },
    { key:'partner', title:'Стати бізнес-партнером', feats:['Заключення договору','Гуртові закупівлі','Дропшипінг','Співробітники в кабінеті','Персональний менеджер','Навчання та техпідтримка'] },
    { key:'undecided', title:'Я ще не визначився', feats:['Замовити консультацію','Менеджер допоможе обрати','Без зобов’язань'] },
  ];

  // Wide modal for role step
  const isWide = step === 'role';
  const isSplit = step === 'login' || step === 'register';

  return (
    <div onClick={e=>{ if(e.target===e.currentTarget) onClose(); }}
      style={{ position:'fixed', inset:0, background:'rgba(0,0,0,0.55)',
        backdropFilter:'blur(4px)', zIndex:9000, display:'flex',
        alignItems:'center', justifyContent:'center', padding:40,
        animation:'cmdkFade 0.15s ease' }}>
      <div style={{ background:T.surface, border:`1px solid ${T.border}`,
        borderRadius:T.rLg, width: isWide?920:isSplit?760:420, maxWidth:'95vw',
        maxHeight:'90vh', overflow:'auto', boxShadow:'0 24px 64px rgba(0,0,0,0.4)',
        animation:'cmdkSlide 0.2s cubic-bezier(0.16,1,0.3,1)',
        display: isSplit?'grid':'block', gridTemplateColumns: isSplit?'1fr 1fr':'none' }}>

        {/* ── LEFT: form ── */}
        <div style={{ padding:40, position:'relative' }}>
          {/* Close */}
          <button onClick={onClose} style={{ position:'absolute', top:16, right:16,
            background:'none', border:'none', cursor:'pointer', color:T.text3, padding:4 }}>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>

          {/* LOGIN */}
          {step==='login' && (
            <div style={{ display:'flex', flexDirection:'column', gap:16 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Вхід до особистого кабінету</h2>
              <Field label="Електронна пошта або телефон +380" placeholder="email@example.com або +380..."
                value={form.login} onChange={e=>upd('login',e.target.value)}/>
              <Field label="Пароль" type="password" placeholder="••••••••"
                value={form.pass} onChange={e=>upd('pass',e.target.value)}/>
              <div style={{ textAlign:'right', marginTop:-6 }}>
                <span style={{ fontSize:12, color:T.text2, cursor:'pointer',
                  textDecoration:'underline', textUnderlineOffset:2 }}>Забули пароль?</span>
              </div>
              <div style={{ display:'flex', alignItems:'center', gap:12 }}>
                <Btn onClick={()=>{ onClose(); setPage('client-dashboard'); }}>Увійти</Btn>
                <label onClick={()=>upd('remember',!form.remember)}
                  style={{ display:'flex', alignItems:'center', gap:8, cursor:'pointer', fontSize:13, color:T.text2 }}>
                  <div style={{ width:16, height:16, border:`1.5px solid ${form.remember?T.invBg:T.border2}`,
                    borderRadius:4, background:form.remember?T.invBg:'transparent', flexShrink:0,
                    display:'flex', alignItems:'center', justifyContent:'center' }}>
                    {form.remember && <svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/></svg>}
                  </div>
                  Запам'ятати мене
                </label>
              </div>

              {/* Divider */}
              <div style={{ display:'flex', alignItems:'center', gap:12, margin:'4px 0' }}>
                <div style={{ flex:1, height:1, background:T.border }}/>
                <span style={{ fontSize:11, color:T.text3 }}>Або увійдіть як користувач</span>
                <div style={{ flex:1, height:1, background:T.border }}/>
              </div>
              <div style={{ display:'flex', gap:10 }}>
                <Btn variant="outline" full><span style={{marginRight:6}}>G</span> Google</Btn>
                <Btn variant="outline" full><span style={{marginRight:6}}>f</span> Facebook</Btn>
              </div>

              <div style={{ textAlign:'center', fontSize:13, color:T.text2, marginTop:4 }}>
                Немає профілю?{' '}
                <span onClick={()=>setStep('register')} style={{ color:T.text, fontWeight:600,
                  cursor:'pointer', textDecoration:'underline', textUnderlineOffset:2 }}>ЗАРЕЄСТРУВАТИСЬ</span>
              </div>
            </div>
          )}

          {/* REGISTER */}
          {step==='register' && (
            <div style={{ display:'flex', flexDirection:'column', gap:14 }}>
              <h2 style={{ fontSize:22, fontWeight:700, color:T.text, letterSpacing:'-0.025em',
                fontFamily:'Geist, Inter, sans-serif' }}>Створити профіль</h2>
              <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:12 }}>
                <Field label="Ім'я *" placeholder="Іван" value={form.name} onChange={e=>upd('name',e.target.value)}/>
                <Field label="Прізвище *" placeholder="Іваненко" value={form.surname} onChange={e=>upd('surname',e.target.value)}/>
              </div>
              <Field label="Номер телефону *" placeholder="+380 XX XXX XX XX" value={form.phone} onChange={e=>upd('phone',e.target.value)}/>
              <Field label="Електронна пошта *" type="email" placeholder="email@example.com" value={form.email} onChange={e=>upd('email',e.target.value)}/>
              <div style={{ display:'grid', gridTemplateColumns:'1fr 1fr', gap:12 }}>
                <Field label="Пароль *" type="password" placeholder="••••••••" value={form.pass1} onChange={e=>upd('pass1',e.target.value)}/>
                <Field label="Повторити пароль *" type="password" placeholder="••••••••" value={form.pass2} onChange={e=>upd('pass2',e.target.value)}/>
              </div>
              <label onClick={()=>upd('policy',!form.policy)}
                style={{ display:'flex', alignItems:'center', gap:10, cursor:'pointer', fontSize:12, color:T.text2 }}>
                <div style={{ width:16, height:16, border:`1.5px solid ${form.policy?T.invBg:T.border2}`,
                  borderRadius:4, background:form.policy?T.invBg:'transparent', flexShrink:0,
                  display:'flex', alignItems:'center', justifyContent:'center' }}>
                  {form.policy && <svg width="9" height="7" viewBox="0 0 9 7" fill="none"><path d="M1 3.5L3.5 6L8 1" stroke={T.invText} strokeWidth="1.5" strokeLinecap="round"/></svg>}
                </div>
                Погоджуюсь з <span style={{ color:T.text, textDecoration:'underline' }}>Політикою конфіденційності</span>
              </label>
              <Btn full disabled={!form.policy} onClick={()=>setStep('confirm')}>Зареєструватись</Btn>
              <div style={{ textAlign:'center', fontSize:13, color:T.text2 }}>
                <span onClick={()=>setStep('login')} style={{ color:T.text, cursor:'pointer',
                  textDecoration:'underline', textUnderlineOffset:2 }}>Увійти</span>
                {' | '}
                <span style={{ cursor:'pointer', textDecoration:'underline', textUnderlineOffset:2 }}>Забули пароль?</span>
              </div>
            </div>
          )}

          {/* CONFIRM */}
          {step==='confirm' && (
            <div style={{ textAlign:'center', padding:'12px 0' }}>
              <div style={{ width:56, height:56, background:T.bg==='#09090b'?'#172554':'#eff6ff',
                border:`1px solid ${T.bg==='#09090b'?'#1e3a8a':'#bfdbfe'}`, borderRadius:T.rLg,
                display:'flex', alignItems:'center', justifyContent:'center', margin:'0 auto 20px' }}>
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke={T.bg==='#09090b'?'#60a5fa':'#2563eb'} strokeWidth="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
              </div>
              <h2 style={{ fontSize:20, fontWeight:700, color:T.text, marginBottom:8, letterSpacing:'-0.02em',
                fontFamily:'Geist, Inter, sans-serif' }}>Підтвердіть реєстрацію</h2>
              <p style={{ fontSize:13, color:T.text2, lineHeight:1.6, marginBottom:6 }}>
                Ми надіслали лист з підтвердженням на адресу
              </p>
              <p style={{ fontSize:13, fontWeight:600, color:T.text, marginBottom:24 }}>
                {form.email || 'your@email.com'}
              </p>
              <div style={{ display:'flex', gap:8, justifyContent:'center', marginBottom:18 }}>
                {code.map((c,i)=>(
                  <input key={i} ref={codeRefs[i]} value={c} onChange={e=>handleCode(i,e.target.value)}
                    onKeyDown={e=>{if(e.key==='Backspace'&&!c&&i>0)codeRefs[i-1].current?.focus();}}
                    maxLength={1} inputMode="numeric"
                    style={{ width:42, height:48, textAlign:'center', fontSize:20, fontWeight:700, color:T.text,
                      background:T.bgAlt, border:`1.5px solid ${c?T.invBg:T.border}`, borderRadius:T.rSm,
                      outline:'none', fontFamily:'Geist, Inter, sans-serif' }}/>
                ))}
              </div>
              <Btn full disabled={!code.every(c=>c)} onClick={()=>setStep('role')}>Активувати акаунт</Btn>
              <div style={{ marginTop:16, fontSize:12, color:T.text2 }}>
                {timer>0 ? <span>Не прийшло? Надіслати ще раз через <strong style={{color:T.text}}>{timer} сек</strong></span>
                  : <span onClick={()=>setTimer(60)} style={{color:T.text, cursor:'pointer', textDecoration:'underline'}}>Надіслати ще раз</span>}
              </div>
              <div style={{ marginTop:8, fontSize:12, color:T.text2 }}>
                або <span style={{ color:T.text, cursor:'pointer', textDecoration:'underline' }}>підтвердження по SMS</span>
              </div>
            </div>
          )}

          {/* ROLE — rendered full-width below */}
          {step==='role' && (
            <div>
              <div style={{ textAlign:'center', marginBottom:8 }}>
                <div style={{ width:48, height:48, background:T.bg==='#09090b'?'#052e16':'#f0fdf4',
                  border:`1px solid ${T.bg==='#09090b'?'#14532d':'#bbf7d0'}`, borderRadius:T.rLg,
                  display:'flex', alignItems:'center', justifyContent:'center', margin:'0 auto 16px' }}>
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h2 style={{ fontSize:22, fontWeight:700, color:T.text, marginBottom:6, letterSpacing:'-0.025em',
                  fontFamily:'Geist, Inter, sans-serif' }}>Дякуємо, ваш акаунт активовано!</h2>
                <p style={{ fontSize:14, color:T.text2 }}>Оберіть, як ви плануєте користуватися платформою</p>
                {/* Steps 1-2-3 */}
                <div style={{ display:'flex', gap:6, justifyContent:'center', marginTop:16 }}>
                  {[1,2,3].map(n=>(
                    <div key={n} style={{ display:'flex', alignItems:'center', gap:6 }}>
                      <div style={{ width:24, height:24, borderRadius:'50%',
                        background: n===1?T.invBg:'transparent', color:n===1?T.invText:T.text3,
                        border:`1px solid ${n===1?T.invBg:T.border}`, fontSize:12, fontWeight:600,
                        display:'flex', alignItems:'center', justifyContent:'center' }}>{n}</div>
                      {n<3 && <div style={{ width:24, height:1, background:T.border }}/>}
                    </div>
                  ))}
                </div>
              </div>
              <div style={{ display:'grid', gridTemplateColumns:'repeat(4,1fr)', gap:12, margin:'24px 0' }}>
                {ROLES.map(r=>(
                  <div key={r.key} onClick={()=>setRole(r.key)}
                    style={{ background:T.surface, border:`2px solid ${role===r.key?T.invBg:T.border}`,
                      borderRadius:T.rLg, padding:18, cursor:'pointer', transition:'all 0.15s',
                      boxShadow: role===r.key?T.shadowMd:T.shadow }}>
                    <div style={{ display:'flex', justifyContent:'space-between', alignItems:'flex-start', marginBottom:10 }}>
                      <div style={{ fontSize:14, fontWeight:700, color:T.text, lineHeight:1.3,
                        fontFamily:'Geist, Inter, sans-serif' }}>{r.title}</div>
                      <div style={{ width:18, height:18, border:`2px solid ${role===r.key?T.invBg:T.border2}`,
                        borderRadius:'50%', flexShrink:0, display:'flex', alignItems:'center', justifyContent:'center' }}>
                        {role===r.key && <div style={{ width:8, height:8, borderRadius:'50%', background:T.invBg }}/>}
                      </div>
                    </div>
                    <div style={{ display:'flex', flexDirection:'column', gap:6 }}>
                      {r.feats.map(f=>(
                        <div key={f} style={{ display:'flex', gap:6, alignItems:'flex-start' }}>
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke={role===r.key?T.text:T.text3} strokeWidth="2" style={{marginTop:3,flexShrink:0}}><polyline points="20 6 9 17 4 12"/></svg>
                          <span style={{ fontSize:11, color:T.text2, lineHeight:1.4 }}>{f}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
              <div style={{ display:'flex', justifyContent:'space-between', alignItems:'center' }}>
                <span onClick={()=>setStep('confirm')} style={{ fontSize:13, color:T.text2, cursor:'pointer' }}>← Назад</span>
                <Btn size="lg" onClick={()=>{ onClose(); setPage(role==='client'||role==='undecided'?'client-dashboard':'ext-dashboard'); }}>
                  Подати заявку
                </Btn>
              </div>
            </div>
          )}
        </div>

        {/* ── RIGHT: benefits (тільки для login/register) ── */}
        {isSplit && (
          <div style={{ background:T.bgAlt, borderLeft:`1px solid ${T.border}`, padding:40,
            display:'flex', flexDirection:'column', justifyContent:'center', gap:18 }}>
            <div style={{ width:48, height:48, background:T.invBg, borderRadius:T.rLg,
              display:'flex', alignItems:'center', justifyContent:'center' }}>
              <span style={{ fontSize:16, fontWeight:700, color:T.invText }}>AG</span>
            </div>
            <div style={{ fontSize:18, fontWeight:700, color:T.text, letterSpacing:'-0.02em',
              fontFamily:'Geist, Inter, sans-serif', lineHeight:1.3 }}>
              {step==='login' ? 'Раді бачити вас знову' : 'Переваги реєстрації'}
            </div>
            <div style={{ fontSize:13, color:T.text2, lineHeight:1.6 }}>
              {step==='login'
                ? 'Увійдіть, щоб керувати замовленнями, відстежувати доставку та користуватись бонусами.'
                : 'Створіть профіль та отримайте доступ до спеціальних умов для постійних клієнтів.'}
            </div>
            <div style={{ display:'flex', flexDirection:'column', gap:12, marginTop:4 }}>
              {benefits[step].map(b=>(
                <div key={b} style={{ display:'flex', gap:10, alignItems:'flex-start' }}>
                  <div style={{ width:20, height:20, borderRadius:'50%', background:T.surface,
                    border:`1px solid ${T.border}`, flexShrink:0, display:'flex',
                    alignItems:'center', justifyContent:'center' }}>
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke={T.text} strokeWidth="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                  <span style={{ fontSize:13, color:T.text, lineHeight:1.4 }}>{b}</span>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

Object.assign(window, { AuthModal });
