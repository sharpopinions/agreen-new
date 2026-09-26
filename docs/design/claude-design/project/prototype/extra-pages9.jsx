// ── EXTRA PAGES 9 ─────────────────────────────────────────────────
// Blog-Event (деталь події з реєстрацією)
// Blog-News / Blog-Events / Blog-About-us — pre-filtered Blog states

// ── BLOG EVENT (деталь події з формою реєстрації) ────────────────
function BlogEventPage({ setPage }) {
  const { T } = useContext(ThemeCtx);
  const [form, setForm] = useState({ name:'', phone:'', email:'', company:'' });
  const [sent, setSent] = useState(false);
  const upd = (k,v) => setForm(f => ({...f, [k]:v}));

  return (
    <div style={{ background: T.bg, padding: '28px 60px' }}>
      <Crumbs items={['Блог', 'Воркшоп: Сучасні технології фарбування']} setPage={setPage}/>

      <h1 style={{ fontSize: 30, fontWeight: 700, color: T.text,
        letterSpacing: '-0.03em', marginBottom: 24, textAlign: 'center',
        fontFamily: 'Geist, Inter, sans-serif' }}>
        Воркшоп: Сучасні технології фарбування 2026
      </h1>

      {/* Banner */}
      <div style={{ borderRadius: T.rLg, overflow: 'hidden', marginBottom: 32, position: 'relative' }}>
        <Img h={400} label="Подія: воркшоп для майстрів"/>
        <div style={{ position:'absolute', top:24, left:24, padding:'6px 14px',
          background:'rgba(255,255,255,0.9)', backdropFilter:'blur(8px)',
          borderRadius: T.rPill, fontSize: 12, fontWeight:600, color:'#000' }}>
          🎟️ Подія
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 360px', gap: 40, marginBottom: 60 }}>
        {/* Article body */}
        <div>
          {/* Meta */}
          <div style={{ display: 'flex', gap: 14, marginBottom: 24, paddingBottom: 20,
            borderBottom: `1px solid ${T.border}`, fontSize: 13, color: T.text2 }}>
            <span><strong style={{ color: T.text }}>📅 12 червня 2026</strong></span>
            <span style={{ color: T.border2 }}>·</span>
            <span><strong style={{ color: T.text }}>🕐 10:00 – 17:00</strong></span>
            <span style={{ color: T.border2 }}>·</span>
            <span><strong style={{ color: T.text }}>📍 Київ</strong></span>
          </div>

          <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text,
            letterSpacing: '-0.01em', marginBottom: 14 }}>Про подію</h2>
          {[
            `Запрошуємо професіоналів галузі малярно-кузовного ремонту на щорічний воркшоп
             A-green, який цього року присвячений новим технологіям лакофарбових покриттів
             та підготовці поверхонь.`,
            `Воркшоп охопить найактуальніші теми галузі: впровадження водорозчинних
             технологій, нові засоби абразивної підготовки, оптимізація процесів полірування,
             вибір засобів захисту персоналу.`,
            `Участь у воркшопі — це можливість поспілкуватися з провідними експертами галузі,
             отримати практичні навички та сертифікат після завершення курсу.`,
          ].map((t, i) => (
            <p key={i} style={{ fontSize: 15, color: T.text2, lineHeight: 1.8, marginBottom: 16 }}>{t}</p>
          ))}

          {/* Program */}
          <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text,
            letterSpacing: '-0.01em', marginTop: 32, marginBottom: 16 }}>Програма</h2>
          <div style={{ background: T.surface, border:`1px solid ${T.border}`,
            borderRadius: T.rLg, overflow:'hidden' }}>
            {[
              ['10:00 – 11:00', 'Реєстрація. Кава-брейк'],
              ['11:00 – 12:30', 'Нові технології Cromax 2026'],
              ['12:30 – 13:30', 'Обід'],
              ['13:30 – 15:00', 'Практикум: абразивна підготовка'],
              ['15:00 – 15:30', 'Перерва'],
              ['15:30 – 17:00', 'Q&A. Сертифікація'],
            ].map(([time, topic], i, arr) => (
              <div key={time} style={{ padding:'14px 20px',
                borderBottom: i < arr.length-1 ? `1px solid ${T.border}` : 'none',
                display:'grid', gridTemplateColumns:'130px 1fr', gap:20, alignItems:'center' }}>
                <span style={{ fontSize: 13, fontWeight: 600, color: T.text,
                  fontFamily: 'Geist, Inter, sans-serif' }}>{time}</span>
                <span style={{ fontSize: 14, color: T.text2 }}>{topic}</span>
              </div>
            ))}
          </div>

          {/* Speakers */}
          <h2 style={{ fontSize: 18, fontWeight: 600, color: T.text,
            letterSpacing: '-0.01em', marginTop: 32, marginBottom: 16 }}>Спікери</h2>
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 12 }}>
            {[
              { name: 'Олександр Петренко', role: 'Технічний директор A-green' },
              { name: 'Марина Коваль',      role: 'Представник Axalta Cromax' },
              { name: 'Ігор Сидоренко',     role: 'Майстер 3M Certified' },
            ].map(s => (
              <div key={s.name} style={{ background: T.surface, border:`1px solid ${T.border}`,
                borderRadius: T.rLg, padding: 16, textAlign:'center' }}>
                <div style={{ width: 56, height: 56, borderRadius: '50%', background: T.muted,
                  margin: '0 auto 10px', display: 'flex', alignItems:'center',
                  justifyContent:'center', fontSize: 22, color: T.text2 }}>👨</div>
                <div style={{ fontSize: 13, fontWeight: 600, color: T.text, marginBottom: 2 }}>{s.name}</div>
                <div style={{ fontSize: 11, color: T.text3, lineHeight: 1.5 }}>{s.role}</div>
              </div>
            ))}
          </div>
        </div>

        {/* Registration form */}
        <div style={{ position: 'sticky', top: 100, height: 'fit-content' }}>
          <div style={{ background: T.surface, border: `1px solid ${T.border}`,
            borderRadius: T.rLg, padding: 28, boxShadow: T.shadow }}>
            <div style={{ marginBottom: 20, paddingBottom: 16, borderBottom: `1px solid ${T.border}` }}>
              <div style={{ fontSize: 13, color: T.text3, marginBottom: 4 }}>Участь</div>
              <div style={{ fontSize: 28, fontWeight: 800, color: T.text,
                letterSpacing: '-0.03em', lineHeight: 1,
                fontFamily: 'Geist, Inter, sans-serif' }}>Безкоштовно</div>
              <div style={{ fontSize: 12, color: T.text2, marginTop: 6 }}>
                Реєстрація обов'язкова. Місць обмежено: 30
              </div>
            </div>

            {sent ? (
              <div style={{ textAlign: 'center', padding: '20px 0' }}>
                <div style={{ fontSize: 40, marginBottom: 16 }}>🎉</div>
                <h3 style={{ fontSize: 16, fontWeight: 600, color: T.text, marginBottom: 8 }}>
                  Реєстрацію підтверджено!
                </h3>
                <p style={{ fontSize: 13, color: T.text2, lineHeight: 1.6 }}>
                  Ми надішлемо вам деталі та нагадування на email напередодні події.
                </p>
              </div>
            ) : (
              <>
                <h3 style={{ fontSize: 14, fontWeight: 600, color: T.text, marginBottom: 14 }}>
                  Реєстрація на подію
                </h3>
                <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                  <Field label="Ім'я *" placeholder="Іван Іваненко"
                    value={form.name} onChange={e => upd('name', e.target.value)}/>
                  <Field label="Телефон *" placeholder="+380 XX XXX XX XX"
                    value={form.phone} onChange={e => upd('phone', e.target.value)}/>
                  <Field label="Email *" placeholder="email@gmail.com" type="email"
                    value={form.email} onChange={e => upd('email', e.target.value)}/>
                  <Field label="Компанія" placeholder="Назва компанії"
                    value={form.company} onChange={e => upd('company', e.target.value)}/>
                  <Btn full onClick={() => setSent(true)}>Зареєструватись</Btn>
                  <div style={{ fontSize: 11, color: T.text3, lineHeight: 1.6, marginTop: 4 }}>
                    Реєструючись, ви погоджуєтесь з{' '}
                    <span onClick={()=>setPage('privacy')} style={{ color: T.text2, cursor: 'pointer',
                      textDecoration: 'underline', textUnderlineOffset: 2 }}>
                      політикою конфіденційності
                    </span>
                  </div>
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}

// ── BLOG WITH PRE-FILTERED CATEGORY ──────────────────────────────
// Reuses BlogPage logic with default category — for /blog-news, /blog-events, /blog-about
function BlogFilteredPage({ setPage, defaultCat }) {
  const { T } = useContext(ThemeCtx);
  // We reuse existing BlogPage but pre-select category
  return (
    <div style={{ background: T.bg }}>
      <div style={{ padding: '28px 60px 0' }}>
        <Crumbs items={['Блог', defaultCat]} setPage={setPage}/>
      </div>
      <BlogPage setPage={setPage} initialCategory={defaultCat}/>
    </div>
  );
}

Object.assign(window, { BlogEventPage, BlogFilteredPage });
